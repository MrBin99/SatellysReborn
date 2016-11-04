<?php
    namespace WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse;

    use WS_SatellysReborn\BaseDonnees\DAO\DAO;
    use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use WS_SatellysReborn\Modeles\Population\Adresse\Ville;

    /**
     * DAO permettant de gérer les villes des adresses en base de données.
     * @package WS_SatellysReborn\BaseDonnees\DAO\Population\Adresse
     */
    class DAO_Ville extends DAO {

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Ville $obj l'objet à insérer dans la base de données.
         * @return Ville|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($obj) {
            // SQL.
            $sql = 'INSERT INTO ville
                    VALUES (:numinsee, :cp, :nom, :pays)';

            $res = $this->connexion->insert($sql, array(
                ':numinsee' => $obj->getNumInsee(),
                ':cp' => $obj->getCodePostal(),
                ':nom' => $obj->getNom(),
                ':pays' => $obj->getPays()->getId()
            ));

            // Insertion ok ?
            if ($res) {
                return new Ville($obj->getNumInsee(), $obj->getCodePostal(),
                                 $obj->getNom(), $obj->getPays());
            }

            // else
            return false;
        }

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Ville $obj l'objet à modifier dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la modification a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function update($obj) {
            // Pré-condition.
            if (is_null($obj->getNumInsee()) ||
                is_null($this->find($obj->getNumInsee()))
            ) {
                return false;
            }
            // else

            // SQL.
            $sql = 'UPDATE ville SET
                        code_postal = :cp,
                        nom = :nom,
                        id_pays = :pays
                    WHERE numinsee = :numinsee';

            return $this->connexion->update($sql, array(
                ':cp' => $obj->getCodePostal(),
                ':nom' => $obj->getNom(),
                ':pays' => $obj->getPays()->getId(),
                ':numinsee' => $obj->getNumInsee(),
            ));
        }

        /**
         * On ne supprime pas une ville.
         */
        public function delete($obj) {
            return true;
        }

        /**
         * Sélectionne l'élèment dont la clé primaire est passée en argument
         * s'il existe.
         * @param $cle string la clé primaire de l'objet à sélectionner.
         * @return Ville
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function find($cle) {
            // SQL.
            $sql = 'SELECT code_postal, nom, id_pays
                    FROM ville
                    WHERE numinsee = :numinsee';

            $resBD = $this->connexion->select($sql, array(
                ':numinsee' => $cle
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $pays = DAO_Factory::getDAO_Pays()
                               ->find($resBD[0]->id_pays);

            return new Ville($cle, $resBD[0]->code_postal, $resBD[0]->nom,
                             $pays);
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
            $sql = 'SELECT numinsee, code_postal, nom, id_pays
                    FROM ville';

            $resBD = $this->connexion->select($sql, array());

            // Vide ?
            if (empty($resBD)) {
                return null;
            }
            // else

            // Convertit en objet Promotion.
            $res = array();

            // Pour toutes les lignes.
            foreach ($resBD as $obj) {
                $pays = DAO_Factory::getDAO_Pays()
                                   ->find($resBD[0]->id_pays);

                array_push($res, new Ville(
                    $obj->numinsee, $obj->code_postal, $obj->nom, $pays
                ));
            }

            return $res;
        }

        /**
         * Sélectionne la ville dont le code postal et le nom est passé en
         * argument.
         * @param $cp string le code postal de la ville.
         * @param $nom string le nom de la ville.
         * @return Ville
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public function findCpNom($cp, $nom) {
            // SQL.
            $sql = 'SELECT numinsee, code_postal, nom, id_pays
                    FROM ville
                    WHERE lower(nom) LIKE lower(:nom)
                    AND code_postal = :cp';

            $resBD = $this->connexion->select($sql, array(
                ':nom' => '%' . $nom . '%',
                ':cp' => $cp,
            ));

            // Pas de résultats ?
            if (empty($resBD)) {
                return null;
            }

            // else
            $pays = DAO_Factory::getDAO_Pays()
                               ->find($resBD[0]->id_pays);

            return new Ville($resBD[0]->numinsee, $resBD[0]->code_postal,
                             $resBD[0]->nom,
                             $pays);
        }
    }