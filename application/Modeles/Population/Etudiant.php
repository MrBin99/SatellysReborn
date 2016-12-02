<?php
    namespace SatellysReborn\Modeles\Population;

    use SatellysReborn\Modeles\Exceptions\DonneesIncorrecteException;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;

    /**
     * Représente un étudiant.
     * @package SatellysReborn\Modeles\Population
     */
    class Etudiant extends Personne implements \JsonSerializable {

        /** @var string le numéro INE de cet étudiant. */
        private $ine;

        /** @var string l'email de l'étudiant. */
        private $email;

        /**
         * Créé une nouvelle personne.
         * @param string $id l'identifiant de la personne.
         * @param string $ine le numéro INE de cet étudiant.
         * @param string $nom le nom de la personne
         * @param string $prenom le prénom de la personne.
         * @param string $tel le téléphone de la personne.
         * @param string $email l'email de l personne.
         * @param Adresse $adresse l'adresse de l'étudiant.
         * @throws DonneesIncorrecteException si une erreur dans les arguments
         *     est détectée (ex: email invalide, INE invalide ...).
         */
        public function __construct($id, $ine, $nom, $prenom, $tel,
                                    $email, $adresse) {
            parent::__construct($id, $nom, $prenom, $tel, $adresse);

            if (strlen($ine) != 12) {
                throw new DonneesIncorrecteException(
                    "Le numéro INE doit être une série de 12 chiffres ou lettres.");
            }

            // Vérifie l'email.
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new DonneesIncorrecteException(
                    "L'email '$email' n'est pas un email valide.");
            }

            $this->ine = $ine;
            $this->email = $email;
        }

        /**
         * @return string le numéro INE de cet étudiant.
         */
        public function getIne() {
            return $this->ine;
        }

        /**
         * @return string l'email de l'étudiant.
         */
        public function getEmail() {
            return $this->email;
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