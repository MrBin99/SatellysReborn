<?php
    namespace SatellysReborn\Modeles\Parser;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Exceptions\DonneesIncorrecteException;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;
    use SatellysReborn\Modeles\Population\Etudiant;
    use SatellysReborn\Modeles\Population\Groupe\Groupe;

    /**
     * Représente l'analyseur de fichier '.csv' pour importer une liste
     * d'étudiant.
     * @package WS_SatellysReborn\Modeles\Parser
     */
    class CSV_Parser {

        /** @var Groupe le groupe d'étudiant à importer. */
        private $groupe;

        /**
         * @var array le contenu du fichier CSV
         * dans un tableau ligne par ligne.
         */
        private $contenu;

        /** @var bool si une erreur est présente dans le fichier CSV. */
        private $erreur;

        /** @var array les logs après l'analyse du fichier CSV. */
        private $logs;

        /**
         * Créé un nouvel analyseur de fichier CSV.
         * @param Groupe $groupe le groupe où les étudiants seront importés.
         * @param $contenu array le contenu du fichier CSV
         * dans un tableau ligne par ligne.
         */
        public function __construct(Groupe $groupe, $contenu) {
            $this->groupe = $groupe;
            $this->contenu = $contenu;
            $this->logs = array();
            $this->erreur = false;
        }

        /**
         * Analyse le fichier ICS :
         * <ul>
         *     <li>Regarde si les lignes CSV sont complètes.</li>
         *     <li>Enregistre les logs de l'analyse.</li>
         * </ul>
         */
        public function parse() {
            // Parcours du fichier.
            for ($i = 0; $i < count($this->contenu); $i++) {
                $csv = str_getcsv($this->contenu[$i], ';');

                // Ligne incomplète
                if (count($csv) != 12) {
                    array_push($this->logs, '<span>Ligne ' . ($i + 1) .
                                            ':</span> La ligne de données CSV est incomplète');
                    $this->erreur = true;
                    continue;
                }

                // INE et numéro UT1
                if (strlen($csv[1]) != 12) {
                    array_push($this->logs, '<span>Ligne ' . ($i + 1) .
                                            ':</span> Le numéro INE de l\'étudiant est incorrect, ' .
                                            'il doit faire 12 caractères.');
                    $this->erreur = true;
                    continue;
                } else if (strlen($csv[0]) != 13) {
                    array_push($this->logs, '<span>Ligne ' . ($i + 1) .
                                            ':</span> Le numéro UT1 de l\'étudiant est incorrect, ' .
                                            'il doit faire 13 caractères.');
                    $this->erreur = true;
                    continue;
                }
            }
        }

        /**
         * Insère les lignes CSV chargés dans la base de données.
         * @return array le nombre d'insertions valides et invalides effectués.
         */
        public function insererBD() {
            $insertions = array(
                "ok" => 0,
                "nok" => 0
            );

            foreach ($this->contenu as $ligne) {
                $csv = str_getcsv($ligne, ';');

                // Ligne incomplète
                if (count($csv) != 12) {
                    $insertions['nok']++;
                    continue;
                }

                // Extrait l'adresse.
                $ville =
                    DAO_Factory::getDAO_Ville()->findCpNom($csv[9], $csv[10]);
                $adresse = new Adresse(null, $csv[6], $csv[7], $csv[8], $ville);

                try {
                    // L'étudiant.
                    $etudiant =
                        new Etudiant($csv[0], $csv[1], $csv[2], $csv[3],
                                     $csv[4],
                                     $csv[5], $adresse);
                } catch (DonneesIncorrecteException $e) {
                    $insertions['nok']++;
                    continue;
                }

                // Si l'étudiant n'existe pas déjà.
                if (DAO_Factory::getDAO_Etudiant()->find($csv[0]) == null) {

                    // On insère l'adresse.
                    $adresse = DAO_Factory::getDAO_Adresse()->insert($adresse);

                    // Créé l'étudiant.
                    $etudiant = new Etudiant($csv[0], $csv[1], $csv[2],
                                             $csv[3], $csv[4],
                                             $csv[5], $adresse);

                    if (DAO_Factory::getDAO_Etudiant()->insert($etudiant)
                        == false
                    ) {
                        $insertions['nok']++;
                        continue;
                    }
                }

                // Ajoute au groupe.
                $ok = DAO_Factory::getDAO_Groupe()
                                 ->ajouterEtudiant($this->groupe->getId(),
                                                   $etudiant->getId());

                // Résultat de l'insertion.
                if ($ok) {
                    $insertions['ok']++;
                } else {
                    $insertions['nok']++;
                }
            }

            return $insertions;
        }

        /**
         * @return array les logs après lecture du fichier CSV.
         */
        public function getLogs() {
            return $this->logs;
        }

        /**
         * @return bool si le fichier CSV après lecture possède des erreurs.
         */
        public function hasErreur() {
            return $this->erreur;
        }

        /**
         * @return Groupe le groupe les étudiants seront affectés.
         */
        public function getGroupe() {
            return $this->groupe;
        }
    }