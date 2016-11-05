<?php
    namespace WS_SatellysReborn\Controleurs;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Login\Utilisateur;
    use WS_SatellysReborn\Vues\Vue;

    /**
     * Controleur des administratifs.
     * @package WS_SatellysReborn\Controleurs
     */
    class Administratif extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            // Bien super-admin ?
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {

                // Récupère la liste des administratifs.
                $liste = DAO_Factory::getDAO_Administratif()->findAll();

                $this->vue = new Vue($this, "Liste");
                $this->vue->render($liste);
            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
                $this->vue->render();
            }
        }

        public function nouveau() {
            // Bien super-admin ?
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                $this->vue = new Vue($this, "Nouveau");
            } else {
                $this->vue = new Vue($this, 'ErreurAdmin');
            }
            $this->vue->render();
        }
    }