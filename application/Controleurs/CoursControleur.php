<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Cours\Cours;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les cours.
     * @package SatellysReborn\Controleurs
     */
    class CoursControleur extends Controleur {

        /**
         * Redirige vers "Inconnu".
         */
        public function index() {
            self::redirect('/SatellysReborn/cours/inconnu/');
        }

        /**
         * Affiche la page de creation d'un cours.
         */
        public function creation() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin() ||
                Utilisateur::utilCourantEstSuperAdmin()
            ) {

                $this->vue = new Vue($this, "Creation", "Créer un cours");
                $this->vue->render();

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Créé un nouveau cours dans la base de données.
         */
        public function creer() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()) {
                if (!isset($_POST['date']) || !isset($_POST['debut']) ||
                    !isset($_POST['fin']) || !isset($_POST['prof']) ||
                    !isset($_POST['salle']) || !isset($_POST['matiere'])
                    || !isset($_POST['groupes'])
                ) {
                    self::redirect('/SatellysReborn/cours/inconnu/');
                }

                $matiere =
                    DAO_Factory::getDAO_Matiere()->find($_POST['matiere']);
                $prof = DAO_Factory::getDAO_Enseignant()->find($_POST['prof']);
                $cours = DAO_Factory::getDAO_Cours()
                                    ->insert(new Cours(null,
                                                       $matiere,
                                                       $prof,
                                                       $_POST['salle'],
                                                       $_POST['date'],
                                                       $_POST['debut'],
                                                       $_POST['fin']));


                foreach ($_POST['groupes'] as $groupeID) {
                    $cours->ajouterGroupe(DAO_Factory::getDAO_Groupe()
                                                     ->find($groupeID));
                    DAO_Factory::getDAO_Cours()->ajouterCours($cours->getId(),
                                                              $groupeID);
                }

                if ($cours != false) {
                    self::redirect('/SatellysReborn/cours/details/' .
                                   $cours->getId());
                } else {
                    self::redirect('/SatellysReborn/cours/errCreer/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche les détails d'un cours.
         * @param $id string l'identifiant du cours.
         */
        public function details($id) {
            // Récupère le cours.
            $cours = DAO_Factory::getDAO_Cours()->find($id);

            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
                || (Utilisateur::utilCourantEstEnseignant() &&
                    $cours->getEnseignant()->getId() ==
                    Utilisateur::getUtilisateur()->getEnseignant()->getId())
            ) {

                $absents = DAO_Factory::getDAO_Absence()
                                      ->getAbsencesCours($cours->getId());
                $groupes = DAO_Factory::getDAO_Cours()
                                      ->getGroupes($cours->getId());

                $this->vue = new Vue($this, "Details", "Details du cours");
                $this->vue->render(array($cours, $absents, $groupes));

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Supprime un cours de la base de données.
         * @param $id string l'identifiant du cours.
         */
        public function supprimer($id) {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($id)) {

                    // Récupère le cours.
                    $cours = DAO_Factory::getDAO_Cours()->find($id);

                    if ($cours == false) {
                        self::redirect('/SatellysReborn/cours/inconnu/');
                    }

                    // Récupère les absences.
                    $absences =
                        DAO_Factory::getDAO_Absence()->getAbsencesCours($id);

                    // Si il y a encore des absences.
                    if ($absences != []) {
                        self::redirect('/SatellysReborn/cours/errAbsences/');
                    }

                    // On supprime
                    if (DAO_Factory::getDAO_Cours()->deleteAssiste($id) 
                        && DAO_Factory::getDAO_Cours()->delete($cours)) {
                        self::redirect('/SatellysReborn/');
                    } else {
                        self::redirect('/SatellysReborn/cours/errSupprimer/');
                    }

                } else {
                    self::redirect('/SatellysReborn/cours/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche au format JSON les étudiants correspondant à l'argument de
         * recherche et qui ne sont pas absent à un cours.
         * @param $cours string l'identifiant du cours.
         * @param $arg string l'argument de recherche.
         */
        public function listeNonAbsentJSON($cours, $arg) {
            echo json_encode(DAO_Factory::getDAO_Etudiant()
                                        ->findNonAbsentCours($cours,
                                                             $arg));
        }

        /**
         * Affiche l'erreur quand un cours n'existe pas.
         */
        public function inconnu() {
            $this->vue = new Vue($this, 'Inconnu', 'Cours Inconnu');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la suppression
         * d'un cours.
         */
        public function errSupprimer() {
            $this->vue =
                new Vue($this, 'ErrSupprimer', 'Erreur dans la suppression');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand il est impossible de créer un nouveau cours..
         */
        public function errCreer() {
            $this->vue =
                new Vue($this, 'ErrCreer', 'Erreur dans la création');
            $this->vue->render();
        }
    }