<?php
    namespace WS_SatellysReborn\Modeles\Cours;

    use WS_SatellysReborn\Modeles\Modele;

    /**
     * Représente une salle de cours.
     * @package WS_SatellysReborn\Modeles\Cours
     */
    class Salle extends Modele {

        /** @var string le numéro de la salle. */
        private $num;

        /** @var int la capacité de la salle. */
        private $capacite;

        /** @var bool si la salle est une salle machine. */
        private $machine;

        /**
         * Créé une nouvelle salle de cours.
         * @param string $num le numéro de cette salle.
         * @param int $capacite la capacité de cette salle.
         * @param bool $machine si la salle est une salle machine.
         */
        public function __construct($num, $capacite, $machine) {
            $this->num = $num;
            $this->capacite = $capacite;
            $this->machine = $machine;
        }

        /**
         * @return string le numéro de la salle.
         */
        public function getNum() {
            return $this->num;
        }

        /**
         * @return int la capacité de la salle.
         */
        public function getCapacite() {
            return $this->capacite;
        }

        /**
         * @return boolean si la salle est une salle machine.
         */
        public function estSalleMachine() {
            return $this->machine;
        }
    }