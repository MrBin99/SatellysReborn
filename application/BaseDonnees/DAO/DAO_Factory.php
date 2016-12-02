<?php
    namespace SatellysReborn\BaseDonnees\DAO;

    use SatellysReborn\BaseDonnees\DAO\Cours\DAO_Absence;
    use SatellysReborn\BaseDonnees\DAO\Cours\DAO_Cours;
    use SatellysReborn\BaseDonnees\DAO\Cours\DAO_Matiere;
    use SatellysReborn\BaseDonnees\DAO\Population\Adresse\DAO_Adresse;
    use SatellysReborn\BaseDonnees\DAO\Population\Adresse\DAO_Pays;
    use SatellysReborn\BaseDonnees\DAO\Population\Adresse\DAO_Ville;
    use SatellysReborn\BaseDonnees\DAO\Population\DAO_Administratif;
    use SatellysReborn\BaseDonnees\DAO\Population\DAO_Enseignant;
    use SatellysReborn\BaseDonnees\DAO\Population\DAO_Etudiant;
    use SatellysReborn\BaseDonnees\DAO\Population\Groupe\DAO_Departement;
    use SatellysReborn\BaseDonnees\DAO\Population\Groupe\DAO_Groupe;
    use SatellysReborn\BaseDonnees\DAO\Population\Groupe\DAO_Promotion;
    use SatellysReborn\BaseDonnees\DAO\Population\Login\DAO_Utilisateur;

    /**
     * Fabrique de DAO.
     * @package SatellysReborn\BaseDonnees\DAO
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
         * @return DAO_Cours un nouveau DAO pour les cours.
         */
        public static function getDAO_Cours() {
            return new DAO_Cours();
        }

        /**
         * @return DAO_Absence un nouveau DAO pour les absences.
         */
        public static function getDAO_Absence() {
            return new DAO_Absence();
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

        /**
         * @return DAO_Enseignant un nouveau DAO pour les enseignants de l'IUT.
         */
        public static function getDAO_Enseignant() {
            return new DAO_Enseignant();
        }

        /**
         * @return DAO_Administratif un nouveau DAO pour les administratifs de
         *     l'IUT.
         */
        public static function getDAO_Administratif() {
            return new DAO_Administratif();
        }

        /**
         * @return DAO_Utilisateur un nouveau DAO pour les utilisateurs de
         *     l'application.
         */
        public static function getDAO_Utilisateur() {
            return new DAO_Utilisateur();
        }
    }