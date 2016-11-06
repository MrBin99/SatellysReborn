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

        /**
         *
         */
        public function ajout(){
            /**
             * TODO : Ajouter un administratif
             * Créer un objet adresse avec les champs, vérifier son existence (l'ajouter au besoin) et récupéré son id
             * Créer un objet administratif avec les champs et l'id d'adresse et l'ajouter avec la fonction insert de
             * DAO_Administratif
             * Test de doublons d'administratif à faire, à moins qu'il se fasse tout seul dans le insert
             */

            $adr = DAO_Factory::getDAO_Adresse()->find(3);
            $exist = false;
            $administratif = DAO_Factory::getDAO_Administratif()->findAll();
            foreach ($administratif as $obj){
                if($obj->getNom() == $_POST['nom'] and $obj->getPrenom() == $_POST['prenom']){
                    $exist = true;
                }
            }
            if(!$exist) {
                $new = new \WS_SatellysReborn\Modeles\Population\Administratif(1000000100000, $_POST['nom'], $_POST['prenom'],
                    $_POST['tel'], $_POST['email'], $_POST['poste'], $adr);
                $res = DAO_Factory::getDAO_Administratif()->insert($new);
            }
        }
    }