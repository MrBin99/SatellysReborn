<?php
    namespace WS_SatellysReborn\Controleurs;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Login\Utilisateur;
    use WS_SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les étudiants.
     * @package WS_SatellysReborn\Controleurs
     */
    class Etudiant extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
        }

        /**
         * Affiche les détails d'un étudiant.
         * @param $id string l'identifiant de l'étudiant.
         */
        public function details($id) {
            // Connecté.
            if (!Utilisateur::estConnecte()) {
                self::redirect('compte/erreurnonconnecte');
            }

            if (isset($id)) {

                // Récupère l'étudiant.
                $etudiant = DAO_Factory::getDAO_Etudiant()->find($id);

                // Etudiant trouvé.
                if (isset($etudiant)) {

                    $this->vue = new Vue($this, 'Details');
                    $this->vue->render($etudiant);

                } else {
                    self::redirect('etudiant/erreurEtudiantInconnu');
                }

            } else {
                self::redirect('etudiant/erreurEtudiantInconnu');
            }
        }

        /**
         * Affiche les absences d'un étudiant.
         * @param $id string l'identifiant de l'étudiant.
         */
        public function absences($id) {
            // Connecté.
            if (!Utilisateur::estConnecte()) {
                self::redirect('compte/erreurnonconnecte');
            }

            if (isset($id)) {

                // Récupère les absences.
                $absences = DAO_Factory::getDAO_Absence()
                                       ->getAbsencesEtudiant($id);

                $this->vue = new Vue($this, 'Absences');
                $this->vue->render($absences);

            } else {
                self::redirect('etudiant/erreurEtudiantInconnu');
            }
        }

        /**
         * Affiche la page d'erreur quand on veut afficher les détails
         * d'un étudiant inconnu.
         */
        public function erreurEtudiantInconnu() {
            $this->vue = new Vue($this, 'ErrEtudiantInconnu');
            $this->vue->render();
        }
    }