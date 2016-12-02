<?php
    namespace SatellysReborn\BaseDonnees\DAO;

    use SatellysReborn\BaseDonnees\BD_Connexion;
    use SatellysReborn\Modeles\Modele;

    /**
     * Représente une entité de la base de données (table) pour y effectuer
     * des modifications ou sélection qui se répercuteront directement sur la
     * base de données.
     * @package SatellysReborn\BaseDonnees\DAO
     */
    abstract class DAO {

        /** @var BD_Connexion la connexion à la base de données. */
        protected $connexion;

        /** Initialise la connexion à la base de données. */
        public function __construct() {
            $this->connexion = BD_Connexion::getInstance();
        }

        /**
         * Insère l'objet passé en argument dans la base de données s'il
         * n'existe pas.
         * @param Modele $obj l'objet à insérer dans la base de données.
         * @return Modele|bool
         * <ul>
         *     <li>L'objet inséré, si l'insertion a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public abstract function insert($obj);

        /**
         * Modifie l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Modele $obj l'objet à modifier dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la modification a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public abstract function update($obj);

        /**
         * Supprime l'objet passé en argument dans la base de données s'il
         * existe.
         * @param Modele $obj l'objet à supprimer dans la base de données.
         * @return bool
         * <ul>
         *     <li>True si la suppression a eu lieu.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public abstract function delete($obj);

        /**
         * Sélectionne l'élèment dont un critère est passé en argument.
         * @param $critere string le critère de l'élément à sélectionner.
         * @return Modele
         * <ul>
         *     <li>L'objet retounée par la selection.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public abstract function find($critere);

        /**
         * Sélectionne tous les éléments de ce type.
         * @return array
         * <ul>
         *     <li>Un tableau d'objets contenant les objets sélectionnés.</li>
         *     <li>null si auncun objet n'a été trouvé.</li>
         * </ul>
         */
        public abstract function findAll();
    }