<?php
/**
 * Created by PhpStorm.
 * User: Julien
 * Date: 04/11/2016
 * Time: 10:22
 */

namespace WS_SatellysReborn\Controleurs;

/**
 * Controleur de la page d'accueil.
 * @package WS_SatellysReborn\Controleurs
 */
class Accueil extends Controleur {

    /**
     * Méthode appelée par défaut quand uniquement
     * le contrôleur est indiqué dans l'URL.
     */
    public function index()
    {
        include_once VUES.'Accueil/Accueil.phtml';
    }
}