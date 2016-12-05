<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Groupe\Departement;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les départements.
     * @package SatellysReborn\Controleurs
     */
    class DepartementControleur extends Controleur {

        /**
         * Affiche la liste de tous les départements.
         */
        public function index() {
            // Droits.
            if (Utilisateur::estConnecte()) {

                // Récupère tous les départements.
                $deps = DAO_Factory::getDAO_Departement()->findAll();

                // Affichage.
                $this->vue = new Vue($this, 'Liste', 'Liste des départements');
                $this->vue->render($deps);

            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche les détails d'un département.
         * @param $id string l'identifiant du département
         */
        public function details($id) {
            // Droits.
            if (Utilisateur::estConnecte()) {
                if (isset($id)) {
                    // Récupère le département et ses promotions.
                    $dep = DAO_Factory::getDAO_Departement()->find($id);

                    if (isset($dep)) {
                        $promos =
                            DAO_Factory::getDAO_Promotion()
                                       ->findDepartement($id);

                        // Affichage.
                        $this->vue =
                            new Vue($this, 'Details',
                                    'Département ' . $dep->getNom());
                        $this->vue->render(array($dep, $promos));

                    } else {
                        self::redirect('/SatellysReborn/departement/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/departement/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche la page de creation d'un département.
         */
        public function creation() {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin() ||
                Utilisateur::utilCourantEstSuperAdmin()
            ) {

                $this->vue = new Vue($this, "Creation", "Créer un département");
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
            if (Utilisateur::utilCourantEstAdmin() ||
                Utilisateur::utilCourantEstSuperAdmin()
            ) {
                // Si les données de création sont bien présentes.
                if (isset($_POST) && isset($_POST['nom'])) {
                    $dep = DAO_Factory::getDAO_Departement()
                                      ->insert(new Departement(null,
                                                               $_POST['nom']));
                    // Département créé ?
                    if ($dep != false) {
                        self::redirect('/SatellysReborn/departement/details/' .
                                       $dep->getId());
                    } else {
                        self::redirect('/SatellysReborn/departement/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/departement/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Modifie le département.
         * @param $id string l'identifiant du département.
         */
        public function modifier($id) {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin() ||
                Utilisateur::utilCourantEstSuperAdmin()
            ) {
                if (isset($id)) {
                    if (!isset($_POST['nom'])) {
                        self::redirect('/SatellysReborn/departement/datails/' .
                                       $id . '/');
                    }

                    // Recherche le département à modifier.
                    $dep = DAO_Factory::getDAO_Departement()->find($id);

                    // Département existe ?
                    if ($dep == false) {
                        self::redirect('/SatellysReborn/departement/inconnu/');
                    }

                    // Fait le modification.
                    if (DAO_Factory::getDAO_Departement()
                                   ->update(new Departement($id, $_POST['nom']))
                    ) {
                        self::redirect('/SatellysReborn/departement/details/' .
                                       $id);
                    } else {
                        self::redirect('/SatellysReborn/departement/errModifier/');
                    }
                } else {
                    self::redirect('/SatellysReborn/departement/inconnu/');
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
            if (Utilisateur::utilCourantEstAdmin() ||
                Utilisateur::utilCourantEstSuperAdmin()
            ) {
                if (isset($id)) {

                    // Récupère le département à supprimer.
                    $dep = DAO_Factory::getDAO_Departement()->find($id);

                    // Département existe ?
                    if ($dep == false) {
                        self::redirect('/SatellysReborn/departement/inconnu/');
                    }

                    // Les promos.
                    $promos = DAO_Factory::getDAO_Promotion()
                                         ->findDepartement($id);

                    // Plus de promos pour ce département.
                    if ($promos == []) {

                        // Supprime.
                        if (DAO_Factory::getDAO_Departement()->delete($dep)) {
                            self::redirect('/SatellysReborn/departement/');
                        } else {
                            self::redirect('/SatellysReborn/departement/errSupprimer/');
                        }
                    } else {
                        self::redirect('/SatellysReborn/departement/errPromotion/');
                    }
                } else {
                    self::redirect('/SatellysReborn/departement/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la liste des départements dont le nom correspond à celui
         * passé en argument au format JSON.
         * @param $nom string le nom du département.
         */
        public function listeJSON($nom) {
            echo json_encode(DAO_Factory::getDAO_Departement()->findNom($nom));
        }

        /**
         * Affiche l'erreur quand un département n'existe pas.
         */
        public function inconnu() {
            $this->vue = new Vue($this, 'Inconnu', 'Département Inconnu');
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
         * d'un département.
         */
        public function errModifier() {
            $this->vue =
                new Vue($this, 'ErrModifier', 'Erreur dans la modification');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand le département ne peut être supprimer car il
         * contient encore des promotions.
         */
        public function errPromotion() {
            $this->vue =
                new Vue($this, 'ErrPromotions',
                        'Erreur ce département contient de promotions');
            $this->vue->render();
        }
    }