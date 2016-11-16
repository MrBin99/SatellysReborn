<?php
    namespace SatellysReborn\BaseDonnees\DAO\Population\Groupe;

    use SatellysReborn\BaseDonnees\DAO\DAO;
    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Groupe\Promotion;

    /**
     * DAO permettant de gérer les promotions en base de données.
     * @package SatellysReborn\BaseDonnees\DAO\Population\Groupe
     */
    class DAO_Promotion extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Promotion $obj l'objet à insérer dans la base de données.
         * @return Promotion|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO promotion (nom, annee, id_departement)
                    VALUES (:nom, :annee, :departement)';

            $res = $this->connexion->insert($sql, array(
                ':nom' => $obj->getNom(),
                ':annee' => $obj->getAnnee(),
                ':departement' => $obj->getDepartement()->getId()
            ));

            // Insertion ok ?
            if ($res) {
                return new Promotion($res, $obj->getNom(), $obj->getAnnee(),
                                     $obj->getDepartement());
            }

            // else
            return false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Promotion $obj l'objet à modifier dans la base de données.
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
            $sql = 'UPDATE promotion SET
                        nom = :nom,
                        annee = :annee,
                        id_departement = :dep
                    WHERE id = :id';

            return $this->connexion->update($sql, array(
                ':nom' => $obj->getNom(),
                ':annee' => $obj->getAnnee(),
                ':dep' => $obj->getDepartement()->getId(),
                ':id' => $obj->getId()
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Promotion $obj l'objet à supprimer dans la base de données.
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
            $sql = 'DELETE FROM promotion
                    WHERE id = :id';

            return $this->connexion->delete($sql, array(
                ':id' => $obj->getId()
            ));
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Promotion
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT nom, annee, id_departement
                    FROM promotion
                    WHERE id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $dep = DAO_Factory::getDAO_Departement()
                              ->find($resBD[0]->id_departement);

            return new Promotion($cle, $resBD[0]->nom, $resBD[0]->annee, $dep);
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
            $sql = 'SELECT id, nom, annee, id_departement
                    FROM promotion';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Promotion.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                $dep = DAO_Factory::getDAO_Departement()
                                  ->find($obj->id_departement);

                array_push($res, new Promotion(
                    $obj->id, $obj->nom, $obj->annee, $dep
                ));
            }

            return $res;
        }
    }