<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Cours\Absence;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    class Cours extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            if (Utilisateur::estConnecte() &&
                (Utilisateur::getUtilisateur()->estAdmin() ||
                 Utilisateur::getUtilisateur()->estEnseignant())
            ) {

                $this->vue =
                    new Vue($this, "ChoixCours", "Sélection d'un cours");
                $cours = DAO_Factory::getDAO_Cours()->findAll();
                $this->vue->render($cours);
            } else {
                self::redirect("/SatellysReborn/administratif/errNonAdmins");
            }
        }

        /**
         * Fonction qui ouvre la page d'ajout d'absence en fonction d'un cours
         */
        public function selectCours() {
            if (Utilisateur::estConnecte() &&
                (Utilisateur::getUtilisateur()->estAdmin() ||
                 Utilisateur::getUtilisateur()->estEnseignant())
            ) {
                $this->vue = new Vue($this, "SaisieAbsence", "Ajout d'absence");
                $etudiants =
                    DAO_Factory::getDAO_Cours()->getGroupes($_POST['cours']);
                $abs = DAO_Factory::getDAO_Absence()
                                  ->getAbsencesCours($_POST['cours']);
                $this->vue->render(array($etudiants, $_POST['cours'], $abs));
            } else {
                self::redirect("/SatellysReborn/administratif/errNonAdmins");
            }
        }

        /**
         * Fonction d'ajout d'absence pour des étudiants dans un cours
         */
        public function addAbsence() {

            if (Utilisateur::estConnecte() &&
                (Utilisateur::getUtilisateur()->estAdmin() ||
                 Utilisateur::getUtilisateur()->estEnseignant())
            ) {
                //vérification de l'existance de $_POST
                if (isset($_POST)) {
                    $complet = true;
                    $tabErreur = array();
                    $j = 0;

                    for ($i = 0; $i < count($_POST['idEtudiant']); $i++) {
                        $id = $_POST['idEtudiant'][$i];
                        if (isset($_POST['etudiant'.$id])) {
                            $exist = DAO_Factory::getDAO_Absence()
                                                ->getAbsence($_POST['idcours'],
                                                             $id);


                            $etudiant =
                                DAO_Factory::getDAO_Etudiant()->find($id);

                            $idcours = $_POST['idcours'];
                            $cours =
                                DAO_Factory::getDAO_Cours()->find($idcours);

                            if (isset($_POST['motif' . $id])) {
                                $motif = $_POST['motif' . $id];
                            } else {
                                $motif = "";
                            }

                            if (isset($_POST['justifie' . $id])) {
                                $justif = $_POST['justifie' . $id];
                            } else {
                                $justif = 0;
                            }

                            //il y a déjà une absence dans ce cours pour cet étudiant
                            if ($exist != null) {

                                //le motif ou la justification on changé, donc on update la bd
                                if ($motif != $exist->getMotif() ||
                                    $justif != $exist->estJustifie()
                                ) {
                                    $new = new Absence($cours, $etudiant,
                                                       $justif, $motif);
                                    DAO_Factory::getDAO_Absence()
                                               ->update($new);
                                }
                                //il n'y a pas d'absence sur ce cours pour cet étudiant
                            } else {

                                $abs =
                                    new Absence($cours, $etudiant, $justif,
                                                $motif);

                                //insertion dans la bd : normalement renvoie true si ok
                                //mais renvoie l'inverse (false) si ok
                                $res =
                                    DAO_Factory::getDAO_Absence()->insert($abs);
                                if ($res) {
                                    $complet = false;
                                    $tabErreur[$j] =
                                        DAO_Factory::getDAO_Etudiant()
                                                   ->find($id);
                                    $j++;
                                }
                            }
                        } else {

                            $etud = DAO_Factory::getDAO_Absence()
                                               ->getAbsence($_POST['idcours'],
                                                            $id);
                            //existe-t-il une absence sur ce cours pour cette élève
                            // si oui on supprime
                            if ($etud != null) {

                                DAO_Factory::getDAO_Absence()
                                           ->delete($etud);
                            }
                        }
                    }
                     if ($complet) {
                        $this->vue =
                            new Vue($this, "AjoutsAbsOk", "Ajout réussi");
                        $this->vue->render();
                    } else {
                        $this->vue =
                            new Vue($this, "ErrAjout", "Erreur d'ajout");
                        $this->vue->render($tabErreur);
                    }
                } else {
                    self::redirect("/SatellysReborn/cours/errChamps/");
                }
            } else {
                self::redirect("/SatellysReborn/cours/errNonAdmins/");
            }
        }

        /**
         * Affiche la page d'erreur quand un utilisateur essaie d'accéder à
         * une page sans être connecté en tant qu'administrateur ou
         * administratif.
         */
        public function errNonAdmins() {
            $this->vue = new Vue($this, 'ErrNonAdmins',
                                 'Pas administrateur ou enseignant');
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur quand le formulaire est vide
         */
        public function errChamps() {
            $this->vue = new Vue($this, 'ErrChamps', "Formulaire vide");
            $this->vue->render();
        }
    }