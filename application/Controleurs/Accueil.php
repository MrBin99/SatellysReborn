<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur de la page d'accueil.
     * @package SatellysReborn\Controleurs
     */
    class Accueil extends Controleur {

        /**
         * Affiche la page d'accueil.
         */
        public function index() {
            $this->vue = new Vue($this, 'Index', 'Accueil');
            $this->vue->render();
        }
    }