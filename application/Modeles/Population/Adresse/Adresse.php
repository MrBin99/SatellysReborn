<?php
    namespace SatellysReborn\Modeles\Population\Adresse;

    use SatellysReborn\Modeles\Modele;

    /**
     * Représente l'adresse d'une personne.
     * @package SatellysReborn\Modeles\Population\Adresse
     */
    class Adresse extends Modele {

        /** @var string l'identifiant de l'adresse. */
        private $id;

        /** @var string la 1ere partie de l'adresse. */
        private $adresse1;

        /** @var string la 2e partie de l'adresse. */
        private $adresse2;

        /** @var string la 3e partie de l'adresse. */
        private $adresse3;

        /** @var Ville la ville de l'adresse. */
        private $ville;

        /**
         * Créé une nouvelle adresse pour une personne.
         * @param string $id l'identifiant de l'adresse.
         * @param string $adresse1 la 1ere partie de l'adresse.
         * @param string $adresse2 la 2e partie de l'adresse.
         * @param string $adresse3 la 3e partie de l'adresse.
         * @param Ville $ville la ville de l'adresse.
         */
        public function __construct($id = null, $adresse1, $adresse2, $adresse3,
                                    Ville $ville) {
            $this->id = $id;
            $this->adresse1 = $adresse1;
            $this->adresse2 = $adresse2;
            $this->adresse3 = $adresse3;
            $this->ville = $ville;
        }

        /**
         * @return string l'identifiant de l'adresse.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return string la 1ere partie de l'adresse.
         */
        public function getAdresse1() {
            return $this->adresse1;
        }

        /**
         * @return string la 2e partie de l'adresse.
         */
        public function getAdresse2() {
            return $this->adresse2;
        }

        /**
         * @return string la 3e partie de l'adresse.
         */
        public function getAdresse3() {
            return $this->adresse3;
        }

        /**
         * @return Ville la ville de l'adresse.
         */
        public function getVille() {
            return $this->ville;
        }
    }