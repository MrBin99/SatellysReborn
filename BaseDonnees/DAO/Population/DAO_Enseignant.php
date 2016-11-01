<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO\Population;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO;
    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Enseignant;

    /**
     * DAO permettant de gérer les enseignants en base de données.
     * @package WS_SatellysReborn\BaseDonnees\DAO\Population
     */
    class DAO_Enseignant extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Enseignant $obj l'objet à insérer dans la base de données.
         * @return Enseignant|string
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // Pré-condition
            if (is_null($obj->getId()) ||
                !is_null($this->find($obj->getId()))
            ) {
                return false;
            }

            // SQL.
            $sql = 'INSERT INTO enseignant
                    VALUES (:id, :nom, :prenom, :tel, :email, :adresse)';

            $res = $this->connexion->insert($sql, array(
                ':id' => $obj->getId(),
                ':nom' => $obj->getNom(),
                ':prenom' => $obj->getPrenom(),
                ':tel' => $obj->getTel(),
                ':email' => $obj->getEmail(),
                ':adresse' => $obj->getAdresse()->getId()
            ));

            return $res ? $obj : false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Enseignant $obj l'objet à modifier dans la base de données.
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
            $sql = 'UPDATE enseignant SET
                        nom = :nom,
                        prenom = :prenom,
                        tel = :tel,
                        email = :email,
                        id_adresse = :adresse
                    WHERE id = :id';

            return $this->connexion->update($sql, array(
                ':nom' => $obj->getNom(),
                ':prenom' => $obj->getPrenom(),
                ':tel' => $obj->getTel(),
                ':email' => $obj->getEmail(),
                ':adresse' => $obj->getAdresse()->getId(),
                ':id' => $obj->getId(),
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Enseignant $obj l'objet à supprimer dans la base de données.
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
            $sql = 'DELETE FROM enseignant
                    WHERE id = :id';

            return $this->connexion->delete($sql, array(
                ':id' => $obj->getId()
            ));
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Enseignant
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT id, nom, prenom, tel, email, id_adresse
                    FROM enseignant
                    WHERE id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else

            $adresse =
                DAO_Factory::getDAO_Adresse()->find($resBD[0]->id_adresse);

            return new Enseignant($resBD[0]->id, $resBD[0]->nom,
                                  $resBD[0]->prenom, $resBD[0]->tel,
                                  $resBD[0]->email, $adresse);
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
            $sql = 'SELECT id, nom, prenom, tel, email, id_adresse
                    FROM enseignant';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Enseignant.
            $res = array();

            foreach ($resBD as $obj) {
                $adresse =
                    DAO_Factory::getDAO_Adresse()->find($obj->id_adresse);

                array_push($res, new Enseignant($obj->id, $obj->nom,
                                                $obj->prenom,
                                                $obj->tel,
                                                $obj->email, $adresse)
                );
            }

            return $res;
        }
    }