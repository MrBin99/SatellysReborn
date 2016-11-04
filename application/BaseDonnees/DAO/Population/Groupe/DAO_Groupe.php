<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO\Population\Groupe;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO;
    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Modele;
    use WS_SatellysReborn\Modeles\Population\Etudiant;
    use WS_SatellysReborn\Modeles\Population\Groupe\Groupe;

    /**
     * DAO permettant de gérer les groupes d'étudiants en base de données.
     * @package WS_SatellysReborn\BaseDonnees\DAO\Population\Groupe
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
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Modele
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
         * On ira jamais chercher tous les groupes.
         */
        public function findAll() {
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
        public function getEtudiants($groupeID) {
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
    }