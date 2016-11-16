<?php
    namespace SatellysReborn\Controleurs;
    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur des pages relatives au compte d'un utilisateur.
     * @package SatellysReborn\Controleurs
     */
    class Compte extends Controleur {

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

                // Récupère l'utilisateur courant.
                $utilCourant = Utilisateur::getUtilisateur();

                // Modifie les données.
                $newUtil = new Utilisateur($utilCourant->getLogin(),
                                           $_POST['mdp'],
                                           $_POST['email'],
                                           $utilCourant->getEnseignant(),
                                           $utilCourant->getAdministratif());

                // Met à jour les données.
                $res = DAO_Factory::getDAO_Utilisateur()->update($newUtil);

                // Si OK ?
                if ($res) {

                    // Modifie l'utilisateur.
                    Utilisateur::setUtilisateur($newUtil);
                    $this->vue = new Vue($this, 'ModifierOK');

                } else {
                    $this->vue = new Vue($this, 'ModifierNOK');
                }
            } else {
                self::redirect('compte/errNonConnecte');
            }
            $this->vue->render();
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
    }