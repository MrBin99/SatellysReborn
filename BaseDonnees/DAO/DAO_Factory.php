<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO;

    use WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse\DAO_Pays;

    /**
     * Fabrique de DAO.
     * @package WS_SatellysReborn\BaseDonnees\DAO
     */
    final class DAO_Factory {

        /** Classe non instantiable. */
        private function __construct() {
        }

        /**
         * @return DAO_Pays un nouveau DAO pour les pays des adresses.
         */
        public static function getDAO_Pays() {
            return new DAO_Pays();
        }
    }