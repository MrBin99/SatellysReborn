<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Cours\Matiere;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les matières.
     * @package SatellysReborn\Controleurs
     */
    class MatiereControleur extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            if (Utilisateur::estConnecte()) {

                // Récupère toutes les matières.
                $mats = DAO_Factory::getDAO_Matiere()->findPromoDep();

                // Affichage.
                $this->vue = new Vue($this, 'Liste', 'Liste des matières');
                $this->vue->render($mats);

            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche les détails d'une matière.
         * @param $id string l'identifiant de la matière.
         */
        public function details($id) {
            if (Utilisateur::estConnecte()) {

                // Récupère toutes les matières.
                $mat = DAO_Factory::getDAO_Matiere()->find($id);
                $cours = DAO_Factory::getDAO_Matiere()->findCours($id);

                // Affichage.
                $this->vue = new Vue($this, 'Details', $mat->getNom());
                $this->vue->render(array($mat, $cours));

            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche la page de creation d'une matière.
         */
        public function creation() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
            ) {

                $this->vue = new Vue($this, "Creation", "Créer une matière");
                $this->vue->render();

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Créé un département dans la base de données.
         */
        public function creer() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
            ) {
                // Si les données de création sont bien présentes.
                if (isset($_POST) && isset($_POST['nom'])) {
                    $mat = DAO_Factory::getDAO_Matiere()
                                      ->insert(new Matiere(null,
                                                           $_POST['nom']));
                    // Département créé ?
                    if ($mat != false) {
                        self::redirect('/SatellysReborn/matiere/details/' .
                                       $mat->getId());
                    } else {
                        self::redirect('/SatellysReborn/matiere/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/matiere/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Modifie la matière.
         * @param $id string l'identifiant de la matière.
         */
        public function modifier($id) {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
            ) {
                if (isset($id)) {
                    if (!isset($_POST['nom'])) {
                        self::redirect('/SatellysReborn/matiere/datails/' .
                                       $id . '/');
                    }

                    // Recherche la matiere à modifier.
                    $mat = DAO_Factory::getDAO_Matiere()->find($id);

                    // Matiere existe ?
                    if ($mat == false) {
                        self::redirect('/SatellysReborn/matiere/inconnu/');
                    }

                    // Fait le modification.
                    if (DAO_Factory::getDAO_Matiere()
                                   ->update(new Matiere($id, $_POST['nom']))
                    ) {
                        self::redirect('/SatellysReborn/matiere/datails/' .
                                       $id . '/');
                    } else {
                        self::redirect('/SatellysReborn/matiere/errModifier/');
                    }
                } else {
                    self::redirect('/SatellysReborn/matiere/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Supprime un département de la base de données s'il n'a plus de
         * promotions qui dépendent de lui.
         * @param $id string l'identifiant du département.
         */
        public function supprimer($id) {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin()
            ) {
                if (isset($id)) {

                    // Récupère la matiere à supprimer.
                    $mat = DAO_Factory::getDAO_Matiere()->find($id);

                    // Département existe ?
                    if ($mat == false) {
                        self::redirect('/SatellysReborn/matiere/inconnu/');
                    }

                    // Cette matière a des cours.
                    if (DAO_Factory::getDAO_Matiere()->findCours($id) != []) {
                        self::redirect('/SatellysReborn/matiere/errCours/');
                    }

                    // Supprime.
                    if (DAO_Factory::getDAO_Matiere()->delete($mat)) {
                        self::redirect('/SatellysReborn/matiere/');
                    } else {
                        self::redirect('/SatellysReborn/matiere/errSupprimer/');
                    }
                } else {
                    self::redirect('/SatellysReborn/matiere/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche les matières dont le nom correspond à celui passé en
         * argument.
         * @param $nom string l'argument de recherche.
         */
        public function listeJSON($nom) {
            echo json_encode(DAO_Factory::getDAO_Matiere()->findNomId($nom));
        }

        /**
         * Affiche l'erreur quand une matière n'existe pas.
         */
        public function inconnu() {
            $this->vue = new Vue($this, 'Inconnu', 'Matière Inconnu');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la suppression
         * d'une matière.
         */
        public function errSupprimer() {
            $this->vue =
                new Vue($this, 'ErrSupprimer', 'Erreur dans la suppression');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la modification
         * d'une matière.
         */
        public function errModifier() {
            $this->vue =
                new Vue($this, 'ErrModifier', 'Erreur dans la modification');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand la matière ne peut être supprimer car il
         * contient encore des cours.
         */
        public function errCours() {
            $this->vue =
                new Vue($this, 'ErrCours',
                        'Erreur cette matière contient des cours');
            $this->vue->render();
        }
    }