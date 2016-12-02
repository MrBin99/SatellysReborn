<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;

    /**
     * Contrôleur permettant de gérer les villes.
     * @package SatellysReborn\Controleurs
     */
    class VilleControleur extends Controleur {

        public function index() {
        }

        /**
         * Liste les villes au format JSON dont le nom
         * correspond à celui passé en paramètre.
         * @param $nom string l'argument de recherche.
         */
        public function listeJSON($nom) {
            echo json_encode(DAO_Factory::getDAO_Ville()
                                        ->findNom($nom));
        }
    }