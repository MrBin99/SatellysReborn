<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    class Matiere extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                $matiere = DAO_Factory::getDAO_Matiere()->findAll();
                $this->vue =
                    new Vue($this, "ListeMatiere", "Liste des matières");
                $this->vue->render($matiere);
            } else {
                self::redirect("/SatellysReborn/matiere/errNonAdmin");
            }
        }

        public function listeCours($idMatiere){
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                $cours = DAO_Factory::getDAO_Cours()->findAll();
                $listecours = array();
                foreach($cours as $val){
                    if ($val->getMatiere()->getId() == $idMatiere){
                        array_push($listecours, $val);
                    }
                }
                $this->vue =
                    new Vue($this, "ListeCours", "Liste des cours");
                $this->vue->render($listecours);
            } else {
                self::redirect("/SatellysReborn/matiere/errNonAdmin");
            }
        }

        /**
         * Affiche la page d'erreur quand un utilisateur essaie d'accéder à
         * une page sans être connecté en tant qu'administrateur.
         */
        public function errNonAdmin() {
            $this->vue = new Vue($this, 'ErrNonAdmin', 'Pas administrateur');
            $this->vue->render();
        }
    }