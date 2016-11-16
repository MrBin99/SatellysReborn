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
            $sql = 'INSERT INTO etudiant
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

            return !$res ? false : $obj;
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
         * Retourne un tableau de tous les étudiants d'un groupe.
         * @param $groupeID string l'identifiant du groupe dont on veut avoir
         *     tous les étudiants.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findEtudiantsGroupe($groupeID) {
            // SQL.
            $sql = 'SELECT e.id AS id, ine, e.nom AS nom, prenom, tel, 
                           email, id_adresse
                    FROM etudiant e
                    JOIN faitpartie f ON e.id = f.id_etudiant
                    JOIN groupe g ON f.id_groupe = g.id
                    WHERE g.id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $groupeID
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Promotion.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
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
         * Retourne un tableau de tous les étudiants d'une promotion.
         * @param $promotionID string l'identifiant de la promotion dont on
         *     veut avoir tous les étudiants.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findEtudiantsPromotion($promotionID) {
            // SQL.
            $sql = 'SELECT e.id AS id, ine, e.nom AS nom, prenom, tel, 
                           email, id_adresse
                    FROM etudiant e
                    JOIN faitpartie f ON e.id = f.id_etudiant
                    JOIN groupe g ON f.id_groupe = g.id
                    WHERE g.id_promotion = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $promotionID
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Promotion.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
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
         * Retourne un tableau de tous les étudiants d'un département.
         * @param $departementID string l'identifiant du département dont on
         *     veut avoir tous les étudiants.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findEtudiantsDepartement($departementID) {
            // SQL.
            $sql = 'SELECT e.id AS id, ine, e.nom AS nom, prenom, tel, 
                           email, id_adresse
                    FROM etudiant e
                    JOIN faitpartie f ON e.id = f.id_etudiant
                    JOIN groupe g ON f.id_groupe = g.id
                    JOIN promotion p ON g.id_promotion = p.id
                    JOIN departement d ON p.id_departement = d.id
                    WHERE d.id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $departementID
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Promotion.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
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