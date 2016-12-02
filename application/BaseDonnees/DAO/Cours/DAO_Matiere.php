<?php
    namespace SatellysReborn\BaseDonnees\DAO\Cours;

    use SatellysReborn\BaseDonnees\DAO\DAO;
    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Cours\Cours;
    use SatellysReborn\Modeles\Cours\Matiere;

    /**
     * DAO permettant de gérer les matières en base de données.
     * @package SatellysReborn\BaseDonnees\DAO\Cours
     */
    class DAO_Matiere extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Matiere $obj l'objet à insérer dans la base de données.
         * @return Matiere|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO matiere (nom)
                    VALUES (:nom)';

            $res = $this->connexion->insert($sql, array(
                ':nom' => $obj->getNom()
            ));

            // Insertion ok ?
            if ($res) {
                return new Matiere($res, $obj->getNom());
            }

            // else
            return false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Matiere $obj l'objet à modifier dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la modification a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function update($obj) {
            // Pré-condition.
            if (is_null($obj->getId()) || is_null($this->find($obj->getId()))) {
                return false;
            }
            // else

            // SQL.
            $sql = 'UPDATE matiere SET
                        nom = :nom
                    WHERE id = :id';

            return $this->connexion->update($sql, array(
                ':nom' => $obj->getNom(),
                ':id' => $obj->getId()
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Matiere $obj l'objet à supprimer dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la suppression a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function delete($obj) {
            // Pré-condition.
            if (is_null($obj->getId()) || is_null($this->find($obj->getId()))) {
                return false;
            }

            // SQL.
            $sql = 'DELETE FROM matiere
                    WHERE id = :id';

            return $this->connexion->delete($sql, array(
                ':id' => $obj->getId()
            ));
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Matiere
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT nom
                    FROM matiere
                    WHERE id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            return new Matiere($cle, $resBD[0]->nom);
        }

        /**
         * Sélectionne la matière dont le nom est passée en argument.
         * @param $nom string le nom de la matière.
         * @return Matiere
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findNom($nom) {
            // SQL.
            $sql = 'SELECT id, nom
                    FROM matiere
                    WHERE lower(nom) LIKE lower(:nom)';

            $resBD = $this->connexion->select($sql, array(
                ':nom' => '%' . $nom . '%'
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            return new Matiere($resBD[0]->id, $resBD[0]->nom);
        }

        /**
         * Sélectionne tous les éléments de ce type.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findAll() {
            // SQL.
            $sql = 'SELECT id, nom
                    FROM matiere';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Pays.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                array_push($res, new Matiere(
                    $obj->id, $obj->nom
                ));
            }

            return $res;
        }

        /**
         * Liste les matières avec leur département et leur promotion dans
         * lesquelles elles sont enseignées.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findPromoDep() {
            // SQL.
            $sql = 'SELECT DISTINCT m.id AS id, m.nom AS nom, p.id AS promo
                    FROM matiere m
                    LEFT JOIN cours c ON m.id = c.id_matiere
                    LEFT JOIN assiste a ON c.id = a.id_cours
                    LEFT JOIN groupe g ON a.id_groupe = g.id
                    LEFT JOIN promotion p ON g.id_promotion = p.id
                    LEFT JOIN departement d ON p.id_departement = d.id';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Matière.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                array_push($res, array(
                    new Matiere($obj->id, $obj->nom),
                    DAO_Factory::getDAO_Promotion()->find($obj->promo)
                ));
            }

            return $res;
        }

        /**
         * Sélectionne les matières dont le nom ou l'identifiant est passée en
         * argument.
         * @param $arg string l'argument de recherche.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findNomId($arg) {
            // SQL.
            $sql = 'SELECT id, nom
                    FROM matiere
                    WHERE enleverAccents(lower(nom)) LIKE 
                          enleverAccents(lower(:arg))
                    OR id LIKE :arg';

            $resBD = $this->connexion->select($sql, array(
                ":arg" => '%' . $arg . '%'
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Pays.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                array_push($res, new Matiere(
                    $obj->id, $obj->nom
                ));
            }

            return $res;
        }

        /**
         * Récupère tous les cours d'une matière.
         * @param $id string l'identifiant de la matière.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findCours($id) {
            // SQL.
            $sql = 'SELECT c.id AS id, jour, debut, fin, salle, id_enseignant, id_matiere
                    FROM matiere m
                    JOIN cours c ON m.id = c.id_matiere
                    WHERE m.id = :id';

            $resBD = $this->connexion->select($sql, array(
                ":id" => $id
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Cours.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                $matiere =
                    DAO_Factory::getDAO_Matiere()->find($resBD[0]->id_matiere);
                $enseignant = DAO_Factory::getDAO_Enseignant()
                                         ->find($resBD[0]->id_enseignant);
                array_push($res,
                           new Cours($obj->id, $matiere, $enseignant,
                                     $obj->salle, $obj->jour, $obj->debut,
                                     $obj->fin));
            }

            return $res;
        }
    }