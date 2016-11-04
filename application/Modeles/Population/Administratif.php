<?php
    namespace WS_SatellysReborn\Modeles\Population;

    use WS_SatellysReborn\Modeles\Population\Adresse\Adresse;

    /**
     * Représente une personne faisant partie de l'administration.
     * @package WS_SatellysReborn\Modeles\Population
     */
    class Administratif extends Personne {

        /** @var string le poste de l'administratif. */
        private $poste;

        /**
         * Créé un nouvel administratif.
         * @param string $id l'identifiant de la personne.
         * @param string $nom le nom de la personne
         * @param string $prenom le prénom de la personne.
         * @param string $tel le téléphone de la personne.
         * @param string $email l'email de l personne.
         * @param string $poste le poste de la personne.
         * @param Adresse $adresse l'adresse de l'neseignant.
         */
        public function __construct($id, $nom, $prenom, $tel, $email, $poste,
                                    Adresse $adresse) {
            parent::__construct($id, $nom, $prenom, $tel, $email, $adresse);
            $this->poste = $poste;
        }

        /**
         * @return string le poste de l'administratif.
         */
        public function getPoste() {
            return $this->poste;
        }
    }