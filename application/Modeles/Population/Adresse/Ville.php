<?php
    namespace SatellysReborn\Modeles\Population\Adresse;

    use SatellysReborn\Modeles\Exceptions\DonneesIncorrecteException;
    use SatellysReborn\Modeles\Modele;

    /**
     * Représente une ville contenu dans une adresse.
     * @package SatellysReborn\Modeles\Population\Adresse
     */
    class Ville extends Modele {

        /** @var string le numéro INSEE de la ville. */
        private $numInsee;

        /** @var string le code postal de la vile. */
        private $code_postal;

        /** @var string le nom de la ville. */
        private $nom;

        /** @var Pays le pays dans lequel la ville est présente. */
        private $pays;

        /**
         * Créé une nouvelle ville pour une adresse.
         * @param string $numInsee le numéro INSEE de la ville.
         * @param string $code_postal le code postal de la ville.
         * @param string $nom le nom de la ville.
         * @param Pays $pays le pays dans lequel la ville est présente.
         * @throws DonneesIncorrecteException si les données pour un créer une
         *     une ville sont incorrectes.
         */
        public function __construct($numInsee, $code_postal, $nom, Pays $pays) {
            // Pré-condition
            if (strlen($numInsee) != 5 || strlen($code_postal) != 5
                || !is_numeric($code_postal) || !is_numeric($code_postal)
            ) {
                echo $numInsee . ' ' . $code_postal;
                throw new DonneesIncorrecteException("Le numéro INSEE d'une 
                    ville ou son code postal doit être consitué d'une 
                    série de 5 chiffres.");
            }

            $this->numInsee = $numInsee;
            $this->code_postal = $code_postal;
            $this->nom = $nom;
            $this->pays = $pays;
        }

        /**
         * @return string le numéro INSEE de la ville.
         */
        public function getNumInsee() {
            return $this->numInsee;
        }

        /**
         * @return string le code postal de la ville.
         */
        public function getCodePostal() {
            return $this->code_postal;
        }

        /**
         * @return string le nom de la ville.
         */
        public function getNom() {
            return $this->nom;
        }

        /**
         * @return Pays le pays dans lequel la ville est présente.
         */
        public function getPays() {
            return $this->pays;
        }
    }