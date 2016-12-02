<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Administratif;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;
    use SatellysReborn\Modeles\Population\Enseignant;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Modeles\Utils\Utils;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les utilisateurs.
     * @package SatellysReborn\Controleurs
     */
    class CompteControleur extends Controleur {

        /**
         * Affiche les informations d'un utilisateur.
         */
        public function index() {
            // Est connecté ?
            if (Utilisateur::estConnecte()) {
                $this->vue = new Vue($this, 'MonCompte', 'Mon Compte');
                $this->vue->render();
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Effectue une connexion à l'application
         * si les identifiants renseignés existent.
         */
        public function connexion() {
            // Non connecté.
            if (Utilisateur::estConnecte() && isset($_POST)) {
                self::redirect('/SatellysReborn/compte/errDejaConnecte/');
            }

            // Récupère les données.
            $util = DAO_Factory::getDAO_Utilisateur()
                               ->findLoginMdp($_POST['login'],
                                              Utilisateur::crypterMdp(
                                                  $_POST['mdp']));

            // Utilisateur existe ?
            if (isset($util)) {

                // Sauvegarde l'utilisateur en session.
                Utilisateur::setUtilisateur($util);

                // Vers la page d'accueil mais connecté.
                self::redirect('/SatellysReborn/');
            } else {
                self::redirect('/SatellysReborn/compte/errIdInvalides');
            }
            $this->vue->render();
        }

        /**
         * Déconnecte un utilisateur de l'application.
         */
        public function deconnexion() {
            // Est connecté ?
            if (Utilisateur::estConnecte()) {
                session_destroy();
                self::redirect('/SatellysReborn/');
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte');
            }
        }

        /**
         * Modifie les informations du compte d'un utilisateur.
         */
        public function modifier() {
            // Est connecté ?
            if (Utilisateur::estConnecte() && isset($_POST)) {

                if (Utilisateur::utilCourantEstSuperAdmin()) {
                    // Récupère l'utilisateur courant.
                    $utilCourant = Utilisateur::getUtilisateur();
                    $newUtil = new Utilisateur($utilCourant->getLogin(),
                                               isset($_POST['mdp']) ?
                                                   Utilisateur::crypterMdp($_POST['mdp']) :
                                                   $utilCourant->getMdp(),
                                               $_POST['email'],
                                               null,
                                               null);

                    // Résultat.
                    if (DAO_Factory::getDAO_Utilisateur()->update($newUtil)) {
                        // Met à jour la session.
                        Utilisateur::setUtilisateur($newUtil);

                        $this->vue =
                            new Vue($this, "ModifOK", "Profil modifié");
                        $this->vue->render();
                    } else {
                        self::redirect('/SatellysReborn/compte/errModification/');
                    }
                } else if (Utilisateur::utilCourantEstAdministratif()) {
                    $this->modifierAdministratif();
                } else {
                    $this->modifierEnseignant();
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Modifie le profil d'un utilisateur administratif et affiche le
         * résultat.
         */
        private function modifierAdministratif() {
            // Récupère l'utilisateur courant.
            $utilCourant = Utilisateur::getUtilisateur();

            // Le nouvel administratif.
            $newAdresse = new Adresse($utilCourant->getAdministratif()
                                                  ->getAdresse()
                                                  ->getId(),
                                      $_POST['adresse1'],
                                      $_POST['adresse2'],
                                      $_POST['adresse3'],
                                      DAO_Factory::getDAO_Ville()
                                                 ->find($_POST['ville']));
            $newAdmin =
                new Administratif($utilCourant->getAdministratif()->getId(),
                                  $_POST['nom'], $_POST['prenom'],
                                  $_POST['tel'],
                                  $_POST['email'],
                                  $_POST['poste'], $newAdresse);
            $newUtil = new Utilisateur($utilCourant->getLogin(),
                                       isset($_POST['mdp']) ?
                                           Utilisateur::crypterMdp($_POST['mdp']) :
                                           $utilCourant->getMdp(), null, null,
                                       $newAdmin);

            // Met à jour.
            $res = DAO_Factory::getDAO_Adresse()->update($newAdresse)
                   && DAO_Factory::getDAO_Administratif()->update($newAdmin)
                   && DAO_Factory::getDAO_Utilisateur()->update($newUtil);

            // Résultat.
            if ($res) {
                // Met à jour la session.
                Utilisateur::setUtilisateur($newUtil);

                $this->vue = new Vue($this, "ModifOK", "Profil modifié");
                $this->vue->render();
            } else {
                self::redirect('/SatellysReborn/compte/errModification/');
            }
        }

        /**
         * Modifie le profil d'un utilisateur administratif et affiche le
         * résultat.
         */
        private function modifierEnseignant() {
            // Récupère l'utilisateur courant.
            $utilCourant = Utilisateur::getUtilisateur();

            // Le nouvel enseignant.
            $newAdresse = new Adresse($utilCourant->getAdministratif()
                                                  ->getAdresse()
                                                  ->getId(),
                                      $_POST['adresse1'],
                                      $_POST['adresse2'],
                                      $_POST['adresse3'],
                                      DAO_Factory::getDAO_Ville()
                                                 ->find($_POST['ville']));
            $newEns =
                new Enseignant($utilCourant->getAdministratif()->getId(),
                               $_POST['nom'], $_POST['prenom'],
                               $_POST['tel'],
                               $_POST['email'], $newAdresse);
            $newUtil = new Utilisateur($utilCourant->getLogin(),
                                       isset($_POST['mdp']) ?
                                           Utilisateur::crypterMdp($_POST['mdp']) :
                                           $utilCourant->getMdp(), null,
                                       $newEns,
                                       null);

            // Met à jour.
            $res = DAO_Factory::getDAO_Adresse()->update($newAdresse)
                   && DAO_Factory::getDAO_Enseignant()->update($newEns)
                   && DAO_Factory::getDAO_Utilisateur()->update($newUtil);

            // Résultat.
            if ($res) {
                // Met à jour la session.
                Utilisateur::setUtilisateur($newUtil);

                $this->vue = new Vue($this, "ModifOK", "Profil modifié");
                $this->vue->render();
            } else {
                self::redirect('/SatellysReborn/compte/errModification/');
            }
        }

        /**
         * Affiche la page quand un utilisateur a perdu son mot de passe.
         */
        public function mdpOublie() {
            $this->vue = new Vue($this, 'MdpOublie', "Mot de passe oublié");
            $this->vue->render();
        }

        /**
         * Reset le mot de passe d'un utilisateur en changeant son mot de passe
         * et en lui envoyant par mail.
         */
        public function resetMdp() {
            if (isset($_POST['login'])) {
                $util = DAO_Factory::getDAO_Utilisateur()->find($_POST['login']);

                // L'utilisateur n'existe pas.
                if ($util == false) {
                    self::redirect('/SatellysReborn/compte/inconnu/');
                }

                // Nouveau mot de passe.
                $newMdp = Utils::genererChaine();

                if (DAO_Factory::getDAO_Utilisateur()
                               ->update(new Utilisateur($util->getLogin(),
                                                        Utilisateur::crypterMdp($newMdp),
                                                        $util->getEmail(),
                                                        $util->getEnseignant(),
                                                        $util->getAdministratif()))
                ) {
                    $mail = "<!DOCTYPE html><body>";
                    $mail .= "<h1>Bonjour, " . $util->getLogin() . '</h1>';
                    $mail .= "<p>Votre nouveau mot de passe est : <span>$newMdp</span></p>";
                    $mail .= "<br /><br /><br /><br />SatellysReborn.fr</body>";

                    if (Utils::envoyerMail($util->getEmail(),
                                           "Nouveau mot de passe.", $mail)
                    ) {
                        $this->vue = new Vue($this, 'MdpReset', "Mot de passe réinitialisé");
                        $this->vue->render();
                    } else {
                        self::redirect('/SatellysReborn/mail/errEnvoie/');
                    }
                } else {
                    self::redirect('/SatellysReborn/compte/errModification/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/inconnu/');
            }
        }

        /**
         * Récupère les utilisateurs dont le nom correspond à celui passé en
         * argument et les affiche au format JSON.
         * @param $arg string l'argument de recherche.
         */
        public function listeUtilisateursJSON($arg) {
            echo json_encode(DAO_Factory::getDAO_Utilisateur()->findNom($arg));
        }

        /**
         * Affiche la page d'erreur quand un utilisateur essaie d'accéder à
         * une page sans être connecté.
         */
        public function errNonConnecte() {
            $this->vue = new Vue($this, 'ErrNonConnecte', 'Connexion Requise');
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur quand un utilisateur essaie de se connecter
         * alors qu'il est déjà connecté.
         */
        public function errDejaConnecte() {
            $this->vue = new Vue($this, 'ErrDejaConnecte', 'Déjà connecté');
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur quand un utilisateur essaie de connecter
         * mais que ses identifiants sont invalides.
         */
        public function errIdInvalides() {
            $this->vue = new Vue($this, 'ErrIdInvalides',
                                 'Identifiants Invalides');
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur quand un utilisateur essaie d'accèder à une
         * page reservé à un administratif alors qu'il ne l'est pas.
         */
        public function errNonAdministratif() {
            $this->vue = new Vue($this, 'ErrNonAdministratif',
                                 'Vous n\'êtes pas administratif');
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur quand une erreur est survenu dans la
         * modification du profil de l'utilisateur.
         */
        public function errModification() {
            $this->vue = new Vue($this, 'ErrModif',
                                 'Erreur dans la modification de votre compte');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand un utilisateur est inconnu.
         */
        public function inconnu() {
            $this->vue = new Vue($this, 'Inconnu',
                                 'Utilisateur Inconnu');
            $this->vue->render();
        }
    }