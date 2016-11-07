<?php
    namespace WS_SatellysReborn\Controleurs;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Administratif as ModeleAdministratif;
    use WS_SatellysReborn\Modeles\Population\Adresse\Adresse;
    use WS_SatellysReborn\Modeles\Population\Enseignant;
    use WS_SatellysReborn\Modeles\Population\Login\Utilisateur;
    use WS_SatellysReborn\Vues\Vue;

    /**
     * Controleur des administratifs.
     * @package WS_SatellysReborn\Controleurs
     */
    class Administratif extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            // Bien super-admin ?
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {

                $this->vue = new Vue($this, "Ajouts");
                $this->vue->render();
            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
                $this->vue->render();
            }
        }
/*
        public function nouveau()
        {
            // Bien super-admin ?
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                $this->vue = new Vue($this, "Nouveau");
            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
            }
            $this->vue->render();
        }
*/
        /**
         * Fonction d'ajout d'un nouvel enseignant
         */
        public function ajoutProf(){
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                //vérification de l'existance de $_POST
                if (isset($_POST)) {
                    $ville = DAO_Factory::getDAO_Ville()->find($_POST['ville']);
                    $adr = new Adresse(null, $_POST['adresse'], "", "", $ville);

                    $res = DAO_Factory::getDAO_Adresse()->insert($adr);

                    //vérification si l'insertion de l'adresse a bien eu lieu
                    if (!$res) {
                        $this->vue = new Vue($this, "ErreurAdresse");
                    } else {

                        $new = new Enseignant($_POST['id'], $_POST['nom'],
                                              $_POST['prenom'], $_POST['tel'],
                                              $_POST['email'], $res);

                        $exist = false;
                        $enseignant =
                            DAO_Factory::getDAO_Enseignant()->findAll();
                        //On vérifie qu'il n'existe pas déjà un enseignant
                        // avec cet identifiant
                        if ($enseignant) {
                            foreach ($enseignant as $obj) {
                                if ($obj->getId() == $new->getId()) {
                                    $exist = true;
                                }
                            }
                        }
                        //Insertion dans la base de l'enseignant
                        // si il n'existe pas déjà dans la BD
                        // Affichage de page d'erreur sinon
                        if (!$exist) {
                            $res2 = DAO_Factory::getDAO_Enseignant()->insert($new);
                            $this->ajoutUtilisateur($_POST['id'], $_POST['nom'],
                                                    $_POST['prenom'],
                                                    $_POST['email']);
                            //redirection de la page après réussite
                            $this->vue = new Vue($this, "AjoutOk");
                        } else {
                            $this->vue = new Vue($this, "ErreurAjout");
                        }
                    }
                } else {
                    $this->vue = new Vue($this, "ErreurChamp");
                }
            }else {
                $this->vue = new Vue($this, 'ErreurAdmin');
            }
            $this->vue->render();
        }

        /**
         * Ajout d'un utilisateur
         * Fonction appelé uniquement dans la fonction ajout de Enseignant
         */
        private function ajoutUtilisateur($id, $nom, $prenom, $mail) {
            $log = strtolower($nom) . "." . strtolower($prenom);
            $mdp = $id;
            $enseignant = DAO_Factory::getDAO_Enseignant()->find($id);
            $util = new Utilisateur($log, $mdp, $mail, $enseignant, null);
            $res3 = DAO_Factory::getDAO_Utilisateur()->insert($util);
        }

        /**
         * Fonction d'ajout d'un département
         */
        public function ajoutDep(){

        }

        /**
         * Fonction d'ajout d'une filière
         */
        public function ajoutFiliere(){

        }
    }