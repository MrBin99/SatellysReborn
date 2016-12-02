<?php
    namespace SatellysReborn\BaseDonnees\DAO\Population;

    use SatellysReborn\BaseDonnees\DAO\DAO;
    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Etudiant;

    /**
     * DAO permettant de gérer les étudiants en base de données.
     * @package SatellysReborn\BaseDonnees\DAO\Population
     */
    class DAO_Etudiant extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Etudiant $obj l'objet à insérer dans la base de données.
         * @return Etudiant|bool
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
            $sql = 'INSERT INTO etudiant (id, ine, nom, prenom, tel, email, id_adresse)
                    VALUES (:id, :ine, :nom, :prenom, :tel, :email, :adresse)';

            $res = $this->connexion->insert($sql, array(
                ':id' => $obj->getId(),
                ':ine' => $obj->getIne(),
                ':nom' => $obj->getNom(),
                ':prenom' => $obj->getPrenom(),
                ':tel' => $obj->getTel(),
                ':email' => $obj->getEmail(),
                ':adresse' => $obj->getAdresse()->getId()
            ));

            return $res === false ? false : $obj;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Etudiant $obj l'objet à modifier dans la base de données.
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
            $sql = 'UPDATE etudiant SET
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
         * @param Etudiant $obj l'objet à supprimer dans la base de données.
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
            $sql = 'DELETE FROM etudiant
                    WHERE id = :id';

            return $this->connexion->delete($sql, array(
                ':id' => $obj->getId()
            ));
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Etudiant
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT ine, nom, prenom, tel, email, id_adresse
                    FROM etudiant
                    WHERE id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else

            // Objets liés.
            $adresse =
                DAO_Factory::getDAO_Adresse()->find($resBD[0]->id_adresse);

            return new Etudiant($cle, $resBD[0]->ine, $resBD[0]->nom,
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
            $sql = 'SELECT id, ine, nom, prenom, tel, email, id_adresse
                    FROM etudiant';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Etudiant.
            $res = array();

            foreach ($resBD as $obj) {

                // Objets liés.
                $adresse =
                    DAO_Factory::getDAO_Adresse()->find($obj->id_adresse);

                array_push($res, new Etudiant($obj->id, $obj->ine, $obj->nom,
                                              $obj->prenom,
                                              $obj->tel,
                                              $obj->email, $adresse)
                );
            }

            return $res;
        }

        /**
         * Sélectionne tous les étudiants dont le nom, l'INE ou l'identifiant
         * est celui passé en argument.
         * @param $arg string l'argument de recherche.
         * @return array <ul>
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findNomIdIne($arg) {
            // SQL.
            $sql = 'SELECT id, ine, nom, prenom, tel, email, id_adresse
                    FROM etudiant e 
                    WHERE enleverAccents(lower(nom)) LIKE 
                          enleverAccents(lower(:arg))
                    OR ine LIKE :arg
                    OR id LIKE :arg';

            $resBD = $this->connexion->select($sql, array(
                ':arg' => '%' . $arg . '%'
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Etudiant.
            $res = array();

            foreach ($resBD as $obj) {

                // Objets liés.
                $adresse =
                    DAO_Factory::getDAO_Adresse()->find($obj->id_adresse);

                array_push($res, new Etudiant($obj->id, $obj->ine, $obj->nom,
                                              $obj->prenom,
                                              $obj->tel,
                                              $obj->email, $adresse)
                );
            }

            return $res;
        }

        /**
         * Récupère l'étudiant qui n'est pas absents à un cours et dont le nom,
         * prenom, INE ou identifiant est celui passé en argument.
         * @param $idCours string l'identifiant du cours.
         * @param $arg string l'argument de recherche.
         * @return array <ul>
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findNonAbsentCours($idCours, $arg) {
            // SQL.
            $sql = 'SELECT DISTINCT e.id AS id, e.ine AS ine, e.nom AS nom, 
                                    e.prenom AS prenom, email, tel, id_adresse
                    FROM cours c
                    JOIN assiste a ON c.id = a.id_cours
                    JOIN groupe g ON a.id_groupe = g.id
                    JOIN faitpartie f ON g.id = f.id_groupe
                    JOIN etudiant e ON f.id_etudiant = e.id
                    WHERE c.id = :id
                    AND id_etudiant NOT IN (
                        SELECT id_etudiant 
                        FROM absence a
                        WHERE a.id_cours = :id)
                    AND (enleverAccents(lower(e.nom)) LIKE 
                         enleverAccents(lower(:arg))
                    OR ine LIKE :arg
                    OR e.id LIKE :arg)';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $idCours,
                ':arg' => '%' . $arg . '%',
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Etudiant.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {

                // Objets liés.
                $adresse =
                    DAO_Factory::getDAO_Adresse()->find($obj->id_adresse);

                array_push($res, new Etudiant($obj->id, $obj->ine, $obj->nom,
                                              $obj->prenom,
                                              $obj->tel,
                                              $obj->email, $adresse)
                );
            }

            return $res;
        }
    }