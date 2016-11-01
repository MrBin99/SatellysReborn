<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO;
    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Adresse\Adresse;

    /**
     * DAO permettant de gérer des adresses en base de données.
     * @package WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse
     */
    class DAO_Adresse extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Adresse $obj l'objet à insérer dans la base de données.
         * @return Adresse|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO adresse (adresse1, adresse2, adresse3, numinsee_ville)
                    VALUES (:adresse1, :adresse2, :adresse3, :ville)';

            $res = $this->connexion->insert($sql, array(
                ':adresse1' => $obj->getAdresse1(),
                ':adresse2' => $obj->getAdresse2(),
                ':adresse3' => $obj->getAdresse3(),
                ':ville' => $obj->getVille()->getNumInsee()
            ));

            // Insertion ok ?
            if ($res) {
                return new Adresse($res, $obj->getAdresse1(),
                                   $obj->getAdresse2(),
                                   $obj->getAdresse3(), $obj->getVille());
            }

            // else
            return false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Adresse $obj l'objet à modifier dans la base de données.
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
            $sql = 'UPDATE adresse SET
                        adresse1 = :adresse1,
                        adresse2 = :adresse2,
                        adresse3 = :adresse3,
                        numinsee_ville = :ville
                    WHERE id = :id';

            return $this->connexion->update($sql, array(
                ':adresse1' => $obj->getAdresse1(),
                ':adresse2' => $obj->getAdresse2(),
                ':adresse3' => $obj->getAdresse3(),
                ':ville' => $obj->getVille()->getNumInsee(),
                'id' => $obj->getId()
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Adresse $obj l'objet à supprimer dans la base de données.
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
            $sql = 'DELETE FROM adresse
                    WHERE id = :id';

            return $this->connexion->delete($sql, array(
                ':id' => $obj->getId()
            ));
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Adresse
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT adresse1, adresse2, adresse3, numinsee_ville
                    FROM adresse
                    WHERE id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $ville = DAO_Factory::getDAO_Ville()
                                ->find($resBD[0]->numinsee_ville);

            return new Adresse($cle, $resBD[0]->adresse1, $resBD[0]->adresse2,
                               $resBD[0]->adresse3, $ville);
        }

        /**
         * On ne va jamais chercher toutes les adresses.
         */
        public function findAll() {
        }
    }