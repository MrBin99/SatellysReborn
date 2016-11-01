<?php
    namespace WS_SatellysReborn\Modeles\Population\Groupe;

    use WS_SatellysReborn\BaseDonnees\BD_Exception;
    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Modele;
    use WS_SatellysReborn\Modeles\Population\Etudiant;

    /**
     * Représente un groupe d'étudiant faisant partie d'une promotion.
     * @package WS_SatellysReborn\Modeles\Population\Groupe
     */
    class Groupe extends Modele {

        /** @var string l'identifiant du groupe. */
        private $id;

        /** @var string le nom du groupe. */
        private $nom;

        /** @var Promotion la promotion dont le groupe fait partie. */
        private $promo;

        /** @var array la liste des étudiants de ce groupe. */
        private $etudiants;

        /**
         * Créé un nouveau groupe d'étudiants.
         * @param string $id l'identifiant du groupe.
         * @param string $nom le nom du groupe.
         * @param Promotion $promo la promotion dont le groupe fait partie.
         */
        public function __construct($id = null, $nom, Promotion $promo) {
            $this->id = $id;
            $this->nom = $nom;
            $this->promo = $promo;
            $this->etudiants = array();
        }

        /**
         * Ajoute un nouvel étudiant à ce groupe.
         * @param Etudiant $etudiant l'étudiant à ajouter à ce groupe.
         * @throws BD_Exception si on ne peut pas affecter cet étudiant à ce
         *     groupe.
         */
        public function ajouterEtudiant(Etudiant $etudiant) {
            array_push($this->etudiants, $etudiant);

            // Sauvegarde.
            $res = DAO_Factory::getDAO_Groupe()->ajouterEtudiant($this->getId(),
                                                                 $etudiant->getId());

            if (!$res) {
                throw new BD_Exception("Impossible d'affecter cet étudiant à ce groupe.");
            }
        }

        /**
         * @return string l'identifiant du groupe.
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @return string le nom du groupe.
         */
        public function getNom() {
            return $this->nom;
        }

        /**
         * @return Promotion la promotion dont le groupe fait partie.
         */
        public function getPromo() {
            return $this->promo;
        }

        /**
         * @return array la liste des étudiants de ce groupe.
         */
        public function getEtudiants() {
            return $this->etudiants;
        }
    }