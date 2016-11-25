<?php
    namespace SatellysReborn\Modeles\Population;

    use SatellysReborn\Modeles\Population\Adresse\Adresse;

    /**
     * Représente un étudiant.
     * @package SatellysReborn\Modeles\Population
     */
    class Etudiant extends Personne implements \JsonSerializable {

        /** @var string le numéro INE de cet étudiant. */
        private $ine;

        /** @var string adresse mail de l'étudiant */
        //private $email;

        /**
         * Créé une nouvelle personne.
         * @param string $id l'identifiant de la personne.
         * @param string $ine le numéro INE de cet étudiant.
         * @param string $nom le nom de la personne
         * @param string $prenom le prénom de la personne.
         * @param string $tel le téléphone de la personne.
         * @param string $email l'email de l personne.
         * @param Adresse $adresse l'adresse de l'étudiant.
         */
        public function __construct($id, $ine, $nom, $prenom, $tel,
                                    $email, $adresse) {
            parent::__construct($id, $nom, $prenom, $tel, $email, $adresse);

            $this->ine = $ine;
        }

        /**
         * @return string le numéro INE de cet étudiant.
         */
        public function getIne() {
            return $this->ine;
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