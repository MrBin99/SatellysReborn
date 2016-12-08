<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Groupe\Promotion;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les promotions.
     * @package SatellysReborn\Controleurs
     */
    class PromotionControleur extends Controleur {

        /**
         * Affiche la liste de toutes les promotions.
         */
        public function index() {
            if (Utilisateur::estConnecte()) {

                // Récupère toutes les promotions.
                $deps = DAO_Factory::getDAO_Promotion()->findAll();

                // Affichage.
                $this->vue = new Vue($this, 'Liste', 'Liste des promotions');
                $this->vue->render($deps);

            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche les détails d'une promotion.
         * @param $id string l'identifiant de la promotion
         */
        public function details($id) {
            if (Utilisateur::estConnecte()) {
                if (isset($id)) {
                    // Récupère la promotion et ses groupes.
                    $dep = DAO_Factory::getDAO_Promotion()->find($id);

                    if (isset($dep)) {
                        $groupes =
                            DAO_Factory::getDAO_Groupe()
                                       ->findPromotion($id);

                        // Affichage.
                        $this->vue =
                            new Vue($this, 'Details',
                                    'Promotion : ' . $dep->getNom());
                        $this->vue->render(array($dep, $groupes));
                    } else {
                        self::redirect('/SatellysReborn/promotion/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/promotion/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche la page de creation d'une promotion.
         * @param string $id l'identifiant d'un département où la promotion
         * doit être affectée.
         */
        public function creation($id = null) {
            if (Utilisateur::utilCourantEstAdmin()) {

                $dep = null;
                if (isset($id)) {
                    $dep = DAO_Factory::getDAO_Departement()->find($id);
                }

                $this->vue = new Vue($this, "Creation", "Créer une promotion");

                if ($dep == null) {
                    $this->vue->render(array($id));
                } else {
                    $this->vue->render(array($id, $dep));
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
                if (isset($_POST) && isset($_POST['nom']) &&
                    isset($_POST['annee']) && isset($_POST['dep'])
                ) {
                    $dep =
                        DAO_Factory::getDAO_Departement()->find($_POST['dep']);
                    $promo = DAO_Factory::getDAO_Promotion()
                                        ->insert(new Promotion(null,
                                                               $_POST['nom'],
                                                               $_POST['annee'],
                                                               $dep));
                    if ($promo != false) {
                        self::redirect('/SatellysReborn/promotion/details/' .
                                       $promo->getId());
                    } else {
                        self::redirect('/SatellysReborn/promotion/inconnu/');
                    }
                } else {
                    self::redirect('/SatellysReborn/promotion/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Modifie la promotion.
         * @param $id string l'identifiant de la promotion.
         */
        public function modifier($id) {
            if (Utilisateur::utilCourantEstAdmin()) {
                if (isset($id)) {
                    if (!isset($_POST['nom'])) {
                        self::redirect('/SatellysReborn/promotion/datails/' .
                                       $id . '/');
                    }

                    $promo = DAO_Factory::getDAO_Promotion()->find($id);

                    // Promotion existe ?
                    if ($promo == false) {
                        self::redirect('/SatellysReborn/promotion/inconnu/');
                    }

                    if (DAO_Factory::getDAO_Promotion()
                                   ->update(new Promotion($id, $_POST['nom'],
                                                          $_POST['annee'],
                                                          $promo->getDepartement()))
                    ) {
                        self::redirect('/SatellysReborn/promotion/details/' .
                                       $id . '/');
                    } else {
                        self::redirect('/SatellysReborn/promotion/errModifier/');
                    }
                } else {
                    self::redirect('/SatellysReborn/promotion/inconnu/');
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

                    $promo = DAO_Factory::getDAO_Promotion()->find($id);

                    // Promotion existe ?
                    if ($promo == false) {
                        self::redirect('/SatellysReborn/promotion/inconnu/');
                    }

                    // Les groupes.
                    $groupes = DAO_Factory::getDAO_Groupe()
                                          ->findPromotion($id);

                    // Plus de promos pour ce département.
                    if ($groupes == []) {
                        if (DAO_Factory::getDAO_Promotion()->delete($promo)) {
                            $this->vue = new Vue($this, "SupprimerOK",
                                                 "Suppression de la promotion effectuée");
                            $this->vue->render();
                        } else {
                            self::redirect('/SatellysReborn/promotion/errSupprimer/');
                        }
                    } else {
                        self::redirect('/SatellysReborn/promotion/errGroupe/');
                    }
                } else {
                    self::redirect('/SatellysReborn/promotion/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la liste des promotions dont le nom correspond à celui
         * passé en argument au format JSON.
         * @param $nom string le nom de la promotion.
         */
        public function listeJSON($nom) {
            echo json_encode(DAO_Factory::getDAO_Promotion()->findNom($nom));
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
         * Affiche l'erreur quand la promotion ne peut être supprimer car il
         * contient encore des groupes.
         */
        public function errGroupe() {
            $this->vue =
                new Vue($this, 'ErrGroupes',
                        'Erreur cette promotion contient des groupes');
            $this->vue->render();
        }
    }