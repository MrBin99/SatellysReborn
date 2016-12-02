<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\Vues\Vue;

    /**
     * Représente le contrôleur d'une page Web.
     * @package SatellysReborn\Controleurs
     */
    abstract class Controleur {

        /** @var Vue la vue de la page courante. */
        protected $vue;

        public function __construct() {
            session_start();
        }

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public abstract function index();

        /**
         * Redirige vers la page passé en argument.
         * @param $page string la page vers laquelle l'internaute doit être
         *     redirigé.
         */
        public static function redirect($page) {
            header('Location: ' . $page);
        }
    }