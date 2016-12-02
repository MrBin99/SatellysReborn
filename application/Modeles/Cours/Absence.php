<?php
    namespace SatellysReborn\Modeles\Cours;

    use SatellysReborn\Modeles\Population\Etudiant;

    /**
     * Représente une absence à un cours.
     * @package SatellysReborn\Modeles\Cours
     */
    class Absence {

        /** @var Cours le cours sur lequel porte l'absence. */
        private $cours;

        /** @var Etudiant l'étudiant absent. */
        private $etudiant;

        /** @var bool si l'absence est justifié ou non. */
        private $justifie;

        /** @var string le motif de l'absence. */
        private $motif;

        /**
         * Créé une nouvelle absence à un cours.
         * @param Cours $cours le cours sur lequel porte l'absence.
         * @param Etudiant $etudiant l'étudiant absent.
         * @param bool $justifie l'absence est justifié ou non.
         * @param string $motif le motif de l'absence.
         */
        public function __construct(Cours $cours, Etudiant $etudiant, $justifie,
                                    $motif) {
            $this->cours = $cours;
            $this->etudiant = $etudiant;
            $this->justifie = $justifie;
            $this->motif = $motif;
        }

        /**
         * @return Cours le cours sur lequel porte l'absence.
         */
        public function getCours() {
            return $this->cours;
        }

        /**
         * @return Etudiant l'étudiant absent.
         */
        public function getEtudiant() {
            return $this->etudiant;
        }

        /**
         * @return boolean l'absence est justifié ou non.
         */
        public function estJustifie() {
            return $this->justifie;
        }

        /**
         * @return string le motif de l'absence.
         */
        public function getMotif() {
            return $this->motif;
        }
    }