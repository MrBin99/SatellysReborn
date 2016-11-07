<?php

    namespace WS_SatellysReborn\Controleurs;


    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Administratif;
    use WS_SatellysReborn\Modeles\Population\Adresse\Adresse;
    use WS_SatellysReborn\Modeles\Population\Login\Utilisateur;
    use WS_SatellysReborn\Vues\Vue;

    class Administrateur extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                $this->vue = new Vue($this, "Nouveau");
                $this->vue->render();
            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
                $this->vue->render();
            }
        }

        /**
         * Ajout d'un membre du personnel administratif
         */
        public function ajout() {
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

                        $new = new Administratif($_POST['id'], $_POST['nom'],
                                                 $_POST['prenom'],
                                                 $_POST['tel'],
                                                 $_POST['email'],
                                                 $_POST['poste'],
                                                 $res);

                        $exist = false;
                        $administratif =
                            DAO_Factory::getDAO_Administratif()->findAll();
                        //On vérifie qu'il n'existe pas déjà un administrateur
                        // avec cet identifiant
                        if ($administratif) {
                            foreach ($administratif as $obj) {
                                if ($obj->getId() == $new->getId()) {
                                    $exist = true;
                                }
                            }
                        }
                        //Insertion dans la base de l'administratif
                        // si il n'existe pas déjà dans la BD
                        // Affichage de page d'erreur sinon
                        if (!$exist) {
                            $res2 =
                                DAO_Factory::getDAO_Administratif()
                                           ->insert($new);
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

            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
            }
            $this->vue->render();
        }

        /**
         * Ajout d'un utilisateur
         * Fonction appelé uniquement dans la fonction ajout de Administrateur
         */
        private function ajoutUtilisateur($id, $nom, $prenom, $mail) {
            $log = strtolower($nom) . "." . strtolower($prenom);
            $mdp = $id;
            $admin = DAO_Factory::getDAO_Administratif()->find($id);
            $util = new Utilisateur($log, $mdp, $mail, null, $admin);
            $res3 = DAO_Factory::getDAO_Utilisateur()->insert($util);
        }
    }