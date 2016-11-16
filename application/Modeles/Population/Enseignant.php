<?php
    namespace SatellysReborn\Modeles\Population;

    use SatellysReborn\Modeles\Population\Adresse\Adresse;

    /**
     * Représente un enseignant.
     * @package SatellysReborn\Modeles\Population
     */
    class Enseignant extends Personne {

        /**
         * Créé un nouvel enseignant.
         * @param string $id l'identifiant de la personne.
         * @param string $nom le nom de la personne
         * @param string $prenom le prénom de la personne.
         * @param string $tel le téléphone de la personne.
         * @param string $email l'email de l personne.
         * @param Adresse $adresse l'adresse de l'neseignant.
         */
        public function __construct($id, $nom, $prenom, $tel, $email,
                                    $adresse) {
            parent::__construct($id, $nom, $prenom, $tel, $email, $adresse);
        }
    }