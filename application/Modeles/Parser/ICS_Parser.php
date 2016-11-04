<?php
    namespace WS_SatellysReborn\Modeles\Parser;

    /**
     * Représente l'analyseur de fichier '.ics' pour importer les plannings.
     * @package WS_SatellysReborn\Modeles\Parser
     */
    class ICS_Parser {

        /**
         * @var string Regex permettant d'isoler les commandes des valeurs pour
         *     chaque ligne du fichier.
         */
        public static $REGEX_CMD = '/^(\w+):(\X+)$/';

        /**
         * @var string Regex permettant d'analyser les "Timestamp".
         */
        public static $REGEX_TIMESTAMP = '/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z$/';

        /**
         * @var string Regex permettant d'analyser la description d'un
         *     évènnement.
         */
        public static $REGEX_DESC = '/^\\n(\w+)\\n(\X+)\\n\X+$/';

        /**
         * @var int le nombre de ligne qu'un évènement prend dans un fichier
         *     .ics.
         */
        public static $NB_LIGNES_EVENT = 12;

        /**
         * @var array le contenu du fichier ICS
         * dans un tableau ligne par ligne.
         */
        private $contenu;

        /**
         * Créé un nouveau analyseur de fichier ICS.
         * @param $contenu array le contenu du fichier ICS ligne par ligne.
         */
        public function __construct($contenu) {
            $this->contenu = $contenu;
        }

        /**
         * Démarre l'analyse du fichier ICS et insère ses données dans la base
         * de données.
         */
        public function parse() {
            // Parcours le fichier.
            for ($i = 0; $i < count($this->contenu); $i++) {
                $ligne = $this->contenu[$i];

                // Si nouvel évènnement.
                if ($ligne == 'BEGIN:VEVENT') {

                    // On récupère les lignes de l'évènnement.
                    $event =
                        array_slice($this->contenu, $i, self::$NB_LIGNES_EVENT);
                    $this->analyserEvent($event);

                    // Au saute au prochain.
                    $i += self::$NB_LIGNES_EVENT;
                }
            }
        }

        /**
         * Analyse un évènnement sous la forme d'un tableau de lignes du
         * fichier ICS.
         * @param $event array l'évènnement à analyser.
         */
        private function analyserEvent($event) {
            // La commande ainsi que son descriptif.
            $cmd = array();

            // Pour toutes les lignes de l'évènnement.
            foreach ($event as $ligne) {

                // On extrait la commande et son descriptif.
                $splitLigne = array();
                $match = preg_match(self::$REGEX_CMD, $ligne, $splitLigne);

                // Si OK.
                if ($match) {
                    // Construit le tableau de commandes.
                    $cmd[$splitLigne[1]] = $splitLigne[2];
                }
            }

            // Créé l'objet ICS_Event.
            $eventObj = $this->creerEvent($cmd);
        }

        /**
         * Créé un objet ICS_Event à partir d'un tableau de commandes.
         * @param $cmd array le tableau de commandes.
         * @return ICS_Event un objet représentant l'évènnement.
         */
        private function creerEvent($cmd) {
            // On récupère les heures de début et fin du cours.
            $heureDebut = $this->toDateTime($cmd['DTSTART'])->format('H:i');
            $heureFin = $this->toDateTime($cmd['DTEND'])->format('H:i');

            // On récupère la date du cours.
            $date = $this->toDateTime($cmd['DTEND'])->format('d-m-Y');

            // On récupère la description.
            $desc = explode('\n', $cmd['DESCRIPTION']);

            // Retourne l'objet ICS.
            return new ICS_Event($heureDebut, $heureFin, $date,
                                 $cmd['SUMMARY'], $desc[1],
                                 $cmd['SUMMARY'], $desc[2]);
        }

        /**
         * Transforme un "Timestamp" en un objet Date valide.
         * @param $date string le "Timestamp" à analyser.
         * @return \DateTime l'objet correspondant au Timestamp.
         */
        public static function toDateTime($date) {
            // On découpe le Timestamp.
            $time = array();
            preg_match(self::$REGEX_TIMESTAMP, $date, $time);

            // On le met à notre fuseau horaire.
            $time[4] = intval($time[4]) + 2;

            // On créé l'objet DateTime.
            $dateTime = new \DateTime();
            $dateTime->setDate($time[1], $time[2], $time[3]);
            $dateTime->setTime($time[4], $time[5], $time[6]);

            return $dateTime;
        }
    }