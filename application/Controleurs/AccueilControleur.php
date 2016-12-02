<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur de la page d'accueil.
     * @package SatellysReborn\Controleurs
     */
    class AccueilControleur extends Controleur {

        /**
         * Affiche la page d'accueil.
         */
        public function index() {
            if (Utilisateur::utilCourantEstEnseignant()) {
                self::redirect('/SatellysReborn/enseignant/emploiTemps/'
                               . Utilisateur::getUtilisateur()
                                   ->getEnseignant()->getId());
            } else {
                $this->vue = new Vue($this, 'Index', 'Accueil');
                $this->vue->render();
            }
        }
    }