<?php
    use WS_SatellysReborn\Core\Application;

    /* ########## Constantes de l'application ########## */

    // Configuration de la connexion à la base de données.

    /* Le nom du dossier parent du site. */
    define('ROOT_DIR', 'WS_SatellysReborn');

    /* Le nom du site. */
    define('SITE_NAME', 'WS_SatellysReborn');

    /* Chemin vers le dossier de configuration de la base de données. */
    define('CONFIG_BD', 'configs/bd_conf.ini');

    // Chemin vers les contrôleurs.
    define('URL_CTRL', 'Controleurs\\');

    /**
     * Fonction d'auto-chargment des classes requises.<br>
     * Cette fonction est appelée directement par PHP avec l'instrcution "use".
     * @param $classe string le chemin de la classe à charger.
     */
    function __autoload($classe) {

        // Modifie le chemin à cause de l'architecture du projet.
        $classe = explode("\\", $classe);
        $classe[0] = "";

        // Importe la classe une seule fois.
        include_once implode('\\', $classe) . '.php';
    }

    // Lance le processus de routage.
    $app = new Application();
