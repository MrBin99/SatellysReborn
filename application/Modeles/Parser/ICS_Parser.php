<?php
    namespace SatellysReborn\Modeles\Parser;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Cours\Cours;
    use SatellysReborn\Modeles\Cours\Matiere;

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

        /** @var int la ligne courannte parcouru. */
        private $ligneCourante;

        /** @var array les évènnements chargés du fichier. */
        private $events;

        /** @var bool si une erreur est présente dans le fichier ICS. */
        private $erreur;

        /** @var array les logs après l'analyse du fichier ICS. */
        private $logs;

        /**
         * Créé un nouveau analyseur de fichier ICS.
         * @param $contenu array le contenu du fichier ICS ligne par ligne.
         */
        public function __construct($contenu) {
            $this->contenu = $contenu;
            $this->events = array();
            $this->logs = array();
            $this->erreur = false;
        }

        /**
         * Analyse le fichier ICS :
         * <ul>
         *     <li>Créé les objets ICS_Event correspondant au évènnement.</li>
         *     <li>Enregistre les logs de l'analyse.</li>
         * </ul>
         */
        public function parse() {
            // Parcours le fichier.
            $this->ligneCourante = 0;

            for (; $this->ligneCourante < count($this->contenu);
                   $this->ligneCourante++) {
                $ligne = $this->contenu[$this->ligneCourante];

                // Si nouvel évènnement.
                if ($ligne == 'BEGIN:VEVENT') {

                    // L'évènnement.
                    $event = array();

                    // On ajoute pas à l'èvennement 'BEGIN:VEVENT'.
                    $this->ligneCourante++;

                    // Pour toutes les lignes de l'évènnement.
                    do {
                        // Sauvegarde la ligne.
                        $ligne = $this->contenu[$this->ligneCourante];

                        // Créé l'évènnement.
                        array_push($event, $ligne);

                        // Passe à l'autre ligne.
                        $this->ligneCourante++;
                    } while ($ligne != 'END:VEVENT');

                    // Analyse l'évennement.
                    $eventObj = $this->analyserEvent($event);

                    // Enregistre l'évènnement.
                    array_push($this->events, $eventObj);

                    // Enregistre les logs.
                    array_push($this->logs, $this->preLoad($eventObj));
                }
            }

            // Libère le contenu.
            unset($this->contenu);
        }

        /**
         * Analyse le tableau contenant un évènnement au format ICS
         * et retourne l'objet ICS_Event correspondant.
         * @param $event array le tableau des ligne de l'évènnement ICS.
         * @return ICS_Event l'évènnement ICS résultat.
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
            return $this->creerEvent($cmd);
        }

        /**
         * Créé un objet ICS_Event à partir d'un tableau de commandes.
         * @param $cmd array le tableau de commandes.
         * @return ICS_Event un objet représentant l'évènnement.
         */
        private function creerEvent($cmd) {
            // On récupère les heures de début et fin du cours.
            $heureDebut = isset($cmd['DTSTART']) ?
                $this->toDateTime($cmd['DTSTART'])->format('H:i') : null;
            $heureFin = isset($cmd['DTEND']) ?
                $this->toDateTime($cmd['DTEND'])->format('H:i') : null;

            // On récupère la date du cours.
            $date = isset($cmd['DTEND']) ?
                $this->toDateTime($cmd['DTEND'])->format('Y-m-d') : null;

            // On récupère la description.
            if (isset($cmd['DESCRIPTION'])) {
                $desc = explode('\n', $cmd['DESCRIPTION']);

                // Si enseignant indéterminé
                if (count($desc) != 3) {
                    array_splice($desc, 0, 1);
                    array_splice($desc, count($desc) - 1, 1);

                    // Extrait l'enseignant.
                    $prof = array_splice($desc, count($desc) - 1, 1)[0];
                } else {
                    $prof = "non déterminé";
                }
            } else {
                $prof = null;
                $desc = null;
            }

            // Si salle indéterminé
            if (isset($cmd['LOCATION'])) {
                $salle = $cmd['LOCATION'];
            } else {
                $salle = "ND";
            }

            // La matière.
            if (isset($cmd['SUMMARY'])) {
                $matiere = str_replace('\\', '', $cmd['SUMMARY']);
            } else {
                $matiere = null;
            }

            // Retourne l'objet ICS.
            return new ICS_Event($heureDebut, $heureFin, $date,
                                 $matiere, $desc, $salle, $prof);
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
            $time[4] = intval($time[4]) + 1;

            // On créé l'objet DateTime.
            $dateTime = new \DateTime();
            $dateTime->setDate($time[1], $time[2], $time[3]);
            $dateTime->setTime($time[4], $time[5], $time[6]);

            return $dateTime;
        }

        /**
         * Effectue un préchargement des évènnements pour vérifier
         * s'ils ne comportent pas d'erreurs et que les lignes pré-requises
         * sont bien présente dans la base de données.
         * @param ICS_Event $eventObj
         * @return string les logs du préchargement.
         */
        private function preLoad(ICS_Event $eventObj) {
            // Les logs.
            $logs = array();

            // L'enseignant.
            if ($eventObj->getProf() != null) {

                // Sépare le nom et prénom du prof.
                $prof = explode(' ', $eventObj->getProf());

                if (DAO_Factory::getDAO_Enseignant()
                               ->findNomPrenom($prof[0], $prof[1]) == null
                ) {
                    $this->erreur = true;
                    $eventObj->marquerErreur();
                    array_push($logs, "<span>Ligne " . $this->ligneCourante .
                                      " : </span>L'enseignant '"
                                      . $eventObj->getProf()
                                      . "' n'existe pas veuillez le créer.");
                }
            } else {
                $this->erreur = true;
                $eventObj->marquerErreur();
                array_push($logs, "<span>Ligne " . $this->ligneCourante .
                                  " : </span>Pas d'enseignant affecté à ce cours.");
            }

            // Les groupes.
            if ($eventObj->getGroupes() != null) {
                foreach ($eventObj->getGroupes() as $groupe) {
                    if (DAO_Factory::getDAO_Groupe()
                                   ->findNom($groupe) == null
                    ) {
                        $this->erreur = true;
                        $eventObj->marquerErreur();
                        array_push($logs, "<span>Ligne " . $this->ligneCourante
                                          . " : </span>Le groupe '" . $groupe
                                          . "' n'existe pas, "
                                          . "veuillez le créer avant.");
                    }
                }
            } else {
                $this->erreur = true;
                $eventObj->marquerErreur();
                array_push($logs, "<span>Ligne " . $this->ligneCourante
                                  . " : </span>Pas de groupes affecté à ce cours.");
            }

            // Les horaires.
            if ($eventObj->getDate() == null) {
                $this->erreur = true;
                $eventObj->marquerErreur();
                array_push($logs, "<span>Ligne " . $this->ligneCourante
                                  . " : </span>Pas de date définis pour ce cours.");
            } else if ($eventObj->getHeureDebut() == null) {
                $this->erreur = true;
                $eventObj->marquerErreur();
                array_push($logs, "<span>Ligne " . $this->ligneCourante
                                  . " : </span>Pas d'heure de début définis pour ce cours.");
            } else if ($eventObj->getHeureFin() == null) {
                $this->erreur = true;
                $eventObj->marquerErreur();
                array_push($logs, "<span>Ligne " . $this->ligneCourante
                                  . " : </span>Pas d'heure de fin définis pour ce cours.");
            }

            // Le cours.
            if ($eventObj->getNom() != null) {
                if (DAO_Factory::getDAO_Cours()
                               ->findNameDateHeure($eventObj->getNom(),
                                                   $eventObj->getDate(),
                                                   $eventObj->getHeureDebut(),
                                                   $eventObj->getHeureFin())
                    != null
                ) {
                    $this->erreur = true;
                    $eventObj->marquerErreur();
                    array_push($logs, "<span>Ligne " . $this->ligneCourante
                                      . " : </span>Ce cours existe déjà.");
                }
            } else {
                $this->erreur = true;
                $eventObj->marquerErreur();
                array_push($logs, "<span>Ligne " . $this->ligneCourante
                                  . " : </span>Pas de matière définis pour ce cours.");
            }

            return $logs;
        }

        /**
         * Insère les évènnements chargés dans la base de données.
         * @return array le nombre d'insertions valides et invalides effectués.
         */
        public function insererBD() {
            $insertions = array(
                "ok" => 0,
                "nok" => 0
            );
            foreach ($this->events as $event) {
                // Evennement erroné ?
                if ($event->isErreur()) {
                    continue;
                }

                // Sépare le nom et prénom du prof.
                $prof = explode(' ', $event->getProf());

                // L'enseignant du cours.
                $enseignant = DAO_Factory::getDAO_Enseignant()
                                         ->findNomPrenom($prof[0], $prof[1]);
                if ($enseignant == null) {
                    $insertions["nok"]++;
                    continue;
                }

                $matiere = DAO_Factory::getDAO_Matiere()->findNom(
                    $event->getNom());

                if ($matiere == null) {
                    $matiere = DAO_Factory::getDAO_Matiere()->insert(
                        new Matiere(null, $event->getNom()));
                }

                // Créé le cours.
                $cours = new Cours(
                    null, $matiere, $enseignant,
                    $event->getSalle(), $event->getDate(),
                    $event->getHeureDebut(), $event->getHeureFin()
                );

                // Ajoute les groupes.
                foreach ($event->getGroupes() as $groupe) {
                    $g = DAO_Factory::getDAO_Groupe()->findNom($groupe);

                    if ($g == null) {
                        continue 2;
                    }
                }

                // Insère le cours.
                $cours = DAO_Factory::getDAO_Cours()->insert($cours);
                if ($cours == false) {
                    $insertions["nok"]++;
                    continue;
                }

                // Ajoute les groupes.
                foreach ($event->getGroupes() as $groupe) {
                    $g = DAO_Factory::getDAO_Groupe()->findNom($groupe);

                    if (isset($g)) {
                        $cours->ajouterGroupe($g);
                        DAO_Factory::getDAO_Cours()->ajouterCours($cours->getId(),
                                                                  $g->getId());
                    }
                }
                $insertions["ok"]++;
            }

            return $insertions;
        }

        /**
         * @return array les évènnements chargées du fichier.
         */
        public function getEvents() {
            return $this->events;
        }

        /**
         * @return array les logs de l'analyse du fichier ICS.
         */
        public function getLogs() {
            return $this->logs;
        }

        /**
         * @return bool si une erreur est contenu dans le fichier ICS.
         */
        public function hasErreur() {
            return $this->erreur;
        }
    }