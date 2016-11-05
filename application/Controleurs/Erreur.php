<?php
    namespace WS_SatellysReborn\Controleurs;

    use WS_SatellysReborn\Vues\Vue;

    /**
     * Le contrôleur gérant les erreurs.
     * @package WS_SatellysReborn\Controleurs
     */
    class Erreur extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            self::redirect('erreur/erreur404');
        }

        /**
         * Affiche la page d'erreur 404.
         */
        public function erreur404() {
            $this->vue = new Vue($this, 'Erreur404');
            $this->vue->render();
        }

        /**
         * Affiche la page d'erreur 42.
         */
        public function erreur42() {
            $this->vue = new Vue($this, 'Erreur42');
            $this->vue->render();
        }
    }