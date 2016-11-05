<?php
    use WS_SatellysReborn\Core\Application;

    /* ########## Constantes de l'application ########## */

    // Définitions des constantes pour l'ensemble des pages.
    define('URL_PUBLIC_FOLDER', 'public');
    define('URL_PROTOCOL', 'http://');
    define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
    define('URL_SUB_FOLDER',
           str_replace(URL_PUBLIC_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
    define('URL', URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER . '/');

    // Les différents modules du site.
    define('CONTROLEURS', '../application/Controleurs/');
    define('VUES', '../application/Vues/');
    define('COMMON', '../application/Vues/_Common/');

    /* Le nom du dossier parent du site. */
    define('ROOT_DIR', 'WS_SatellysReborn');

    /* Le "root" namespace. */
    define('NAMESPACE_ROOT', 'WS_SatellysReborn');

    /* Le nom du site. */
    define('SITE_NAME', 'SatellysReborn');

    /* Chemin vers le dossier de configuration de la base de données. */
    define('CONFIG_BD', '../application/conf/bd_conf.ini');

    // Les fichiers CSS.
    define('CSS', URL . 'css/');

    // Les fichiers JS.
    define('JS', URL . 'js/');

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
