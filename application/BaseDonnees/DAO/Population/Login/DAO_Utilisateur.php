<?php
    namespace SatellysReborn\BaseDonnees\DAO\Population\Login;

    use SatellysReborn\BaseDonnees\DAO\DAO;
    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;

    /**
     * DAO permettant de gérer les utilisateurs en base de données.
     * @package SatellysReborn\BaseDonnees\DAO\Population\Login
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
            // Pré-condition
            if (is_null($obj->getLogin()) ||
                !is_null($this->find($obj->getLogin()))
            ) {
                return false;
            }

            // SQL.
            $sql = 'INSERT INTO utilisateur
                    VALUES (:login, :mdp, :email, :enseignant, :admin)';

            $res = $this->connexion->insert($sql, array(
                ':login' => $obj->getLogin(),
                ':mdp' => $obj->getMdp(),
                ':email' => $obj->getEmail(),
                ':enseignant' => $obj->getEnseignant() ?
                    $obj->getEnseignant()->getId() : null,
                ':admin' => $obj->getAdministratif() ?
                    $obj->getAdministratif()->getId() : null
            ));

            return $res === false ? false : $obj;
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
                ':email' => $obj->getEmail(),
                ':login' => $obj->getLogin()
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
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Utilisateur
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT login, mdp, email, id_enseignant, id_administratif
                    FROM utilisateur
                    WHERE login = :login';

            $resBD = $this->connexion->select($sql, array(
                ':login' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else

            $admin = null;
            $ensei = null;

            // Remplissage du champs de la personne correspondante.
            if ($resBD[0]->id_administratif != null) {
                $admin = DAO_Factory::getDAO_Administratif()
                                    ->find($resBD[0]->id_administratif);
            } elseif ($resBD[0]->id_enseignant != null) {
                $ensei = DAO_Factory::getDAO_Enseignant()
                                    ->find($resBD[0]->id_enseignant);
            }

            return new Utilisateur($resBD[0]->login, $resBD[0]->mdp,
                                   $resBD[0]->email,
                                   $ensei, $admin);
        }

        /**
         * Sélectionne l'utilisateur dont l'enseignant est passé en paramètre.
         * @param $idEnseignant string l'identifiant de l'enseignant.
         * @return Utilisateur
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findUtilisateurEnseignant($idEnseignant) {
            // SQL.
            $sql = 'SELECT login, mdp, email, id_enseignant, id_administratif
                    FROM utilisateur
                    WHERE id_enseignant = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $idEnseignant
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else

            $admin = null;
            $ensei = null;

            // Remplissage du champs de la personne correspondante.
            if ($resBD[0]->id_administratif != null) {
                $admin = DAO_Factory::getDAO_Administratif()
                                    ->find($resBD[0]->id_administratif);
            } elseif ($resBD[0]->id_enseignant != null) {
                $ensei = DAO_Factory::getDAO_Enseignant()
                                    ->find($resBD[0]->id_enseignant);
            }

            return new Utilisateur($resBD[0]->login, $resBD[0]->mdp,
                                   $resBD[0]->email,
                                   $ensei, $admin);
        }

        /**
         * Sélectionne l'utilisateur dont l'administratif est passé en
         * paramètre.
         * @param $idAdmin string l'identifiant de l'administratif.
         * @return Utilisateur
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findUtilisateurAdministratif($idAdmin) {
            // SQL.
            $sql = 'SELECT login, mdp, email, id_enseignant, id_administratif
                    FROM utilisateur
                    WHERE id_administratif = :id';

            $resBD = $this->connexion->select($sql, array(
                ':id' => $idAdmin
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else

            $admin = null;
            $ensei = null;

            // Remplissage du champs de la personne correspondante.
            if ($resBD[0]->id_administratif != null) {
                $admin = DAO_Factory::getDAO_Administratif()
                                    ->find($resBD[0]->id_administratif);
            } elseif ($resBD[0]->id_enseignant != null) {
                $ensei = DAO_Factory::getDAO_Enseignant()
                                    ->find($resBD[0]->id_enseignant);
            }

            return new Utilisateur($resBD[0]->login, $resBD[0]->mdp,
                                   $resBD[0]->email,
                                   $ensei, $admin);
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
            $sql = 'SELECT login, mdp, email, id_administratif, id_enseignant
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

            // Remplissage du champs de la personne correspondante.
            if ($resBD[0]->id_administratif != null) {
                $admin = DAO_Factory::getDAO_Administratif()
                                    ->find($resBD[0]->id_administratif);
            } elseif ($resBD[0]->id_enseignant != null) {
                $ensei = DAO_Factory::getDAO_Enseignant()
                                    ->find($resBD[0]->id_enseignant);
            }

            return new Utilisateur($resBD[0]->login, $resBD[0]->mdp,
                                   $resBD[0]->email,
                                   $ensei, $admin);
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
            $sql = 'SELECT login, mdp, email, id_administratif, id_enseignant
                    FROM utilisateur';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Utilisateur.
            $res = array();

            foreach ($resBD as $obj) {
                $admin = null;
                $ensei = null;

                // Remplissage du champs de la personne correspondante.
                if ($obj->id_administratif != null) {
                    $admin = DAO_Factory::getDAO_Administratif()
                                        ->find($obj->id_administratif);
                } elseif ($obj->id_enseignant != null) {
                    $ensei = DAO_Factory::getDAO_Enseignant()
                                        ->find($obj->id_enseignant);
                }

                array_push($res,
                           new Utilisateur($obj->login, $obj->mdp,
                                           $obj->email, $ensei, $admin)
                );
            }

            return $res;
        }

        /**
         * Récupère les utilisateurs dont le nom correspond à celui passé en
         * argument.
         * @param $nom string l'argument de recherche.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findNom($nom) {
            // SQL.
            $sql = 'SELECT login, mdp, email, id_administratif, id_enseignant
                    FROM utilisateur
                    WHERE enleverAccents(lower(login)) LIKE
                          enleverAccents(lower(login))';

            $resBD = $this->connexion->select($sql, array(
                ":nom" => '%' . $nom . '%'
            ));

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Utilisateur.
            $res = array();

            foreach ($resBD as $obj) {
                $admin = null;
                $ensei = null;

                // Remplissage du champs de la personne correspondante.
                if ($obj->id_administratif != null) {
                    $admin = DAO_Factory::getDAO_Administratif()
                                        ->find($obj->id_administratif);
                } elseif ($obj->id_enseignant != null) {
                    $ensei = DAO_Factory::getDAO_Enseignant()
                                        ->find($obj->id_enseignant);
                }

                array_push($res,
                           new Utilisateur($obj->login, $obj->mdp,
                                           $obj->email, $ensei, $admin)
                );
            }

            return $res;
        }
    }