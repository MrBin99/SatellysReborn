<?php
    namespace WS_SatellysReborn\Controleurs;

    /**
     * Représente le contrôleur d'une page Web.
     * @package WS_SatellysReborn\Controleurs
     */
    abstract class Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public abstract function index();
    }