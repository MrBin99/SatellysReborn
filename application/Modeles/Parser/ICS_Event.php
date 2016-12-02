<?php
    namespace SatellysReborn\Modeles\Parser;

    /**
     * Représente un évènnement dans un fichier ICS.
     * @package WS_SatellysReborn\Modeles\Parser
     */
    class ICS_Event {

        /**
         * @var string l'heure de début du cours.
         */
        private $heureDebut;

        /**
         * @var string l'heure de fin du cours.
         */
        private $heureFin;

        /**
         * @var string le jour où à lieu le cours.
         */
        private $date;

        /**
         * @var string le nom du cours.
         */
        private $nom;

        /**
         * @var array le nom des groupes qui ont cours.
         */
        private $groupes;

        /**
         * @var string la salle de cours.
         */
        private $salle;

        /**
         * @var string le nom de l'enseignant qui donne ce cours.
         */
        private $prof;

        /**
         * Créé un nouvel évènnement ICS.
         * @param $heureDebut string l'heure de début du cours.
         * @param $heureFin string l'heure de fin du cours.
         * @param $date string le jour où à lieu le cours.
         * @param $nom string le nom du cours.
         * @param $groupes array le nom des groupes qui ont cours.
         * @param $salle string la salle de cours.
         * @param $prof string le nom de l'enseignant qui donne ce cours.
         */
        public function __construct($heureDebut, $heureFin, $date, $nom,
                                    $groupes, $salle, $prof) {
            $this->heureDebut = $heureDebut;
            $this->heureFin = $heureFin;
            $this->date = $date;
            $this->nom = $nom;
            $this->groupes = $groupes;
            $this->salle = $salle;
            $this->prof = $prof;
        }

        /**
         * @return string l'heure de début du cours.
         */
        public function getHeureDebut() {
            return $this->heureDebut;
        }

        /**
         * @return string l'heure de fin du cours.
         */
        public function getHeureFin() {
            return $this->heureFin;
        }

        /**
         * @return string le jour où à lieu le cours.
         */
        public function getDate() {
            return $this->date;
        }

        /**
         * @return array le nom des groupes qui ont cours.
         */
        public function getGroupes() {
            return $this->groupes;
        }

        /**
         * @return string la salle où à lieu le cours.
         */
        public function getSalle() {
            return $this->salle;
        }

        /**
         * @return string le nom du cours.
         */
        public function getNom() {
            return $this->nom;
        }

        /**
         * @return string le nom de l'enseignant qui donne le cours.
         */
        public function getProf() {
            return $this->prof;
        }
    }