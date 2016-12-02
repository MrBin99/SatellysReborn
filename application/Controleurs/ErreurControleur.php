<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur gérant les erreurs du site.
     * @package SatellysReborn\Controleurs
     */
    class ErreurControleur extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            self::redirect('/SatellysReborn/erreur/erreur404');
        }

        /**
         * Affiche la page d'erreur 404.
         */
        public function erreur404() {
            $this->vue = new Vue($this, "Erreur404", "Erreur 404");
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur 42.
         */
        public function erreur42() {
            $this->vue = new Vue($this, "Erreur42", "Erreur 42");
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur de connexion à la base de données.
         */
        public function erreurBD() {
            $this->vue =
                new Vue($this, "ErreurBD", "Erreur avec la base de données.");
            $this->vue->render();
        }
    }