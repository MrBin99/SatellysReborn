<?php
    namespace WS_SatellysReborn\Modeles\Population;

    use WS_SatellysReborn\Modeles\Exceptions\DonneesIncorrecteException;
    use WS_SatellysReborn\Modeles\Modele;
    use WS_SatellysReborn\Modeles\Population\Adresse\Adresse;

    /**
     * Représente une personne utilisant l'application.
     * @package WS_SatellysReborn\Modeles\Population
     */
    abstract class Personne extends Modele {

        /** @var string le regex représentant un email. */
        public static $REGEX_EMAIL = '/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/';

        /** @var string l'identifiant UT1 de la personne. */
        protected $id;

        /** @var string le nom de la personne. */
        protected $nom;

        /** @var string le prenom de la personne. */
        protected $prenom;

        /** @var string le téléphone de la personne. */
        protected $tel;

        /** @var string l'email de la personne. */
        protected $email;

        /** @var Adresse l'adresse de la personne. */
        protected $adresse;

        /**
         * Créé une nouvelle personne.
         * @param string $id l'identifiant de la personne.
         * @param string $nom le nom de la personne
         * @param string $prenom le prénom de la personne.
         * @param string $tel le téléphone de la personne.
         * @param string $email l'email de l personne.
         * @param Adresse $adresse l'adresse de la personne.
         * @throws DonneesIncorrecteException si les données pour un créer une
         *     une personne sont incorrectes.
         */
        public function __construct($id, $nom, $prenom, $tel, $email,
                                    $adresse) {
            // Vérifie l'ID
            if (strlen($id) != 13) {
                throw new DonneesIncorrecteException("L'identifiant d'une 
                    personne doit être composé de 13 chiffres.");
            }

            // Vérifie le téléphone.
            if (strlen($tel) != 10 || !is_numeric($tel)) {
                throw new DonneesIncorrecteException("Un numéro de téléphone 
                    doit être composé de 10 chiffres.");
            }

            // Vérifie l'email.
            if (preg_match(self::$REGEX_EMAIL, $email) != 1) {
                throw new DonneesIncorrecteException("L'email donné n'est pas
                    un email valide.");
            }

            $this->id = $id;
            $this->nom = $nom;
            $this->prenom = $prenom;
            $this->tel = $tel;
            $this->email = $email;
            $this->adresse = $adresse;
        }

        /**
         * @return string l'identifiant UT1 de la personne.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return string le nom de la personne.
         */
        public function getNom() {
            return $this->nom;
        }

        /**
         * @return string le prénom de la personne.
         */
        public function getPrenom() {
            return $this->prenom;
        }

        /**
         * @return string le téléphone de la personne.
         */
        public function getTel() {
            return $this->tel;
        }

        /**
         * @return string l'email de la personne.
         */
        public function getEmail() {
            return $this->email;
        }

        /**
         * @return Adresse l'adresse de la personne.
         */
        public function getAdresse() {
            return $this->adresse;
        }
    }