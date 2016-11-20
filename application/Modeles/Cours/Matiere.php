<?php
    namespace SatellysReborn\Modeles\Cours;

    use SatellysReborn\Modeles\Modele;

    /**
     * Représente une matière enseigné par un professeur.
     * @package SatellysReborn\Modeles\Cours
     */
    class Matiere extends Modele implements \JsonSerializable {

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