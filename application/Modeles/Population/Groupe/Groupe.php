<?php
    namespace SatellysReborn\Modeles\Population\Groupe;

    use SatellysReborn\Modeles\Modele;
    use SatellysReborn\Modeles\Population\Etudiant;

    /**
     * Représente un groupe d'étudiant faisant partie d'une promotion.
     * @package SatellysReborn\Modeles\Population\Groupe
     */
    class Groupe extends Modele implements \JsonSerializable {

        /** @var string l'identifiant du groupe. */
        private $id;

        /** @var string le nom du groupe. */
        private $nom;

        /** @var Promotion la promotion dont le groupe fait partie. */
        private $promo;

        /** @var array la liste des étudiants de ce groupe. */
        private $etudiants;

        /**
         * Créé un nouveau groupe d'étudiants.
         * @param string $id l'identifiant du groupe.
         * @param string $nom le nom du groupe.
         * @param Promotion $promo la promotion dont le groupe fait partie.
         */
        public function __construct($id = null, $nom, Promotion $promo) {
            $this->id = $id;
            $this->nom = $nom;
            $this->promo = $promo;
            $this->etudiants = array();
        }

        /**
         * Ajoute l'étudiant au groupe.
         * @param $etudiant Etudiant l'étudiant à ajouter.
         */
        public function ajouterEtudiant($etudiant) {
            array_push($this->etudiants, $etudiant);
        }

        /**
         * @return string l'identifiant du groupe.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return string le nom du groupe.
         */
        public function getNom() {
            return $this->nom;
        }

        /**
         * @return Promotion la promotion dont le groupe fait partie.
         */
        public function getPromo() {
            return $this->promo;
        }

        /**
         * @return array la liste des étudiants de ce groupe.
         */
        public function getEtudiants() {
            return $this->etudiants;
        }

        /**
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        public function jsonSerialize() {
            $var = get_object_vars($this);
            foreach ($var as &$value) {
                if (is_object($value) &&
                    method_exists($value, 'jsonSerialize')
                ) {
                    $value = $value->jsonSerialize();
                }
            }

            return $var;
        }
    }