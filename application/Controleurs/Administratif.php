<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;
    use SatellysReborn\Modeles\Population\Enseignant;
    use SatellysReborn\Modeles\Population\Groupe\Departement;
    use SatellysReborn\Modeles\Population\Groupe\Promotion;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    class Administratif extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            if (Utilisateur::estConnecte() &&
                (Utilisateur::getUtilisateur()->estAdmin() ||
                 Utilisateur::getUtilisateur()->estAdministratif())
            ) {
                $ville = DAO_Factory::getDAO_Ville()->findAll();
                $deps = DAO_Factory::getDAO_Departement()->findAll();
                $this->vue =
                    new Vue($this, "Ajouts", "Ajout pour administrateur");
                $this->vue->render(array($ville, $deps));
            } else {
                self::redirect("/SatellysReborn/administratif/errNonAdmins");
            }
        }

        /**
         * Fonction d'ajout d'un nouvel enseignant
         */
        public function ajoutProf() {
            if (Utilisateur::estConnecte() &&
                (Utilisateur::getUtilisateur()->estAdmin() ||
                 Utilisateur::getUtilisateur()->estAdministratif())
            ) {
                //vérification de l'existance de $_POST
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

                    //vérification si l'insertion de l'adresse a bien eu lieu
                    if (!$res) {
                        self::redirect("/SatellysReborn/administratif/errAdresse");
                    } else {
                        $new = new Enseignant($_POST['id'], $_POST['nom'],
                                              $_POST['prenom'], $_POST['tel'],
                                              $res);

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
                            DAO_Factory::getDAO_Enseignant()->insert($new);
                            $this->ajoutUtilisateur($_POST['id'], $_POST['nom'],
                                                    $_POST['prenom'],
                                                    $_POST['email']);
                            //redirection de la page après réussite
                            $this->vue = new Vue($this, "AjoutOkProf");
                            $this->vue->render();
                        } else {
                            self::redirect("/SatellysReborn/administratif/errAjout");
                        }
                    }
                } else {
                    self::redirect("/SatellysReborn/administratif/errChamps");
                }
            } else {
                self::redirect("/SatellysReborn/administratif/errNonAdmins");
            }
        }

        /**
         * Ajout d'un utilisateur
         * Fonction appelé uniquement dans la fonction ajout de Enseignant
         * @param $id : identifiant de l'utilisateur qui sera utilisé pour
         *     créer le mot de passe
         * @param $nom : nom de l'utilisateur utilisé pour le login, combiné
         *     avec le prénom
         * @param $prenom : prenom de l'utilisateur utilisé pour le login,
         *     combiné avec le nom
         * @param $mail : adresse mail de l'utilisateur
         */
        private function ajoutUtilisateur($id, $nom, $prenom, $mail) {
            $log = strtolower($nom) . "." . strtolower($prenom);
            $mdp = $id;
            $enseignant = DAO_Factory::getDAO_Enseignant()->find($id);
            $util = new Utilisateur($log, $mdp, $mail, $enseignant, null);
            DAO_Factory::getDAO_Utilisateur()->insert($util);
        }

        /**
         * Fonction d'ajout d'un département
         */
        public function ajoutDep() {
            if (Utilisateur::estConnecte() &&
                (Utilisateur::getUtilisateur()->estAdmin() ||
                 Utilisateur::getUtilisateur()->estAdministratif())
            ) {
                //vérification de l'existance de $_POST
                if (isset($_POST)) {
                    $dep = new Departement($_POST['idDep'], $_POST['nomDep']);
                    if (DAO_Factory::getDAO_Departement()
                                   ->find($_POST['idDep']) == null
                    ) {
                        DAO_Factory::getDAO_Departement()->insert($dep);
                        $this->vue = new Vue($this, 'AjoutOkDep');
                        $this->vue->render();
                    } else {
                        self::redirect("/SatellysReborn/administratif/errAjout");
                    }
                } else {
                    self::redirect("/SatellysReborn/administratif/errChamps");
                }
            } else {
                self::redirect("/SatellysReborn/administratif/errNonAdmins");
            }
        }

        /**
         * Fonction d'ajout d'une filière
         */
        public function ajoutPromo() {
            if (Utilisateur::estConnecte() &&
                (Utilisateur::getUtilisateur()->estAdmin() ||
                 Utilisateur::getUtilisateur()->estAdministratif())
            ) {
                //vérification de l'existance de $_POST
                if (isset($_POST)) {
                    $dep = DAO_Factory::getDAO_Departement()
                                      ->find($_POST['depPromo']);
                    $promo = new Promotion(null, $_POST['nomPromo'],
                                           $_POST['anneePromo'], $dep);

                    $res = DAO_Factory::getDAO_Promotion()->insert($promo);
                    if (DAO_Factory::getDAO_Departement()
                                   ->find($res->getId()) == null
                    ) {
                        $this->vue = new Vue($this, 'AjoutOkPromo');
                        $this->vue->render();
                    } else {
                        self::redirect("/SatellysReborn/administratif/errAjout");
                    }
                } else {
                    self::redirect("/SatellysReborn/administratif/errChamps");
                }
            } else {
                self::redirect("/SatellysReborn/administratif/errNonAdmins");
            }
        }

        /**
         * Affiche la page d'erreur quand un utilisateur essaie d'accéder à
         * une page sans être connecté en tant qu'administrateur ou
         * administratif.
         */
        public function errNonAdmins() {
            $this->vue = new Vue($this, 'ErrNonAdmins',
                                 'Pas administrateur ou administratif');
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