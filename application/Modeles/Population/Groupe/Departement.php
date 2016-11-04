<?php
    namespace WS_SatellysReborn\Modeles\Population\Groupe;

    use WS_SatellysReborn\Modeles\Modele;

    /**
     * Représente un département de l'IUT.
     * @package WS_SatellysReborn\Modeles\Population\Groupe
     */
    class Departement extends Modele {

        /** @var string l'identifiant du département. */
        private $id;

        /** @var string le nom du département. */
        private $nom;

        /**
         * Créé un nouveau département.
         * @param string $id l'identifiant du départment.
         * @param string $nom le nom du département.
         */
        public function __construct($id = null, $nom) {
            $this->id = $id;
            $this->nom = $nom;
        }

        /**
         * @return string l'identifiant du département.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return string le nom du département.
         */
        public function getNom() {
            return $this->nom;
        }
    }