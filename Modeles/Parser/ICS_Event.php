<?php
    namespace WS_SatellysReborn\Modeles\Parser;

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
         * @var string le groupe qui a cours.
         */
        private $groupe;

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
         * @param $groupe string le groupe qui a cours.
         * @param $salle string la salle de cours.
         * @param $prof string le nom de l'enseignant qui donne ce cours.
         */
        public function __construct($heureDebut, $heureFin, $date, $nom,
                                    $groupe, $salle, $prof) {
            $this->heureDebut = $heureDebut;
            $this->heureFin = $heureFin;
            $this->date = $date;
            $this->nom = $nom;
            $this->groupe = $groupe;
            $this->salle = $salle;
            $this->prof = $prof;
        }
    }