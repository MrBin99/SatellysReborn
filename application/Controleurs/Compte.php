<?php
    namespace WS_SatellysReborn\Controleurs;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Login\Utilisateur;
    use WS_SatellysReborn\Vues\Vue;

    /**
     * Contrôleur pour la gestion du compte et la connexion d'un utilisateur.
     * @package WS_SatellysReborn\Controleurs
     */
    class Compte extends Controleur {

        /**
         * Affiche la page de modification du compte.
         */
        public function index() {
            // Est bien connecté.
            if (Utilisateur::estConnecte()) {
                $this->vue = new Vue($this, 'MonCompte');
            } else {
                $this->vue = new Vue($this, 'ErreurNonConnecte');
            }
            $this->vue->render();
        }

        /**
         * Connecte un utilisateur à l'application si possible.
         */
        public function connexion() {
            // N'est pas déjà connecté.
            if (!Utilisateur::estConnecte() && isset($_POST['login'])) {
                $util = DAO_Factory::getDAO_Utilisateur()
                                   ->findLoginMdp($_POST['login'],
                                                  Utilisateur::crypterMdp($_POST['mdp']));

                // Utilisateur existe ?
                if (isset($util)) {

                    // Met à jour l'utilisateur courant.
                    Utilisateur::setUtilisateur($util);

                    // Retour à la page d'accueil mais connecté.
                    self::redirect('');
                } else {
                    $this->vue = new Vue($this, 'IdentifiantsInvalides');
                }
            } else {
                $this->vue = new Vue($this, 'ErreurDejaConnecte');
            }
            $this->vue->render();
        }

        /**
         * Déconnecte un utilisateur de l'application.
         */
        public function deconnexion() {
            // Est connecté ?
            if (Utilisateur::estConnecte()) {
                session_destroy();
                self::redirect('');
            } else {
                $this->vue = new Vue($this, 'ErreurNonConnecte');
                $this->vue->render();
            }
        }

        /**
         * Modifie les informations d'un utilisateur.
         */
        public function modifier() {
            // Est connecté ?
            if (Utilisateur::estConnecte() && isset($_POST['login'])) {

                // Récupère l'utilisateur courant.
                $utilCourant = Utilisateur::getUtilisateur();

                // Modifie les données.
                $newUtil = new Utilisateur($utilCourant->getLogin(),
                                           $_POST['mdp'],
                                           $_POST['email'],
                                           $utilCourant->getEnseignant(),
                                           $utilCourant->getAdministratif());

                // Met à jour les données.
                $res = DAO_Factory::getDAO_Utilisateur()->update($newUtil);

                // Si OK ?
                if ($res) {

                    // Modifie l'utilisateur.
                    Utilisateur::setUtilisateur($newUtil);
                    $this->vue = new Vue($this, 'MiseAJourOk');

                } else {
                    $this->vue = new Vue($this, 'MiseAJourNOk');
                }
            } else {
                $this->vue = new Vue($this, 'ErreurNonConnecte');
            }
            $this->vue->render();
        }
    }