<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO\Population\Login;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO;
    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Login\Utilisateur;

    /**
     * DAO permettant de gérer les utilisateurs en base de données.
     * @package WS_SatellysReborn\BaseDonnees\DAO\Population\Login
     */
    class DAO_Utilisateur extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Utilisateur $obj l'objet à insérer dans la base de données.
         * @return Utilisateur|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO utilisateur
                    VALUES (:login, :mdp, :email, :enseignant, :etudiant, :admin)';

            $res = $this->connexion->insert($sql, array(
                ':login' => $obj->getLogin(),
                ':mdp' => $obj->getEmail(),
                ':enseignant' => $obj->getEnseignant()->getId(),
                ':etudiant' => $obj->getEtudiant()->getId(),
                ':admin' => $obj->getAdministratif()->getId()
            ));

            return $res ? $obj : false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Utilisateur $obj l'objet à modifier dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la modification a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function update($obj) {
            // Pré-condition.
            if (is_null($obj->getLogin()) ||
                is_null($this->find($obj->getLogin()))
            ) {
                return false;
            }
            // else

            // SQL.
            $sql = 'UPDATE utilisateur SET
                        mdp = :mdp,
                        email = :email
                    WHERE login = :login';

            return $this->connexion->update($sql, array(
                ':mdp' => $obj->getMdp(),
                ':email' => $obj->getEmail()
            ));
        }

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Utilisateur $obj l'objet à supprimer dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la suppression a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function delete($obj) {
            // Pré-condition.
            if (is_null($obj->getLogin()) ||
                is_null($this->find($obj->getLogin()))
            ) {
                return false;
            }

            // SQL.
            $sql = 'DELETE FROM utilisateur
                    WHERE login = :login';

            return $this->connexion->delete($sql, array(
                ':login' => $obj->getLogin()
            ));
        }

        /**
         * Non utilisé.
         * @see findLoginMdp
         */
        public function find($cle) {
            return null;
        }

        /**
         * Reécupère l'utilisateur dont le login et le mot de passe est passé
         * en argument.
         * @param $login string le login de l'utilisateur.
         * @param $mdp string le mot de passe de l'utilisateur.
         * @return Utilisateur
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findLoginMdp($login, $mdp) {
            // SQL.
            $sql = 'SELECT login, mdp, id_administratif, id_enseignant, id_etudiant
                    FROM utilisateur
                    WHERE login = :login
                    AND mdp = :mdp';

            $resBD = $this->connexion->select($sql, array(
                ':login' => $login,
                ':mdp' => $mdp
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else

            $admin = null;
            $ensei = null;
            $etud = null;

            // Remplissage du champs de la personne correspondante.
            if ($resBD[0]->id_administratif != null) {
                $admin = DAO_Factory::getDAO_Administratif()
                                    ->find($resBD[0]->id_administratif);
            } elseif ($resBD[0]->id_enseignant != null) {
                $ensei = DAO_Factory::getDAO_Enseignant()
                                    ->find($resBD[0]->id_enseignant);
            } elseif ($resBD[0]->id_etudiant != null) {
                $etud = DAO_Factory::getDAO_Etudiant()
                                   ->find($resBD[0]->id_etudiant);
            }

            return new Utilisateur($resBD[0]->login, $resBD[0]->mdp,
                                   $resBD[0]->email, $ensei, $admin, $etud);
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
            $sql = 'SELECT login, mdp, id_administratif, id_enseignant, id_etudiant
                    FROM utilisateur';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Etudiant.
            $res = array();

            foreach ($resBD as $obj) {
                $admin = null;
                $ensei = null;
                $etud = null;

                // Remplissage du champs de la personne correspondante.
                if ($obj->id_administratif != null) {
                    $admin = DAO_Factory::getDAO_Administratif()
                                        ->find($obj->id_administratif);
                } elseif ($obj->id_enseignant != null) {
                    $ensei = DAO_Factory::getDAO_Enseignant()
                                        ->find($obj->id_enseignant);
                } elseif ($obj->id_etudiant != null) {
                    $etud = DAO_Factory::getDAO_Etudiant()
                                       ->find($obj->id_etudiant);
                }

                array_push($res,
                           new Utilisateur($obj->login, $obj->mdp,
                                           $obj->email, $ensei, $admin,
                                           $etud)
                );
            }

            return $res;
        }
    }