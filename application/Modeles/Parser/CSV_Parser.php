<?php
    namespace SatellysReborn\Modeles\Parser;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
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

        /** @var array les étudiants à importer. */
        private $etudiants;

        /**
         * Créé un nouvel analyseur de fichier CSV.
         * @param Groupe $groupe le groupe où les étudiants seront importés.
         * @param $contenu array le contenu du fichier CSV
         * dans un tableau ligne par ligne.
         */
        public function __construct(Groupe $groupe, $contenu) {
            $this->groupe = $groupe;
            $this->contenu = $contenu;
            $this->etudiants = array();
        }

        /**
         * Lance l'analyse du fichier CSV et enregistre ses données dans la
         * base de données..
         */
        public function parse() {
            // Parcours du fichier.
            foreach ($this->contenu as $ligne) {
                $csv = str_getcsv($ligne, ';');

                // Extrait l'adresse.
                $ville =
                    DAO_Factory::getDAO_Ville()->findCpNom($csv[9], $csv[10]);
                $adresse = new Adresse(null, $csv[6], $csv[7], $csv[8], $ville);

                // L'étudiant.
                $etudiant =
                    new Etudiant($csv[0], $csv[1], $csv[2], $csv[3], $csv[4],
                                 $csv[5], $adresse);

                // Si l'étudiant n'existe pas déjà.
                if (DAO_Factory::getDAO_Etudiant()->find($csv[0]) == null) {

                    // On insère l'adresse.
                    $adresse = DAO_Factory::getDAO_Adresse()->insert($adresse);

                    // Créé l'étudiant.
                    $etudiant = new Etudiant($csv[0], $csv[1], $csv[2], $csv[3],
                                             $csv[4],
                                             $csv[5], $adresse);
                    DAO_Factory::getDAO_Etudiant()->insert($etudiant);
                }

                // L'ajoute au groupe.
                $this->groupe->ajouterEtudiant($etudiant);
            }
        }
    }