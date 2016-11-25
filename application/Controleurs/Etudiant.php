<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\BaseDonnees\DAO\Population\Groupe\DAO_Promotion;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    class Etudiant extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                $etudiants = DAO_Factory::getDAO_Etudiant()->findAll();
                $this->vue =
                    new Vue($this, "ListeEtudiant", "Liste des étudiants");
                $this->vue->render($etudiants);
            } else {
                self::redirect("/SatellysReborn/etudiant/errNonAdmin");
            }
        }

        /**
         * Fonction affichant le détail d'un étudiant
         */
        public function details($idEtudiant) {
            if (Utilisateur::estConnecte() &&
                Utilisateur::getUtilisateur()->estAdmin()
            ) {
                $etudiant = DAO_Factory::getDAO_Etudiant()->find($idEtudiant);
                /*$adr = $etudiant->getAdresse();
                var_dump($adr);
                $adresse = $adr->getAdresse1();
                if($adr->getAdresse2() != "" && $adr->getAdresse2() != null){
                    $adresse .= "<br>" . $adr->getAdresse2();
                }
                if($adr->getAdresse3() != "" && $adr->getAdresse3() != null){
                    $adresse .= "<br>".$adr->getAdresse3();
                }
                $adresse .= "<br>".$adr->getVille()->getCodePostal()." ".$adr->getVille()->getNom();*/
                $abs = DAO_Factory::getDAO_Absence()->getAbsencesEtudiant($idEtudiant);

                $this->vue =
                    new Vue($this, "Details", "Détails d'un étudiant");
                $this->vue->render(array($etudiant,$abs));
            } else {
                self::redirect("/SatellysReborn/etudiant/errNonAdmin");
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