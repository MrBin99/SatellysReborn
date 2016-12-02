<?php
    namespace SatellysReborn\BaseDonnees\DAO\Population\Groupe;

    use SatellysReborn\BaseDonnees\DAO\DAO;
    use SatellysReborn\Modeles\Population\Groupe\Departement;

    /**
     * DAO permettant de gérer département de l'IUT en base de données.
     * @package SatellysReborn\BaseDonnees\DAO\Population\Groupe
     */
    class DAO_Departement extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Departement $obj l'objet à insérer dans la base de données.
         * @return Departement|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO departement (nom)
                    VALUES (:nom)';

            $res = $this->connexion->insert($sql, array(
                ':nom' => $obj->getNom()
            ));

            // Insertion ok ?
            if ($res != false) {
                return new Departement($res, $obj->getNom());
            }

            // else

            return false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Departement $obj l'objet à modifier dans la base de données.
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
            $sql = 'UPDATE departement SET
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
         * @param Departement $obj l'objet à supprimer dans la base de données.
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
            $sql = 'DELETE FROM departement
                    WHERE id = :id';

            return $this->connexion->delete($sql, array(
                ':id' => $obj->getId()
            ));
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Departement
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT nom
                    FROM departement
                    WHERE id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            return new Departement($cle, $resBD[0]->nom);
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
                    FROM departement';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Departement.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                array_push($res, new Departement(
                    $obj->id, $obj->nom
                ));
            }

            return $res;
        }

        /**
         * Sélectionne le pays dont le nom est passée en argument
         * s'il existe.
         * @param $nom string le nom du pays.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findNom($nom) {
            // SQL.
            $sql = 'SELECT id, nom
                    FROM departement
                    WHERE enleverAccents(lower(nom)) LIKE 
                          enleverAccents(lower(:nom))';

            $resBD = $this->connexion->select($sql, array(
                ':nom' => '%' . $nom . '%'
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Departement.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                array_push($res, new Departement(
                    $obj->id, $obj->nom
                ));
            }

            return $res;
        }
    }