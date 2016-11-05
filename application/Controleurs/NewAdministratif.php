<?php
/**
 * Created by PhpStorm.
 * User: Julien
 * Date: 05/11/2016
 * Time: 15:51
 */

namespace WS_SatellysReborn\Controleurs;

/**
 * Controleur de la page de création d'administrateur
 * @package WS_SatellysReborn\Controleurs
 */
class NewAdministratif extends Controleur {

    /**
     * Méthode appelée par défaut quand uniquement
     * le contrôleur est indiqué dans l'URL.
     */
    public function index()
    {
        include_once VUES.'Creation/NewAdministratif.phtml';
    }
}