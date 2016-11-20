<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Vues\Vue;

    class Enseignant extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            $this->vue = new Vue($this, "EmploiTemps");
            $this->vue->render();
        }

        public function cours($id) {
            $cours = DAO_Factory::getDAO_Cours()->findCoursEnseignant($id);
            echo json_encode($cours);
        }
    }