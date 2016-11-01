<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO;

    use WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse\DAO_Pays;
    use WS_SatellysReborn\BaseDonnees\DAO\Population\Groupe\DAO_Departement;

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

        /**
         * @return DAO_Departement un nouveau DAO pour départements de l'IUT.
         */
        public static function getDAO_Departement() {
            return new DAO_Departement();
        }
    }