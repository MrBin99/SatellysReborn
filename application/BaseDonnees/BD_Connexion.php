<?php
    namespace SatellysReborn\BaseDonnees;

    /**
     * Classe représentant la connexion à la base de données.
     * @package SatellysReborn\BaseDonnees
     */
    final class BD_Connexion {

        /**
         * @var array les options pour toutes les requêtes
         * à la base de donnée.
         */
        public static $OPTIONS_DB = array(
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT
        );

        /**
         * @var BD_Connexion la seule instance de connexion
         * à la base de données.
         */
        private static $instance;

        /** @var \PDO la connexion PDO à la base de données. */
        private $connexion;

        /**
         * Initialise une nouvelle connexion à la base de données.
         * @throws BD_Exception si impossible de se connecter à la base de
         *     données.
         */
        private function __construct() {
            // Lis le fichier de configuration.
            $configs = parse_ini_file(CONFIG_BD);

            try {
                // Créé la connexion.
                $this->connexion = new \PDO($configs['sgbd'] . ':host=' .
                                            $configs['host'] . ';dbname=' .
                                            $configs['nom_bd'] .
                                            ';charset=utf8',
                                            $configs['login'], $configs['mdp'],
                                            self::$OPTIONS_DB);
            } catch (\PDOException $e) {
                throw new BD_Exception("Impossible de se connecter " .
                                       "à la base de données");
            }
        }

        /**
         * Exécute une requête de type SELECT sur la base de données.
         * @param $requete string la requête à effectuer.
         * @param array $params les paramètres de la requête sous la forme
         *     (nom_param => valeur).
         * @return array les lignes retournées par la requête sous la forme
         *     d'un tableau d'objets.
         */
        public function select($requete, array $params) {
            // Prépare la requête.
            $reqObj = $this->connexion->prepare($requete);

            // Exécute la requête.
            $reqObj->execute($params);

            // Retoune les lignes.
            return $reqObj->fetchAll();
        }

        /**
         * Exécute une requête de type INSERT sur la base de données.
         * @param $requete string la requête à effectuer.
         * @param array $params les paramètres de la requête sous la forme
         *     (nom_param => valeur).
         * @return bool|string
         * <ul>
         *     <li>La clé primaire insérée.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function insert($requete, array $params) {
            return $this->execute($requete, $params) ?
                $this->connexion->lastInsertId() : false;
        }

        /**
         * Exécute une requête de type INSERT, UPDATE ou DELETE sur la base de
         * données.
         * @param $requete string la requête à effectuer.
         * @param array $params les paramètres de la requête sous la forme
         *     (nom_param => valeur).
         * @return bool
         * <ul>
         *     <li>True si la requête a été correctment effectuée.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        private function execute($requete, array $params) {
            // Prépare la requête.
            $reqObj = $this->connexion->prepare($requete);

            // Exécute la requête.
            return $reqObj->execute($params);
        }

        /**
         * Exécute une requête de type UPDATE sur la base de données.
         * @param $requete string la requête à effectuer.
         * @param array $params les paramètres de la requête sous la forme
         *     (nom_param => valeur).
         * @return bool
         * <ul>
         *     <li>True si la requête a été correctment effectuée.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function update($requete, array $params) {
            return $this->execute($requete, $params);
        }

        /**
         * Exécute une requête de type DELETE sur la base de données.
         * @param $requete string la requête à effectuer.
         * @param array $params les paramètres de la requête sous la forme
         *     (nom_param => valeur).
         * @return bool
         * <ul>
         *     <li>True si la requête a été correctment effectuée.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function delete($requete, array $params) {
            return $this->execute($requete, $params);
        }

        /**
         * @return BD_Connexion la seule instance de connexion à la base
         * de données (Pattern Singleton).
         * @throws BD_Exception si impossible de se connecter à la base de
         *     données.
         */
        public static function getInstance() {
            if (self::$instance == null) {
                self::$instance = new BD_Connexion();
            }

            return self::$instance;
        }
    }