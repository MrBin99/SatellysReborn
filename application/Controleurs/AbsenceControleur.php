<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Cours\Absence;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les absences à des cours.
     * @package SatellysReborn\Controleurs
     */
    class AbsenceControleur extends Controleur {

        /**
         * Redirige vers la page "Inconnu".
         */
        public function index() {
            self::redirect('/SatellysReborn/absence/inconnu/');
        }

        /**
         * Affiche les détails d'absence.
         * @param $idCours string l'identifiant du cours.
         * @param $idEtudiant string l'identifiant de l'étudiant.
         */
        public function details($idCours, $idEtudiant) {
            // Récupère le cours.
            $cours = DAO_Factory::getDAO_Cours()->find($idCours);

            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
                || (Utilisateur::utilCourantEstEnseignant() &&
                    $cours->getEnseignant()->getId() ==
                    Utilisateur::getUtilisateur()->getEnseignant()->getId())
            ) {
                if (isset($idCours) && isset($idEtudiant)) {
                    $etudiant =
                        DAO_Factory::getDAO_Etudiant()->find($idEtudiant);

                    if (!isset($etudiant)) {
                        self::redirect('/SatellysReborn/etudiant/inconnu/');
                    }

                    // Récupère l'absence.
                    $absence = DAO_Factory::getDAO_Absence()
                                          ->getAbsence($idCours, $idEtudiant);

                    // Si l'absence existe.
                    if (isset($absence)) {
                        $this->vue = new Vue($this, "Details", "Absence");
                        $this->vue->render($absence);
                    } else {
                        self::redirect('/SatellysReborn/absence/inconnu/');
                    }

                } else {
                    self::redirect('/SatellysReborn/cours/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la page d'ajout d'un absent à un cours.
         * @param $id string l'identifiant du cours.
         */
        public function creation($id) {
            // Récupère le cours.
            $cours = DAO_Factory::getDAO_Cours()->find($id);

            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
                || (Utilisateur::utilCourantEstEnseignant() &&
                    $cours->getEnseignant()->getId() ==
                    Utilisateur::getUtilisateur()->getEnseignant()->getId())
            ) {
                $this->vue = new Vue($this, "Creation", "Ajouter un absent");
                $this->vue->render($id);

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Ajoute dans la base de données une absences pour un étudiant à un
         * cours.
         * @param $idCours string l'identifiant du cours.
         */
        public function creer($idCours) {
            // Récupère le cours.
            $cours = DAO_Factory::getDAO_Cours()->find($idCours);

            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
                || (Utilisateur::utilCourantEstEnseignant() &&
                    $cours->getEnseignant()->getId() ==
                    Utilisateur::getUtilisateur()->getEnseignant()->getId())
            ) {
                if (isset($idCours) && isset($_POST['etudiant'])) {

                    $cours = DAO_Factory::getDAO_Cours()->find($idCours);
                    $etudiant =
                        DAO_Factory::getDAO_Etudiant()
                                   ->find($_POST['etudiant']);

                    $res = DAO_Factory::getDAO_Absence()
                                      ->insert(new Absence($cours, $etudiant,
                                                           "0", ""));
                    if ($res != false) {
                        self::redirect('/SatellysReborn/cours/details/' .
                                       $idCours);
                    } else {
                        self::redirect('/SatellysReborn/absence/errCreer/');
                    }
                } else {
                    self::redirect('/SatellysReborn/cours/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Modifie l'absence.
         * @param $idCours string l'identifiant du cours.
         * @param $idEtudiant string l'identifiant de l'étudiant.
         */
        public function modifier($idCours, $idEtudiant) {
            // Récupère le cours.
            $cours = DAO_Factory::getDAO_Cours()->find($idCours);

            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
                || (Utilisateur::utilCourantEstEnseignant() &&
                    $cours->getEnseignant()->getId() ==
                    Utilisateur::getUtilisateur()->getEnseignant()->getId())
            ) {
                if (isset($idCours) && isset($idEtudiant)) {
                    $etudiant =
                        DAO_Factory::getDAO_Etudiant()->find($idEtudiant);

                    if (!isset($etudiant)) {
                        self::redirect('/SatellysReborn/etudiant/inconnu/');
                    }

                    if (DAO_Factory::getDAO_Absence()
                                   ->update(new Absence($cours,
                                                        $etudiant,
                                       isset($_POST['justifie']),
                                                        $_POST['motif']))
                    ) {
                        self::redirect('/SatellysReborn/cours/details/' .
                                       $idCours);
                    } else {
                        self::redirect('SatellysReborn/absence/errModifier/');
                    }
                } else {
                    self::redirect('/SatellysReborn/cours/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Supprime l'absence à un cours d'un étudiant.
         * @param $idCours string l'identifiant du cours.
         * @param $idEtudiant string l'identifiant de l'étudiant.
         */
        public function supprimer($idCours, $idEtudiant) {
            // Récupère le cours.
            $cours = DAO_Factory::getDAO_Cours()->find($idCours);

            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
                || (Utilisateur::utilCourantEstEnseignant() &&
                    $cours->getEnseignant()->getId() ==
                    Utilisateur::getUtilisateur()->getEnseignant()->getId())
            ) {
                if (isset($idCours) && isset($idEtudiant)) {
                    $etudiant =
                        DAO_Factory::getDAO_Etudiant()->find($idEtudiant);

                    if (!isset($etudiant)) {
                        self::redirect('/SatellysReborn/etudiant/inconnu/');
                    }

                    // Supprime
                    if (DAO_Factory::getDAO_Absence()
                                   ->delete(new Absence($cours,
                                                        $etudiant,
                                                        false,
                                                        ""))
                    ) {
                        self::redirect('/SatellysReborn/cours/details/' .
                                       $idCours);
                    } else {
                        self::redirect('SatellysReborn/absence/errSupprimer/');
                    }
                } else {
                    self::redirect('/SatellysReborn/cours/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche l'erreur quand une absence n'existe pas.
         */
        public function inconnu() {
            $this->vue = new Vue($this, 'Inconnu', 'Absence Inconnue');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la suppression
         * d'une absence.
         */
        public function errSupprimer() {
            $this->vue =
                new Vue($this, 'ErrSupprimer', 'Erreur dans la suppression');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la modification
         * d'une absence.
         */
        public function errModifier() {
            $this->vue =
                new Vue($this, 'ErrModifier', 'Erreur dans la modification');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand il est impossible de créer une nouvelle absence.
         */
        public function errCreer() {
            $this->vue =
                new Vue($this, 'ErrCreer', 'Erreur dans la création');
            $this->vue->render();
        }
    }