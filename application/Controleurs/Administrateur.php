<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Administratif;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Modeles\Utils\Utils;
    use SatellysReborn\Vues\Vue;

    class Administrateur extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                $ville = DAO_Factory::getDAO_Ville()->findAll();
                $this->vue =
                    new Vue($this, "Nouveau", "Ajout d'un administratif");
                $this->vue->render($ville);
            } else {
                self::redirect("/SatellysReborn/administrateur/errNonAdmin");
            }
        }

        /**
         * Ajout d'un membre du personnel administratif
         */
        public function ajout() {
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                // Vérification de l'existance de $_POST
                if (isset($_POST)) {
                    $ville = DAO_Factory::getDAO_Ville()->find($_POST['ville']);
                    if (isset($_POST['adresse2'])) {
                        $adr2 = $_POST['adresse2'];
                    } else {
                        $adr2 = "";
                    }
                    if (isset($_POST['adresse3'])) {
                        $adr3 = $_POST['adresse3'];
                    } else {
                        $adr3 = "";
                    }
                    $adr = new Adresse(null, $_POST['adresse'], $adr2, $adr3,
                                       $ville);
                    $res = DAO_Factory::getDAO_Adresse()->insert($adr);

                    // Vérification si l'insertion de l'adresse à fonctionné
                    if (!$res) {
                        self::redirect("/SatellysReborn/administrateur/errAdresse");
                    } else {
                        $new = new Administratif($_POST['id'], strtoupper($_POST['nom']),
                                                 ucfirst(strtolower($_POST['prenom'])),
                                                 $_POST['tel'],
                                                 $_POST['poste'],
                                                 $res);
                        $exist = false;
                        $administratif =
                            DAO_Factory::getDAO_Administratif()->findAll();

                        // On vérifie qu'il n'existe pas déjà un administrateur
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
                            DAO_Factory::getDAO_Administratif()->insert($new);
                            $this->ajoutUtilisateur($_POST['id'], $_POST['nom'],
                                                    $_POST['prenom'],
                                                    $_POST['email']);
                            //redirection de la page après réussite
                            $this->vue = new Vue($this, "AjoutOk");
                            $this->vue->render();
                        } else {
                            self::redirect("/SatellysReborn/administrateur/errAjout");
                        }
                    }
                } else {
                    self::redirect("/SatellysReborn/administrateur/errChamps");
                }
            } else {
                self::redirect("/SatellysReborn/administrateur/errNonAdmin");
            }

        }

        /**
         * Ajout d'un utilisateur
         * Fonction appelé uniquement dans la fonction ajout de Administrateur
         * @param $id string identifiant de l'utilisateur qui sera utilisé pour
         *     créer le mot de passe
         * @param $nom string nom de l'utilisateur utilisé pour le login, combiné
         *     avec le prénom
         * @param $prenom string prenom de l'utilisateur utilisé pour le login,
         *     combiné avec le nom
         * @param $mail string adresse mail de l'utilisateur
         */
        private function ajoutUtilisateur($id, $nom, $prenom, $mail) {
            $log = Utils::enleverAccents(strtolower($prenom) . "." .
                                         strtolower($nom));

            $admin = DAO_Factory::getDAO_Administratif()->find($id);
            $util = new Utilisateur($log, $log, $mail, null, $admin);
            DAO_Factory::getDAO_Utilisateur()->insert($util);
        }

        /**
         * Affiche la page d'erreur quand un utilisateur essaie d'accéder à
         * une page sans être connecté en tant qu'administrateur.
         */
        public function errNonAdmin() {
            $this->vue = new Vue($this, 'ErrNonAdmin', 'Pas administrateur');
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur quand un problème d'adresse survient
         */
        public function errAdresse() {
            $this->vue = new Vue($this, 'ErrAdresse', "Problème d'adresse");
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur quand il y a un problème lors d'un ajout en
         * BD
         */
        public function errAjout() {
            $this->vue = new Vue($this, 'ErrAjout', "Problème d'insertion");
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