<?php
    namespace SatellysReborn\Modeles\Cours;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Modele;
    use SatellysReborn\Modeles\Population\Enseignant;
    use SatellysReborn\Modeles\Population\Groupe\Groupe;

    /**
     * Représente un cours.
     * @package SatellysReborn\Modeles\Cours
     */
    class Cours extends Modele implements \JsonSerializable {

        /** @var string l'identifiant du cours. */
        private $id;

        /** @var Matiere la matière enseignée dans ce cours. */
        private $matiere;

        /** @var Enseignant l'enseigant qui donne ce cours. */
        private $enseignant;

        /** @var array les groupes d'étudiant suivant ce cours. */
        private $groupes;

        /** @var string la salle où se produit ce cours. */
        private $salle;

        /** @var string le jour où se déroule le cours. */
        private $jour;

        /** @var string l'heure de début du cours. */
        private $debut;

        /** @var string l'heure de fin du cours. */
        private $fin;

        /**
         * Créé un nouveau cours.
         * @param string $id l'identifiant du cours.
         * @param Matiere $matiere la matière enseignée dans ce cours.
         * @param Enseignant $enseignant l'enseigant qui donne ce cours.
         * @param string $salle la salle où se produit ce cours.
         * @param $jour string le jour où se déroule le cours.
         * @param $debut string l'heure de début du cours.
         * @param $fin string l'heure de fin du cours.
         */
        public function __construct($id = null, Matiere $matiere,
                                    Enseignant $enseignant, $salle,
                                    $jour, $debut, $fin) {
            $this->id = $id;
            $this->matiere = $matiere;
            $this->groupes = array();
            $this->enseignant = $enseignant;
            $this->salle = $salle;
            $this->jour = $jour;
            $this->debut = $debut;
            $this->fin = $fin;
        }

        /**
         * Ajoute un nouveau groupe à ce cours.
         * @param Groupe $groupe le groupe à ajouter à ce cours.
         */
        public function ajouterGroupe(Groupe $groupe) {
            array_push($this->groupes, $groupe);
            DAO_Factory::getDAO_Cours()->ajouterCours($this->id, $groupe->getId());
        }

        /**
         * @return string l'identifiant du cours.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return Matiere la matière enseignée dans ce cours.
         */
        public function getMatiere() {
            return $this->matiere;
        }

        /**
         * @return Enseignant l'enseigant qui donne ce cours.
         */
        public function getEnseignant() {
            return $this->enseignant;
        }

        /**
         * @return string la salle où se produit ce cours.
         */
        public function getSalle() {
            return $this->salle;
        }

        /**
         * @return string le jour où se déroule le cours.
         */
        public function getJour() {
            return $this->jour;
        }

        /**
         * @return string l'heure de début du cours.
         */
        public function getDebut() {
            return $this->debut;
        }

        /**
         * @return string l'heure de fin du cours.
         */
        public function getFin() {
            return $this->fin;
        }

        /**
         * @return array les groupes présent à ce cours.
         */
        public function getGroupes() {
            return $this->groupes;
        }

        /**
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        public function jsonSerialize() {
            $var = get_object_vars($this);
            foreach ($var as &$value) {
                if (is_object($value) && method_exists($value,'jsonSerialize')) {
                    $value = $value->jsonSerialize();
                }
            }
            return $var;
        }
    }