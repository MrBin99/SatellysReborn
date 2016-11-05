<?php
    namespace WS_SatellysReborn\Controleurs;

    use WS_SatellysReborn\Vues\Vue;

    /**
     * Controleur de la page d'accueil.
     * @package WS_SatellysReborn\Controleurs
     */
    class Accueil extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            $this->vue = new Vue($this, 'Accueil');
            $this->vue->render();
        }
    }