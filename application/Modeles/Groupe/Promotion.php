<?php
    namespace SatellysReborn\Modeles\Population\Groupe;

    use SatellysReborn\Modeles\Modele;

    /**
     * Représente la promotion d'un étudiant.
     * @package SatellysReborn\Modeles\Population\Groupe
     */
    class Promotion extends Modele {

        /** @var string l'identifiant de la promotion. */
        private $id;

        /** @var string le nom de la promotion. */
        private $nom;

        /** @var string l'année scolaire de la promotion. */
        private $annee;

        /** @var Departement le département dont fait partie la promotion. */
        private $departement;

        /**
         * Créé une nouvelle promotion.
         * @param string $id l'identifiant de la promotion.
         * @param string $nom le nom de la promotion.
         * @param string $annee l'année scolaire de la promotion.
         * @param Departement $departement le département dont fait partie la
         *     promotion.
         */
        public function __construct($id = null, $nom, $annee,
                                    Departement $departement) {
            $this->id = $id;
            $this->nom = $nom;
            $this->annee = $annee;
            $this->departement = $departement;
        }

        /**
         * @return string l'identifiant de la promotion.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return string le nom de la promotion.
         */
        public function getNom() {
            return $this->nom;
        }

        /**
         * @return string l'année scolaire de la promotion.
         */
        public function getAnnee() {
            return $this->annee;
        }

        /**
         * @return Departement le département dont fait partie la
         *     promotion.
         */
        public function getDepartement() {
            return $this->departement;
        }
    }