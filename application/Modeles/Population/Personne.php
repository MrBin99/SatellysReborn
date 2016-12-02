<?php
    namespace SatellysReborn\Modeles\Population;

    use SatellysReborn\Modeles\Exceptions\DonneesIncorrecteException;
    use SatellysReborn\Modeles\Modele;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;

    /**
     * Représente une personne utilisant l'application.
     * @package SatellysReborn\Modeles\Population
     */
    abstract class Personne extends Modele {

        /** @var string l'identifiant UT1 de la personne. */
        protected $id;

        /** @var string le nom de la personne. */
        protected $nom;

        /** @var string le prenom de la personne. */
        protected $prenom;

        /** @var string le téléphone de la personne. */
        protected $tel;

        /** @var Adresse l'adresse de la personne. */
        protected $adresse;

        /**
         * Créé une nouvelle personne.
         * @param string $id l'identifiant de la personne.
         * @param string $nom le nom de la personne
         * @param string $prenom le prénom de la personne.
         * @param string $tel le téléphone de la personne.
         * @param Adresse $adresse l'adresse de la personne.
         * @throws DonneesIncorrecteException si les données pour un créer une
         *     une personne sont incorrectes.
         */
        public function __construct($id, $nom, $prenom, $tel,
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

            $this->id = $id;
            $this->nom = $nom;
            $this->prenom = $prenom;
            $this->tel = $tel;
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
         * @return string retourne le nom complet de la personne c'est-à-dire
         * une chaine composée de son nom et prénom.
         */
        public function getNomComplet() {
            return $this->getNom() . ' ' . $this->getPrenom();
        }

        /**
         * @return string le téléphone de la personne.
         */
        public function getTel() {
            return $this->tel;
        }

        /**
         * @return Adresse l'adresse de la personne.
         */
        public function getAdresse() {
            return $this->adresse;
        }
    }