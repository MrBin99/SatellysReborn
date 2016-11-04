<?php
    namespace WS_SatellysReborn\Controleurs;

    /**
     * Le contrôleur gérant les erreurs 404.
     * @package WS_SatellysReborn\Controleurs
     */
    class Erreur404 extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            $this->setTitre('Erreur 404');
            include_once VUES . 'Erreurs/Erreur404.phtml';
        }
    }