<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Parser\CSV_Parser;
    use SatellysReborn\Modeles\Population\Groupe\Groupe;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les groupes.
     * @package SatellysReborn\Controleurs
     */
    class GroupeControleur extends Controleur {

        /**
         * Affiche la liste de tout les groupes.
         */
        public function index() {
            if (Utilisateur::estConnecte()) {

                // Récupère toutes les promotions.
                $deps = DAO_Factory::getDAO_Groupe()->findAll();

                // Affichage.
                $this->vue = new Vue($this, 'Liste', 'Liste des groupes');
                $this->vue->render($deps);

            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche les détails d'un groupe.
         * @param $id string l'identifiant du groupe
         */
        public function details($id) {
            if (Utilisateur::estConnecte()) {
                if (isset($id)) {
                    // Récupère le groupe et ses étudiants.
                    $groupe = DAO_Factory::getDAO_Groupe()->find($id);

                    if (isset($groupe)) {
                        $etudiants =
                            DAO_Factory::getDAO_Groupe()
                                       ->getEtudiants($id);

                        // Affichage.
                        $this->vue =
                            new Vue($this, 'Details',
                                    'Groupe : ' . $groupe->getNom());
                        $this->vue->render(array($groupe, $etudiants));
                    } else {
                        self::redirect('/SatellysReborn/groupe/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/groupe/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche la page de creation du groupe.
         * @param string $id l'identifiant d'une promotion où le groupe
         * doit être affecté.
         */
        public function creation($id = null) {
            if (Utilisateur::utilCourantEstAdmin()) {

                $promo = null;
                if (isset($id)) {
                    $promo = DAO_Factory::getDAO_Promotion()->find($id);
                }

                $this->vue = new Vue($this, "Creation", "Créer un groupe");

                if ($promo == null) {
                    $this->vue->render(array($id));
                } else {
                    $this->vue->render(array($id, $promo));
                }

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la page de creation d'un département.
         */
        public function creer() {
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($_POST) && isset($_POST['nom'])
                    && isset($_POST['promo'])
                ) {
                    $promo =
                        DAO_Factory::getDAO_Promotion()->find($_POST['promo']);
                    $groupe = DAO_Factory::getDAO_Groupe()
                                         ->insert(new Groupe(null,
                                                             $_POST['nom'],
                                                             $promo));
                    if ($groupe != false) {
                        self::redirect('/SatellysReborn/groupe/details/' .
                                       $groupe->getId());
                    } else {
                        self::redirect('/SatellysReborn/groupe/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/groupe/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Modifie le groupe.
         * @param $id string l'identifiant du groupe.
         */
        public function modifier($id) {
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($id)) {
                    if (!isset($_POST['nom'])) {
                        self::redirect('/SatellysReborn/groupe/datails/' .
                                       $id . '/');
                    }

                    $groupe = DAO_Factory::getDAO_Groupe()->find($id);

                    // Promotion existe ?
                    if ($groupe == false) {
                        self::redirect('/SatellysReborn/groupe/inconnu/');
                    }

                    if (DAO_Factory::getDAO_Groupe()
                                   ->update(new Groupe($id, $_POST['nom'],
                                                       $groupe->getPromo()))
                    ) {
                        self::redirect('/SatellysReborn/groupe/datails/' .
                                       $id . '/');
                    } else {
                        self::redirect('/SatellysReborn/groupe/errModifier/');
                    }
                } else {
                    self::redirect('/SatellysReborn/groupe/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Supprime une promotion de la base de données s'il n'a plus de
         * groupes qui dépendent de lui.
         * @param $id string l'identifiant de la promotion.
         */
        public function supprimer($id) {
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($id)) {

                    $groupe = DAO_Factory::getDAO_Groupe()->find($id);

                    // Promotion existe ?
                    if ($groupe == false) {
                        self::redirect('/SatellysReborn/groupe/inconnu/');
                    }

                    // Les groupes.
                    $etudiants = DAO_Factory::getDAO_Groupe()
                                            ->getEtudiants($id);

                    // Plus d'étudiants pour ce groupe.
                    if ($etudiants == []) {
                        if (DAO_Factory::getDAO_Groupe()->delete($groupe) !=
                            false
                        ) {
                            $this->vue = new Vue($this, "SupprimerOK",
                                                 "Suppression du groupe effectuée");
                            $this->vue->render();
                        } else {
                            self::redirect('/SatellysReborn/groupe/errSupprimer/');
                        }
                    } else {
                        self::redirect('/SatellysReborn/groupe/errEtudiant/');
                    }
                } else {
                    self::redirect('/SatellysReborn/groupe/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la page d'ajout d'un étudiant à un groupe.
         * @param $id string l'identifiant du groupe.
         */
        public function ajouterEtudiant($id) {
            if (Utilisateur::utilCourantEstAdmin()) {

                $this->vue = new Vue($this, 'AjouterEtudiant',
                                     'Ajouter un étudiant au groupe');
                $this->vue->render($id);

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Ajoute un étudiant dans le groupe dont l'identifiant est passé en
         * argument.
         * @param $id string l'identifiant du groupe.
         */
        public function ajouter($id) {
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($id)) {
                    if (!isset($_POST['etudiant'])) {
                        self::redirect('/SatellysReborn/groupe/datails/' .
                                       $id . '/');
                    }

                    $groupe = DAO_Factory::getDAO_Groupe()->find($id);

                    // Groupe existe ?
                    if ($groupe == false) {
                        self::redirect('/SatellysReborn/groupe/inconnu/');
                    }

                    if (DAO_Factory::getDAO_Groupe()
                                   ->ajouterEtudiant($id, $_POST['etudiant'])
                    ) {
                        self::redirect('/SatellysReborn/groupe/details/' .
                                       $id);
                    } else {
                        self::redirect('/SatellysReborn/groupe/errAjoutEtudiant/');
                    }
                } else {
                    self::redirect('/SatellysReborn/groupe/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Supprime un étudiant d'un groupe.
         * @param $groupe string l'identifiant du groupe.
         * @param $etudiant string l'identifiant de l'étudiant.
         */
        public function supprimerEtudiant($groupe, $etudiant) {
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($groupe)) {
                    if (isset($etudiant)) {
                        if (DAO_Factory::getDAO_Groupe()
                                       ->supprimerEtudiant($groupe,
                                                           $etudiant)
                        ) {
                            self::redirect('/SatellysReborn/groupe/details/' .
                                           $groupe);
                        } else {
                            self::redirect('/SatellysReborn/groupe/errSupprimerEtudiant/');
                        }
                    } else {
                        self::redirect('/SatellysReborn/etudiant/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/groupe/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la page d'importation d'une liste d'étudiant
         * par un fichier CSV.
         * @param $id string le groupe dans lequel on doit importer les
         *     étudiants.
         */
        public function csv($id) {
            if (Utilisateur::utilCourantEstAdmin()) {

                $this->vue = new Vue($this, 'CSV', 'Importer un fichier CSV');
                $this->vue->render($id);

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche les logs après avoir lu le fichier CSV.
         * @param $id string le groupe dans lequel on doit importer les
         *     étudiants.
         */
        public function logsCSV($id) {
            if (Utilisateur::utilCourantEstAdmin()) {

                $groupe = DAO_Factory::getDAO_Groupe()->find($id);

                if ($groupe == null) {
                    self::redirect('/SatellysReborn/groupe/inconnu/');
                }

                if (isset($_FILES['csv'])) {

                    $f = fopen($_FILES['csv']['tmp_name'], 'r');
                    $contenu = array();

                    while (($ligne = fgets($f)) != null) {
                        array_push($contenu, trim($ligne));
                    }

                    $csvParser = new CSV_Parser($groupe, $contenu);
                    $csvParser->parse();

                    $_SESSION['csv'] = $csvParser;

                    $this->vue = new Vue($this, 'LogsCSV',
                                         'Résultat lecture du fichier CSV');
                    $this->vue->render($csvParser);

                } else {
                    self::redirect('/SatellysReborn/groupe/errCSV/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Importe le fichier CSV.
         */
        public function importCSV() {
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($_SESSION['csv'])) {

                    $this->vue = new Vue($this, 'ImportCSV',
                                         'Importation du CSV réussie !');
                    $this->vue->render($_SESSION['csv']->insererBD());

                    unset($_SESSION['csv']);
                } else {
                    self::redirect('/SatellysReborn/groupe/errCSV/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche les groupes dont le nom correspond à celui passé
         * en argumentau format JSON.
         * @param $arg string l'argument de rechercher.
         */
        public function listeJSON($arg) {
            echo json_encode(DAO_Factory::getDAO_Groupe()->findIdNom($arg));
        }

        /**
         * Affiche l'erreur quand un groupe n'existe pas.
         */
        public function inconnu() {
            $this->vue = new Vue($this, 'Inconnu', 'Groupe Inconnu');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la suppression
         * d'un groupe.
         */
        public function errSupprimer() {
            $this->vue =
                new Vue($this, 'ErrSupprimer', 'Erreur dans la suppression');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la modification
         * d'un groupe.
         */
        public function errModifier() {
            $this->vue =
                new Vue($this, 'ErrModifier', 'Erreur dans la modification');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand le groupe ne peut être supprimer car il
         * contient encore des étudiants.
         */
        public function errEtudiant() {
            $this->vue =
                new Vue($this, 'ErrEtudiants',
                        'Erreur ce groupe contient des étudiants');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand le groupe ne peut être supprimer car il
         * contient encore des étudiants.
         */
        public function errAjoutEtudiant() {
            $this->vue =
                new Vue($this, 'ErrAjoutEtudiant',
                        'Erreur impossible d\'ajouter cet étudiant');
            $this->vue->render();
        }


        /**
         * Affiche l'erreur quand on ne peut pas supprimer un étudiant du
         * groupe.
         */
        public function errSupprimerEtudiant() {
            $this->vue =
                new Vue($this, 'ErrSupprimerEtudiant',
                        'Erreur impossible d\'ajouter cet étudiant');
            $this->vue->render();
        }

        /**
         * Affiche une erreur quand le fichier CSV est introuvable ou mal
         * importé.
         */
        public function errCSV() {
            $this->vue =
                new Vue($this, 'ErrCSV',
                        'Fichier CSV introuvable');
            $this->vue->render();
        }
    }