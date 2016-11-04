<?php
    namespace WS_SatellysReborn\Modeles\Cours;

    use WS_SatellysReborn\Modeles\Modele;

    /**
     * Représente une matière enseigné par un professeur.
     * @package WS_SatellysReborn\Modeles\Cours
     */
    class Matiere extends Modele {

        /** @var string l'identifiant de la matière. */
        private $id;

        /** @var string le nom de la matière. */
        private $nom;

        /**
         * Créé une nouvelle matière enseigné.
         * @param string $id l'identification de la matière.
         * @param string $nom le nom de la matière.
         */
        public function __construct($id, $nom) {
            $this->id = $id;
            $this->nom = $nom;
        }

        /**
         * @return string l'identifiant de la matière.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return string le nom de la matière.
         */
        public function getNom() {
            return $this->nom;
        }
    }