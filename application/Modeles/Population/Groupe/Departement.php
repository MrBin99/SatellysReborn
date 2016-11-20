<?php
    namespace SatellysReborn\Modeles\Population\Groupe;

    use SatellysReborn\Modeles\Modele;

    /**
     * Représente un département de l'IUT.
     * @package SatellysReborn\Modeles\Population\Groupe
     */
    class Departement extends Modele implements \JsonSerializable {

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
                if (is_object($value) && method_exists($value,'jsonSerialize')) {
                    $value = $value->jsonSerialize();
                }
            }
            return $var;
        }
    }