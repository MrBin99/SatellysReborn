<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur gérant les pages des différentes erreurs HTTP.
     * @package SatellysReborn\Controleurs
     */
    class Erreur extends Controleur {

        /**
         * Affiche la page d'erreur 404.
         */
        public function index() {
            self::redirect('/SatellysReborn/erreur/erreur404');
        }

        /**
         * Affiche la page d'erreur 404.
         */
        public function erreur404() {
            $this->vue = new Vue($this, 'Erreur404', 'Erreur 404');
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur 42.
         */
        public function erreur42() {
            $this->vue = new Vue($this, 'Erreur42', 'Erreur 42');
            $this->vue->render();
        }
    }