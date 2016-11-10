<?php
    namespace WS_SatellysReborn\Controleurs;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Cours\Absence;
    use WS_SatellysReborn\Modeles\Population\Login\Utilisateur;
    use WS_SatellysReborn\Vues\Vue;

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

                $this->vue = new Vue($this, "ChoixCours");
                $cours = DAO_Factory::getDAO_Cours()->findAll();
                $this->vue->render($cours);
            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
                $this->vue->render();
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
                $this->vue = new Vue($this, "SaisieAbsence");
                $etudiants =
                    DAO_Factory::getDAO_Cours()->getGroupes($_POST['cours']);
                $this->vue->render(array($etudiants, $_POST['cours']));
            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
                $this->vue->render();
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
                    for ($i = 0; $i < count($_POST['etudiant']); $i++) {
                        if (isset($_POST['etudiant'][$i])) {
                            $id = (string) $_POST['etudiant'][$i];
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

                            $abs =
                                new Absence($cours, $etudiant, $justif, $motif);
                            //insertion dans la bd : normalement renvoie true si ok
                            //mais renvoie l'inverse (false) si ok
                            $res = DAO_Factory::getDAO_Absence()->insert($abs);
                            var_dump($res);
                            if ($res) {
                                $complet = false;
                                $tabErreur[$j] =
                                    DAO_Factory::getDAO_Etudiant()->find($id);
                                $j++;
                            }
                        }

                    }
                    if ($complet) {
                        $this->vue = new Vue($this, "AjoutAbsOk");
                        $this->vue->render();
                    } else {
                        $this->vue = new Vue($this, "ErreurAjout");
                        $this->vue->render($tabErreur);
                    }
                } else {
                    $this->vue = new Vue($this, "ErreurChamp");
                    $this->vue->render();
                }
            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
                $this->vue->render();
            }

        }
    }