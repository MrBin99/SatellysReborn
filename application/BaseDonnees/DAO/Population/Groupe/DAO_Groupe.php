<?php
    namespace SatellysReborn\BaseDonnees\DAO\Population\Groupe;

    use SatellysReborn\BaseDonnees\DAO\DAO;
    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Etudiant;
    use SatellysReborn\Modeles\Population\Groupe\Groupe;

    /**
     * DAO permettant de gérer les groupes d'étudiants en base de données.
     * @package SatellysReborn\BaseDonnees\DAO\Population\Groupe
     */
    class DAO_Groupe extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Groupe $obj l'objet à insérer dans la base de données.
         * @return Groupe|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO groupe (nom, id_promotion)
                    VALUES (:nom, :promo)';

            $res = $this->connexion->insert($sql, array(
                ':nom' => $obj->getNom(),
                ':promo' => $obj->getPromo()->getId()
            ));

            // Insertion ok ?
            if ($res) {
                return new Groupe($res, $obj->getNom(), $obj->getPromo());
            }

            // else
            return false;
        }

        /**
         * Ajoute un étudiant dans un groupe.
         * @param $groupeID string l'identifiant du groupe.
         * @param $etudiantID string l'identifiant de l'étudiant.
         * @return bool si l'étudiant a bien été affecté au groupe.
         */
        public function ajouterEtudiant($groupeID, $etudiantID) {
            // SQL.
            $sql = 'INSERT INTO faitpartie
                    VALUES (:etudiant, :groupe)';

            return $this->connexion->insert($sql, array(
                ':etudiant' => $etudiantID,
                ':groupe' => $groupeID
            )) != '' ? true : false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Groupe $obj l'objet à modifier dans la base de données.
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
            $sql = 'UPDATE groupe SET
                        nom = :nom,
                        id_promotion = :promo
                    WHERE id = :id';

            return $this->connexion->update($sql, array(
                ':nom' => $obj->getNom(),
                ':promo' => $obj->getPromo()->getId(),
                ':id' => $obj->getId(),
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Groupe $obj l'objet à supprimer dans la base de données.
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
            $sql = 'DELETE FROM groupe
                    WHERE id = :id';

            return $this->connexion->delete($sql, array(
                ':id' => $obj->getId()
            ));
        }

        /**
         * Supprime un étudiant d'un groupe.
         * @param $groupeID string l'identifiant du groupe.
         * @param $etudiantID string l'identifiant de l'étudiant.
         * @return bool si l'étudiant a bien été supprimé du groupe.
         */
        public function supprimerEtudiant($groupeID, $etudiantID) {
            // SQL.
            $sql = 'DELETE FROM faitpartie
                    WHERE id_etudiant = :etudiant
                    AND id_groupe = :groupe';

            return $this->connexion->delete($sql, array(
                ':etudiant' => $etudiantID,
                ':groupe' => $groupeID
            )) != '' ? true : false;
        }

        /**
         * Supprime un étudiant de tous les groupes auquel il est présent.
         * @param $etudiant string l'identifiant de l'étudiant.
         * @return bool si l'étudiant a bien été supprimé des groupes.
         */
        public function supprimerEtudiantGroupes($etudiant) {
            // SQL.
            $sql = 'DELETE FROM faitpartie
                    WHERE id_etudiant = :etudiant';

            return $this->connexion->delete($sql, array(
                ':etudiant' => $etudiant,
            )) != '' ? true : false;
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Groupe
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT nom, id_promotion
                    FROM groupe
                    WHERE id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $promo = DAO_Factory::getDAO_Promotion()
                                ->find($resBD[0]->id_promotion);

            // Ajout des étudiants.
            $groupe = new Groupe($cle, $resBD[0]->nom, $promo);
            $etudiants = $this->getEtudiants($cle);

            if ($etudiants != null) {
                foreach ($etudiants as $etudiant) {
                    $groupe->ajouterEtudiant($etudiant);
                }
            }

            return $groupe;
        }

        /**
         * Retourne le groupe dont le nom est passé en argument.
         * @param $nom string le nom du groupe.
         * @return Groupe
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findNom($nom) {
            // SQL.
            $sql = 'SELECT id, nom, id_promotion
                    FROM groupe
                    WHERE lower(nom) LIKE lower(:nom)';

            $resBD = $this->connexion->select($sql, array(
                ':nom' => '%' . $nom . '%'
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $promo = DAO_Factory::getDAO_Promotion()
                                ->find($resBD[0]->id_promotion);

            // Ajout des étudiants.
            $groupe = new Groupe($resBD[0]->id, $resBD[0]->nom, $promo);
            $etudiants = $this->getEtudiants($resBD[0]->id);

            if ($etudiants != null) {
                foreach ($etudiants as $etudiant) {
                    $groupe->ajouterEtudiant($etudiant);
                }
            }

            return $groupe;
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
            $sql = 'SELECT id, nom, id_promotion
                    FROM groupe';

            $resBD = $this->connexion->select($sql, array());

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en Groupe.
            $res = array();

            foreach ($resBD as $obj) {
                $promo = DAO_Factory::getDAO_Promotion()
                                    ->find($obj->id_promotion);

                // Ajout des étudiants.
                $groupe = new Groupe($obj->id, $obj->nom, $promo);
                $etudiants = $this->getEtudiants($obj->id);

                if ($etudiants != null) {
                    foreach ($etudiants as $etudiant) {
                        $groupe->ajouterEtudiant($etudiant);
                    }
                }

                array_push($res, $groupe);
            }

            return $res;
        }

        /**
         * Sélectionne tous les groupes dont le nom ou l'identifiant
         * correspondant à celui passé en argument.
         * @param $arg string l'argument de recherche.
         * @return array <ul>
         * <ul>
         * <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         * <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findIdNom($arg) {
            // SQL.
            $sql = 'SELECT id, nom, id_promotion
                    FROM groupe
                    WHERE enleverAccents(lower(nom)) LIKE enleverAccents(lower(:arg))
                    OR id LIKE :arg';

            $resBD = $this->connexion->select($sql, array(
                ":arg" => '%' . $arg . '%'
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en Groupe.
            $res = array();

            foreach ($resBD as $obj) {
                $promo = DAO_Factory::getDAO_Promotion()
                                    ->find($obj->id_promotion);

                // Ajout des étudiants.
                $groupe = new Groupe($obj->id, $obj->nom, $promo);
                $etudiants = $this->getEtudiants($obj->id);

                if ($etudiants != null) {
                    foreach ($etudiants as $etudiant) {
                        $groupe->ajouterEtudiant($etudiant);
                    }
                }

                array_push($res, $groupe);
            }

            return $res;
        }

        /**
         * Sélectionne les groupes faisant partie d'une promotion.
         * @param $promotion string l'identifiant de la promotion.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findPromotion($promotion) {
            // SQL.
            $sql = 'SELECT id, nom, id_promotion
                    FROM groupe
                    WHERE id_promotion = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $promotion
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en Groupe.
            $res = array();

            foreach ($resBD as $obj) {
                $promo = DAO_Factory::getDAO_Promotion()
                                    ->find($obj->id_promotion);

                // Ajout des étudiants.
                $groupe = new Groupe($obj->id, $obj->nom, $promo);
                $etudiants = $this->getEtudiants($obj->id);

                if ($etudiants != null) {
                    foreach ($etudiants as $etudiant) {
                        $groupe->ajouterEtudiant($etudiant);
                    }
                }

                array_push($res, $groupe);
            }

            return $res;
        }

        /**
         * Sélectionne les groupes dont létudiant passé en arhument fait partie.
         * @param $etudiant string l'identifiant de l'étudiant.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findEtudiantGroupes($etudiant) {
            // SQL.
            $sql = 'SELECT id, nom, id_promotion
                    FROM groupe g
                    JOIN faitpartie f ON g.id = f.id_groupe
                    WHERE id_etudiant = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $etudiant
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en Groupe.
            $res = array();

            foreach ($resBD as $obj) {
                $promo = DAO_Factory::getDAO_Promotion()
                                    ->find($obj->id_promotion);

                // Ajout des étudiants.
                $groupe = new Groupe($obj->id, $obj->nom, $promo);
                $etudiants = $this->getEtudiants($obj->id);

                if ($etudiants != null) {
                    foreach ($etudiants as $etudiant) {
                        $groupe->ajouterEtudiant($etudiant);
                    }
                }

                array_push($res, $groupe);
            }

            return $res;
        }

        /**
         * Retourne un tableau contenant les étudiants du groupe.
         * @param $groupeId string l'identifiant du groupe.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function getEtudiants($groupeId) {
            // SQL.
            $sql = 'SELECT e.id AS id, ine, e.nom AS nom, prenom, tel, 
                           email, id_adresse
                    FROM etudiant e
                    JOIN faitpartie f ON e.id = f.id_etudiant
                    JOIN groupe g ON f.id_groupe = g.id
                    WHERE g.id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $groupeId
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // Convertit en objet Groupe.
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