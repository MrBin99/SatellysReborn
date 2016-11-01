<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO;

    use WS_SatellysReborn\BaseDonnees\DAO\Cours\DAO_Matiere;
    use WS_SatellysReborn\BaseDonnees\DAO\Cours\DAO_Salle;
    use WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse\DAO_Adresse;
    use WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse\DAO_Pays;
    use WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse\DAO_Ville;
    use WS_SatellysReborn\BaseDonnees\DAO\Population\DAO_Etudiant;
    use WS_SatellysReborn\BaseDonnees\DAO\Population\Groupe\DAO_Departement;
    use WS_SatellysReborn\BaseDonnees\DAO\Population\Groupe\DAO_Groupe;
    use WS_SatellysReborn\BaseDonnees\DAO\Population\Groupe\DAO_Promotion;

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
         * @return DAO_Departement un nouveau DAO pour les départements de
         *     l'IUT.
         */
        public static function getDAO_Departement() {
            return new DAO_Departement();
        }

        /**
         * @return DAO_Ville un nouveau DAO pour les villes.
         */
        public static function getDAO_Ville() {
            return new DAO_Ville();
        }

        /**
         * @return DAO_Adresse un nouveau DAO pour les adresses.
         */
        public static function getDAO_Adresse() {
            return new DAO_Adresse();
        }

        /**
         * @return DAO_Matiere un nouveau DAO pour les matières.
         */
        public static function getDAO_Matiere() {
            return new DAO_Matiere();
        }

        /**
         * @return DAO_Salle un nouveau DAO pour les salles de l'IUT.
         */
        public static function getDAO_Salle() {
            return new DAO_Salle();
        }

        /**
         * @return DAO_Promotion un nouveau DAO pour les promotions de l'IUT.
         */
        public static function getDAO_Promotion() {
            return new DAO_Promotion();
        }

        /**
         * @return DAO_Groupe un nouveau DAO pour les groupes d'étudiants.
         */
        public static function getDAO_Groupe() {
            return new DAO_Groupe();
        }

        /**
         * @return DAO_Etudiant un nouveau DAO pour les étudiants de l'IUT.
         */
        public static function getDAO_Etudiant() {
            return new DAO_Etudiant();
        }
    }