<?php
    namespace WS_SatellysReborn\BaseDonnees;

    /**
     * Exception levée quand il est impossible quand une erreur au niveau
     * de la base de données est levée (duplication de clé primaire, impossible
     * de supprimer une ligne ...).
     * @package GestionAbs\BaseDonnees
     */
    class BD_Exception extends \Exception {
    }