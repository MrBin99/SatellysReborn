<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO\Cours;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO;
    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Cours\Absence;

    /**
     * DAO permettant de gérer les absences en base de données.
     * @package WS_SatellysReborn\BaseDonnees\DAO\Cours
     */
    class DAO_Absence extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Absence $obj l'objet à insérer dans la base de données.
         * @return Absence|string
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO absence
                    VALUES (:cours, :etudiant, :justifie, :motif)';

            $res = $this->connexion->insert($sql, array(
                ':cours' => $obj->getCours()->getId(),
                ':etudiant' => $obj->getEtudiant()->getId(),
                ':justifie' => $obj->estJustifie(),
                ':motif' => $obj->getMotif()
            ));

            return $res ? $obj : false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Absence $obj l'objet à modifier dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la modification a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function update($obj) {
            // Pré-condition.
            if (is_null($obj->getCours()) || !is_null($obj->getEtudiant())
            ) {
                return false;
            }
            // else

            // SQL.
            $sql = 'UPDATE absence SET
                        justifie = :justifie,
                        motif = :motif
                    WHERE id_cours = :cours
                    AND id_etudiant = :etudiant';

            return $this->connexion->update($sql, array(
                ':justifie' => $obj->estJustifie(),
                ':motif' => $obj->getMotif(),
                ':cours' => $obj->getCours()->getId(),
                ':etudiant' => $obj->getEtudiant()->getId()
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Absence $obj l'objet à supprimer dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la suppression a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function delete($obj) {
            // Pré-condition.
            if (is_null($obj->getCours()) || !is_null($obj->getEtudiant())
            ) {
                return false;
            }

            // SQL.
            $sql = 'DELETE FROM absence
                    WHERE id_cours = :cours
                    AND id_etudiant = :etudiant';

            return $this->connexion->delete($sql, array(
                ':cours' => $obj->getCours()->getId(),
                ':etudiant' => $obj->getEtudiant()->getId()
            ));
        }

        /**
         * Non utilisé.
         * @see getAbsence()
         */
        public function find($cle) {
            return null;
        }

        /**
         * Retourne l'absence dont le cours et l'étudiant sont passé en
         * argument.
         * @param $coursID string l'identifiant du cours.
         * @param $etudiantID string l'identifiant de l'étudiant.
         * @return Absence
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function getAbsence($coursID, $etudiantID) {
            // SQL.
            $sql = 'SELECT justifie, motif
                    FROM absence
                    WHERE id_cours = :cours
                    AND id_etudiant = :etudiant';

            $resBD = $this->connexion->select($sql, array(
                ':cours' => $coursID,
                ':etudiant' => $etudiantID
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $cours = DAO_Factory::getDAO_Cours()->find($coursID);
            $etudiant = DAO_Factory::getDAO_Etudiant()->find($etudiantID);

            return new Absence($cours, $etudiant, $resBD[0]->justifie,
                               $resBD[0]->motif);
        }

        /**
         * On ne cherche jamais toutes les absences.
         */
        public function findAll() {
        }

        /**
         * Retourne les absences à un cours.
         * @param $coursID string l'identifiant du cours.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function getAbsencesCours($coursID) {
            // SQL.
            $sql = 'SELECT justifie, motif, id_etudiant
                    FROM absence
                    WHERE id_cours = :cours';

            $resBD = $this->connexion->select($sql, array(
                ':cours' => $coursID
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Groupes.
            $res = array();

            // Le cours concerné.
            $cours = DAO_Factory::getDAO_Cours()->find($coursID);

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                $etudiant =
                    DAO_Factory::getDAO_Etudiant()->find($obj->id_etudiant);

                array_push($res, new Absence(
                    $cours, $etudiant, $obj->justifie, $obj->motif
                ));
            }

            return $res;
        }

        /**
         * Retourne les absences d'un étudiant.
         * @param $etudiantID string l'identifiant de l'étudiant.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function getAbsencesEtudiant($etudiantID) {
            // SQL.
            $sql = 'SELECT justifie, motif, id_cours
                    FROM absence
                    WHERE id_etudiant = :etudiant';

            $resBD = $this->connexion->select($sql, array(
                ':etudiant' => $etudiantID
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Groupes.
            $res = array();

            // L'étudiant concerné.
            $etudiant = DAO_Factory::getDAO_Etudiant()->find($etudiantID);

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                $cours =
                    DAO_Factory::getDAO_Cours()->find($obj->id_cours);

                array_push($res, new Absence(
                    $cours, $etudiant, $obj->justifie, $obj->motif
                ));
            }

            return $res;
        }
    }