<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;
    use SatellysReborn\Modeles\Population\Etudiant;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les étudiants.
     * @package SatellysReborn\Controleurs
     */
    class EtudiantControleur extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            if (Utilisateur::estConnecte()) {

                // Récupère tous les enseignants.
                $ens = DAO_Factory::getDAO_Etudiant()->findAll();

                // Affichage.
                $this->vue = new Vue($this, 'Liste', 'Liste des étudiants');
                $this->vue->render($ens);

            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche les détails d'un étudiant.
         * @param $id string l'identifiant de l'étudiant.
         */
        public function details($id) {
            if (Utilisateur::estConnecte()) {
                if (isset($id)) {
                    // Récupère l'étudiant.
                    $etud = DAO_Factory::getDAO_Etudiant()->find($id);

                    if (isset($etud)) {
                        $abs = DAO_Factory::getDAO_Absence()
                                          ->getAbsencesEtudiant($id);

                        // Affichage.
                        $this->vue =
                            new Vue($this, 'Details', $etud->getNomComplet());
                        $this->vue->render(array($etud, $abs));
                    } else {
                        self::redirect('/SatellysReborn/etudiant/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/etudiant/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche la page de creation d'un enseignant.
         */
        public function creation() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
            ) {

                $this->vue = new Vue($this, 'Creation', "Créer un étudiant");
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
                    isset($_POST['prenom']) && isset($_POST['ine']) &&
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

                    $etudiant = DAO_Factory::getDAO_Etudiant()
                                           ->insert(new Etudiant($_POST['id'],
                                                                 $_POST['ine'],
                                                                 strtoupper($_POST['nom']),
                                                                 ucfirst(strtolower($_POST['prenom'])),
                                                                 $_POST['tel'],
                                                                 $_POST['email'],
                                                                 $adresse));

                    if ($adresse != false && $etudiant != false) {
                        self::redirect('/SatellysReborn/etudiant/details/' .
                                       $_POST['id']);
                    } else {
                        self::redirect('/SatellysReborn/etudiant/errCreer/');
                    }
                } else {
                    self::redirect('/SatellysReborn/etudiant/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Créé un nouvel étudiant dans la base de données.
         * @param $id string l'identifiant de l'étudiant.
         */
        public function modifier($id) {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($id) && isset($_POST['nom']) &&
                    isset($_POST['prenom']) &&
                    isset($_POST['tel']) && isset($_POST['email']) &&
                    isset($_POST['adresse1']) && isset($_POST['adresse2']) &&
                    isset($_POST['adresse3']) && isset($_POST['ville'])
                ) {

                    // L'étudiant.
                    $etudiant = DAO_Factory::getDAO_Etudiant()->find($id);

                    if (!isset($etudiant)) {
                        self::redirect('/SatellysReborn/etudiant/inconnu/');
                    }

                    $adresse = DAO_Factory::getDAO_Adresse()
                                          ->find($etudiant->getAdresse()
                                                          ->getId());

                    $resAdr = DAO_Factory::getDAO_Adresse()
                                         ->update(new Adresse($adresse->getId(),
                                                              $_POST['adresse1'],
                                                              $_POST['adresse2'],
                                                              $_POST['adresse3'],
                                                              DAO_Factory::getDAO_Ville()
                                                                         ->find($_POST['ville'])));

                    $resEtud = DAO_Factory::getDAO_Etudiant()
                                          ->update(new Etudiant($etudiant->getId(),
                                                                $etudiant->getIne(),
                                                                $_POST['nom'],
                                                                $_POST['prenom'],
                                                                $_POST['tel'],
                                                                $_POST['email'],
                                                                $adresse));

                    if ($resAdr && $resEtud) {
                        self::redirect('/SatellysReborn/etudiant/details/' .
                                       $etudiant->getId());
                    } else {
                        self::redirect('/SatellysReborn/etudiant/errModifier/');
                    }

                } else {
                    self::redirect('/SatellysReborn/etudiant/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Supprime un étudiant.
         * @param $id string l'identifiant de l'étudiant.
         */
        public function supprimer($id) {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($id)) {

                    // L'etudiant.
                    $etudiant = DAO_Factory::getDAO_Etudiant()->find($id);

                    if ($etudiant == false) {
                        self::redirect('/SatellysReborn/enseignant/inconnu/');
                    }

                    $groupes =
                        DAO_Factory::getDAO_Groupe()->findEtudiantGroupes($id);
                    $absences = DAO_Factory::getDAO_Absence()->getAbsencesEtudiant($id);


                    if ($groupes != [] && $absences != []) {
                        self::redirect('/SatellysReborn/etudiant/errGroupesAbsences/');
                    }

                    if (DAO_Factory::getDAO_Etudiant()->delete($etudiant)) {
                        self::redirect('/SatellysReborn/etudiant/');
                    } else {
                        self::redirect('/SatellysReborn/etudiant/errSupprimer/');
                    }

                } else {
                    self::redirect('/SatellysReborn/etudiant/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la liste des étudiants dont le nom, l'INE ou l'identifiant
         * correspond à celui passé en argument, retourne le résultat au format
         * JSON.
         * @param $arg string l'argument de recherche.
         */
        public function listeJSON($arg) {
            echo json_encode(DAO_Factory::getDAO_Etudiant()
                                        ->findNomIdIne($arg));
        }

        /**
         * Affiche la page d'erreur quand un étudiant n'existe pas.
         */
        public function inconnu() {
            $this->vue = new Vue($this, "Inconnu", "Etudiant inconnu");
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la suppression
         * d'un département.
         */
        public function errSupprimer() {
            $this->vue =
                new Vue($this, 'ErrSupprimer', 'Erreur dans la suppression');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la modification
         * d'un étudiant.
         */
        public function errModifier() {
            $this->vue =
                new Vue($this, 'ErrModifier', 'Erreur dans la modification');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand il est impossible de créer un nouvel étudiant.
         */
        public function errCreer() {
            $this->vue =
                new Vue($this, 'ErrCreer', 'Erreur dans la création');
            $this->vue->render();
        }
    }