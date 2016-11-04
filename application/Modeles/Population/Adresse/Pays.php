<?php
    namespace WS_SatellysReborn\Modeles\Population\Adresse;

    use WS_SatellysReborn\Modeles\Modele;

    /**
     * Représente un pays contenu dans une adresse.
     * @package WS_SatellysReborn\Modeles\Population\Adresse
     */
    class Pays extends Modele {

        /** @var string l'identifiant du pays. */
        private $id;

        /** @var string le nom du pays. */
        private $nom;

        /**
         * Créé un nouveau pays pour une adresse.
         * @param string $id l'identifiant du pays.
         * @param string $nom le nom du pays.
         */
        public function __construct($id = null, $nom) {
            $this->id = $id;
            $this->nom = $nom;
        }

        /**
         * @return string l'identifiant du pays.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return string le nom du pays.
         */
        public function getNom() {
            return $this->nom;
        }
    }