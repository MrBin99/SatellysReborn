<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;
    use SatellysReborn\Modeles\Population\Enseignant;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Modeles\Utils\Utils;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les enseignants.
     * @package SatellysReborn\Controleurs
     */
    class EnseignantControleur extends Controleur {

        /**
         * Affiche la liste des enseignants.
         */
        public function index() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()) {

                // Tous les enseignants.
                $enseignants = DAO_Factory::getDAO_Enseignant()->findAll();

                // Affichage.
                $this->vue = new Vue($this, "Liste", "Liste des enseignants");
                $this->vue->render($enseignants);

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la page de creation d'un enseignant.
         */
        public function creation() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()) {

                $this->vue = new Vue($this, 'Creation', "Créer un enseignant");
                $this->vue->render();

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Créé un nouvel enseignant dans la base de données.
         */
        public function creer() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($_POST['id']) && isset($_POST['nom']) &&
                    isset($_POST['prenom']) &&
                    isset($_POST['tel']) && isset($_POST['email']) &&
                    isset($_POST['adresse1']) && isset($_POST['adresse2']) &&
                    isset($_POST['adresse3']) && isset($_POST['ville'])
                ) {

                    // Insère.
                    $adresse =
                        DAO_Factory::getDAO_Adresse()
                                   ->insert(new Adresse(null,
                                                        $_POST['adresse1'],
                                                        $_POST['adresse2'],
                                                        $_POST['adresse3'],
                                                        DAO_Factory::getDAO_Ville()
                                                                   ->find($_POST['ville'])));

                    $enseignant = DAO_Factory::getDAO_Enseignant()
                                             ->insert(new Enseignant($_POST['id'],
                                                                     strtoupper($_POST['nom']),
                                                                     ucfirst(strtolower($_POST['prenom'])),
                                                                     $_POST['tel'],
                                                                     $adresse));

                    if ($adresse != false && $enseignant != false &&
                        $this->creerUtilisateur($enseignant)
                    ) {
                        self::redirect('/SatellysReborn/enseignant/details/' .
                                       $_POST['id']);
                    } else {
                        self::redirect('/SatellysReborn/enseignant/errCreer/');
                    }
                } else {
                    self::redirect('/SatellysReborn/enseignant/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Insère l'utilisateur correspondant à un enseignant dans la base de
         * données.
         * @param Enseignant $enseignant l'enseignant correspond à
         *     l'utilisateur.
         * @return bool si l'insertion a bien eu lieu.
         */
        private function creerUtilisateur(Enseignant $enseignant) {
            // Construit le login.
            $login =
                strtolower(Utils::enleverAccents($enseignant->getPrenom()) .
                           "." .
                           Utils::enleverAccents($enseignant->getNom()));

            // Insère.
            return DAO_Factory::getDAO_Utilisateur()
                              ->insert(new Utilisateur($login,
                                                       Utilisateur::crypterMdp($enseignant->getId()),
                                                       $_POST['email'],
                                                       $enseignant, null)) != false;
        }

        /**
         * Affiche les détails d'un enseignant.
         * @param $id string l'identifiant de l'enseignant.
         */
        public function details($id) {
            // Droits.
            if (Utilisateur::estConnecte()) {
                if (isset($id)) {
                    // Récupère l'enseignant.
                    $ens = DAO_Factory::getDAO_Enseignant()->find($id);
                    $util = DAO_Factory::getDAO_Utilisateur()->findUtilisateurEnseignant($id);

                    $this->vue =
                        new Vue($this, 'Details', $ens->getNomComplet());
                    $this->vue->render(array($ens, $util));
                } else {
                    self::redirect('/SatellysReborn/etudiant/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Supprime un enseignant de la base de données s'il n'a plus de cours
         * affecté.
         * @param $id string l'identifiant de l'enseignant.
         */
        public function supprimer($id) {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($id)) {

                    // L'enseignant.
                    $enseignant = DAO_Factory::getDAO_Enseignant()->find($id);

                    if ($enseignant == false) {
                        self::redirect('/SatellysReborn/enseignant/inconnu/');
                    }

                    if (DAO_Factory::getDAO_Cours()
                                   ->findCoursEnseignant($id)
                        != null
                    ) {
                        self::redirect('/SatellysReborn/enseignant/errCours/');
                    }

                    // L'utilisateur.
                    $util = DAO_Factory::getDAO_Utilisateur()
                                       ->findUtilisateurEnseignant($id);

                    if (DAO_Factory::getDAO_Utilisateur()->delete($util) &&
                        DAO_Factory::getDAO_Enseignant()->delete($enseignant)
                    ) {
                        self::redirect('/SatellysReborn/enseignant/');
                    } else {
                        self::redirect('/SatellysReborn/enseignant/errSupprimer/');
                    }
                } else {
                    self::redirect('/SatellysReborn/etudiant/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche l'emploi du temps d'un enseignant.
         * @param $id string l'identifiant de l'enseignant.
         */
        public function emploiTemps($id) {
            if (Utilisateur::utilCourantEstAdmin() ||
                Utilisateur::utilCourantEstSuperAdmin() ||
                (Utilisateur::utilCourantEstEnseignant() &&
                 Utilisateur::getUtilisateur()->getEnseignant()->getId() == $id)
            ) {

                $this->vue = new Vue($this, 'EmploiTemps', 'Emploi du temps');
                $this->vue->render();

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Récupère les cours d'un enseignant et les affiche au format JSON.
         * @param $id string l'identifiant de l'enseignant.
         */
        public function coursJSON($id) {
            echo json_encode(DAO_Factory::getDAO_Cours()
                                        ->findCoursEnseignant($id));
        }

        /**
         * Récupère les enseignants dont le identifiant, le nom ou le prénom
         * est passé en argument et les affiche au format JSON.
         * @param $arg string l'argument de recherche.
         */
        public function listeJSON($arg) {
            echo json_encode(DAO_Factory::getDAO_Enseignant()->findNomId($arg));
        }

        /**
         * Affiche l'erreur quand un enseignant n'existe pas.
         */
        public function inconnu() {
            $this->vue = new Vue($this, 'Inconnu', 'Enseignant Inconnu');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la suppression
         * d'un enseignant.
         */
        public function errSupprimer() {
            $this->vue =
                new Vue($this, 'ErrSupprimer', 'Erreur dans la suppression');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand il est impossible de créer un nouvel
         * enseignant.
         */
        public function errCreer() {
            $this->vue =
                new Vue($this, 'ErrCreer', 'Erreur dans la création');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand il est impossible de supprimer un
         * enseignant qui a des cours.
         */
        public function errCours() {
            $this->vue =
                new Vue($this, 'ErrCours', 'Erreur suppression impossible');
            $this->vue->render();
        }
    }