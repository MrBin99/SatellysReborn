<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO\Cours;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO;
    use WS_SatellysReborn\Modeles\Cours\Matiere;

    /**
     * DAO permettant de gérer les matières en base de données.
     * @package WS_SatellysReborn\BaseDonnees\DAO\Cours
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
            $sql = 'INSERT INTO matiere
                    VALUES (:id, :nom)';

            $res = $this->connexion->insert($sql, array(
                ':id' => $obj->getId(),
                ':nom' => $obj->getNom()
            ));

            return $res != 1 ? $obj : false;
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
         * Liste les matières d'un département.
         * @param $depID string l'identifiant du département.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findDepartement($depID) {
            /// SQL.
            $sql = 'SELECT m.id AS id, m.nom AS nom
                    FROM matiere m
                    JOIN cours c ON m.id = c.id_matiere
                    JOIN assiste a ON c.id = a.id_cours
                    JOIN groupe g ON a.id_groupe = g.id
                    JOIN promotion p ON g.id_promotion = p.id
                    JOIN departement d ON p.id_departement = d.id
                    WHERE d.id = :dep';

            $resBD = $this->connexion->select($sql, array(
                ':dep' => $depID
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Matiere.
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
         * Liste les matières d'une promotion.
         * @param $promoID string l'identifiant de la promotion.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findPromotion($promoID) {
            /// SQL.
            $sql = 'SELECT m.id AS id, m.nom AS nom
                    FROM matiere m
                    JOIN cours c ON m.id = c.id_matiere
                    JOIN assiste a ON c.id = a.id_cours
                    JOIN groupe g ON a.id_groupe = g.id
                    JOIN promotion p ON g.id_promotion = p.id
                    WHERE p.id = :promo';

            $resBD = $this->connexion->select($sql, array(
                ':promo' => $promoID
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Matiere.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                array_push($res, new Matiere(
                    $obj->id, $obj->nom
                ));
            }

            return $res;
        }
    }