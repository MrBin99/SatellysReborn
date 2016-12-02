<?php
    namespace SatellysReborn\BaseDonnees\DAO\Cours;

    use SatellysReborn\BaseDonnees\DAO\DAO;
    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Cours\Cours;

    /**
     * DAO permettant de gérer les cours en base de données.
     * @package SatellysReborn\BaseDonnees\DAO\Cours
     */
    class DAO_Cours extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Cours $obj l'objet à insérer dans la base de données.
         * @return Cours|string
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO cours (id_matiere, id_enseignant, salle, 
                                       jour, debut, fin)
                    VALUES (:matiere, :enseignant, :salle, :jour, :debut, :fin)';

            $res = $this->connexion->insert($sql, array(
                ':matiere' => $obj->getMatiere()->getId(),
                ':enseignant' => $obj->getEnseignant()->getId(),
                ':salle' => $obj->getSalle(),
                ':jour' => $obj->getJour(),
                ':debut' => $obj->getDebut(),
                ':fin' => $obj->getFin()
            ));

            // Insertion ok ?
            if ($res) {
                return new Cours($res, $obj->getMatiere(),
                                 $obj->getEnseignant(),
                                 $obj->getSalle(),
                                 $obj->getJour(),
                                 $obj->getDebut(),
                                 $obj->getFin()
                );
            }

            // else

            return false;
        }

        /**
         * Ajoute un groupe qui assiste à un cours.
         * @param $coursId string l'identifiant du cours.
         * @param $groupeId string l'identifiant du groupe.
         * @return bool si l'ajout a été effectué.
         */
        public function ajouterCours($coursId, $groupeId) {
            // SQL.
            $sql = 'INSERT INTO assiste
                    VALUES (:cours, :groupe)';
            $res = $this->connexion->insert($sql, array(
                ':cours' => $coursId,
                ':groupe' => $groupeId
            ));

            return $res != false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Cours $obj l'objet à modifier dans la base de données.
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
            $sql = 'UPDATE cours SET
                        id_matiere = :matiere,
                        id_enseignant = :enseignant,
                        salle = :salle,
                        jour = :jour,
                        debut = :debut,
                        fin = :fin
                    WHERE id = :id';

            return $this->connexion->update($sql, array(
                ':matiere' => $obj->getMatiere()->getId(),
                ':enseignant' => $obj->getEnseignant()->getId(),
                ':salle' => $obj->getSalle(),
                ':jour' => $obj->getJour(),
                ':debut' => $obj->getDebut(),
                ':fin' => $obj->getFin(),
                ':id' => $obj->getId()
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Cours $obj l'objet à supprimer dans la base de données.
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
            $sql = 'DELETE FROM cours
                    WHERE id = :id';

            return $this->connexion->delete($sql, array(
                ':id' => $obj->getId()
            ));
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Cours
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT id_matiere, id_enseignant, salle, 
                           jour, debut, fin
                    FROM cours
                    WHERE id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $matiere =
                DAO_Factory::getDAO_Matiere()->find($resBD[0]->id_matiere);
            $enseignant = DAO_Factory::getDAO_Enseignant()
                                     ->find($resBD[0]->id_enseignant);
            $cours = new Cours($cle, $matiere, $enseignant, $resBD[0]->salle,
                               $resBD[0]->jour, $resBD[0]->debut,
                               $resBD[0]->fin);

            // Ajout des groupes.
            $groupes = $this->getGroupes($cle);

            if ($groupes != null) {
                foreach ($groupes as $groupe) {
                    $cours->ajouterGroupe($groupe);
                }
            }

            return $cours;
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
            $sql = 'SELECT id, id_matiere, id_enseignant, salle,
                           jour, debut, fin
                    FROM cours';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Pays.
            $res = array();

            foreach ($resBD as $obj) {
                $matiere =
                    DAO_Factory::getDAO_Matiere()->find($resBD[0]->id_matiere);
                $enseignant = DAO_Factory::getDAO_Enseignant()
                                         ->find($resBD[0]->id_enseignant);

                array_push($res,
                           new Cours($obj->id, $matiere, $enseignant,
                                     $obj->salle, $obj->jour, $obj->debut,
                                     $obj->fin));
            }

            return $res;
        }

        /**
         * Retourne le cours dont le nom, la date et les heures de début
         * et de fin sont passées en argument.
         * @param $nom string le nom de la matière du cours.
         * @param $date string la date du cours.
         * @param $debut string l'heure de début du cours.
         * @param $fin string l'heure de fin du cours.
         * @return Cours
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findNameDateHeure($nom, $date, $debut, $fin) {
            // Reverse la date.
            $date = explode('-', $date);
            $date = implode('-', array_reverse($date));

            // SQL.
            $sql = 'SELECT c.id AS id, id_matiere, id_enseignant, salle, 
                           jour, debut, fin
                    FROM cours c
                    JOIN matiere m ON c.id_matiere = m.id
                    WHERE lower(nom) = lower(:nom)
                    AND jour = :jour
                    AND c.debut = :debut
                    AND fin = :fin';

            $resBD = $this->connexion->select($sql, array(
                ':nom' => $nom,
                ':jour' => $date,
                ':debut' => $debut,
                ':fin' => $fin,
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $matiere =
                DAO_Factory::getDAO_Matiere()->find($resBD[0]->id_matiere);
            $enseignant = DAO_Factory::getDAO_Enseignant()
                                     ->find($resBD[0]->id_enseignant);
            $cours = new Cours($resBD[0]->id, $matiere, $enseignant,
                               $resBD[0]->salle, $resBD[0]->jour,
                               $resBD[0]->debut, $resBD[0]->fin);

            // Ajout des groupes.
            $groupes = $this->getGroupes($resBD[0]->id);

            if ($groupes != null) {
                foreach ($groupes as $groupe) {
                    $cours->ajouterGroupe($groupe);
                }
            }

            return $cours;
        }

        /**
         * Retourne les groupes qui assistent au cours dont l'identifiant est
         * passé en argument.
         * @param $coursID string l'identifiant du cours.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function getGroupes($coursID) {
            // SQL.
            $sql = 'SELECT g.id AS id
                    FROM groupe g
                    JOIN assiste a ON g.id = a.id_groupe
                    WHERE a.id_cours = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $coursID
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Groupes.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                array_push($res, DAO_Factory::getDAO_Groupe()->find($obj->id));
            }

            return $res;
        }

        /**
         * Récupère tous les cours d'un enseignant.
         * @param $enseignant string l'identifiant de l'ensaignant.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findCoursEnseignant($enseignant) {
            // SQL.
            $sql = 'SELECT c.id AS id, debut, fin, jour, salle, 
                           id_matiere, id_enseignant
                    FROM cours c
                    JOIN enseignant e ON c.id_enseignant = e.id
                    WHERE e.id = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $enseignant
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Groupes.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {

                // Objets liés.
                $matiere =
                    DAO_Factory::getDAO_Matiere()->find($obj->id_matiere);
                $enseignant = DAO_Factory::getDAO_Enseignant()
                                         ->find($obj->id_enseignant);
                $groupes = $this->getGroupes($obj->id);
                $cours = new Cours($obj->id, $matiere, $enseignant,
                                   $obj->salle, $obj->jour, $obj->debut,
                                   $obj->fin);

                // Ajoute les groupes.
                if (isset($groupes)) {
                    foreach ($groupes as $groupe) {
                        $cours->ajouterGroupe($groupe);
                    }
                }

                array_push($res, $cours);
            }

            return $res;
        }
    }