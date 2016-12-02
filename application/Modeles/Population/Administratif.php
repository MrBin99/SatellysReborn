<?php
    namespace SatellysReborn\Modeles\Population;

    use SatellysReborn\Modeles\Population\Adresse\Adresse;

    /**
     * Représente une personne faisant partie de l'administration.
     * @package SatellysReborn\Modeles\Population
     */
    class Administratif extends Personne implements \JsonSerializable {

        /** @var string le poste de l'administratif. */
        private $poste;

        /**
         * Créé un nouvel administratif.
         * @param string $id l'identifiant de la personne.
         * @param string $nom le nom de la personne
         * @param string $prenom le prénom de la personne.
         * @param string $tel le téléphone de la personne.
         * @param string $poste le poste de la personne.
         * @param Adresse $adresse l'adresse de l'neseignant.
         */
        public function __construct($id, $nom, $prenom, $tel, $poste,
                                    Adresse $adresse) {
            parent::__construct($id, $nom, $prenom, $tel, $adresse);
            $this->poste = $poste;
        }

        /**
         * @return string le poste de l'administratif.
         */
        public function getPoste() {
            return $this->poste;
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