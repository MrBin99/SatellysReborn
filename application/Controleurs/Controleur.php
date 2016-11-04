<?php
    namespace WS_SatellysReborn\Controleurs;

    /**
     * Représente le contrôleur d'une page Web.
     * @package WS_SatellysReborn\Controleurs
     */
    abstract class Controleur {

        /** @var string le titre de la page. */
        private $titre = SITE_NAME;

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public abstract function index();

        /**
         * Modifie le titre de la page.
         * @param string $titre le nouveau titre de la page.
         */
        public function setTitre($titre) {
            $this->titre = SITE_NAME . ' - ' . $titre;
        }

        /**
         * @return string le titre de la page.
         */
        public function getTitre() {
            return $this->titre;
        }
    }