<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO\Cours;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO;
    use WS_SatellysReborn\Modeles\Cours\Salle;

    /**
     * DAO permettant de gérer les salles de cours en base de données.
     * @package WS_SatellysReborn\BaseDonnees\DAO\Cours
     */
    class DAO_Salle extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Salle $obj l'objet à insérer dans la base de données.
         * @return Salle|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO salle
                    VALUES (:num, :capacite, :machine)';

            $res = $this->connexion->insert($sql, array(
                ':num' => $obj->getNum(),
                ':capacite' => $obj->getCapacite(),
                ':machine' => $obj->estSalleMachine()
            ));

            return $res != 1 ? $obj : false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Salle $obj l'objet à modifier dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la modification a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function update($obj) {
            // Pré-condition.
            if (is_null($obj->getNum()) ||
                is_null($this->find($obj->getNum()))
            ) {
                return false;
            }
            // else

            // SQL.
            $sql = 'UPDATE salle SET
                        capacite = :capacite,
                        machine = :machine
                    WHERE num = :num';

            return $this->connexion->update($sql, array(
                ':capacite' => $obj->getCapacite(),
                ':machine' => $obj->estSalleMachine(),
                ':num' => $obj->getNum()
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Salle $obj l'objet à supprimer dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la suppression a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function delete($obj) {
            // Pré-condition.
            if (is_null($obj->getNum()) ||
                is_null($this->find($obj->getNum()))
            ) {
                return false;
            }

            // SQL.
            $sql = 'DELETE FROM salle
                    WHERE num = :num';

            return $this->connexion->delete($sql, array(
                ':num' => $obj->getNum()
            ));
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Salle
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT capacite, machine
                    FROM salle
                    WHERE num = :num';

            $resBD = $this->connexion->select($sql, array(
                ':num' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            return new Salle($cle, $resBD[0]->capacite, $resBD[0]->machine);
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
            $sql = 'SELECT num, capacite, machine
                    FROM salle';

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
                array_push($res, new Salle(
                    $obj->num, $obj->capacite, $obj->machine
                ));
            }

            return $res;
        }
    }