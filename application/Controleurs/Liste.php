<?php
/**
 * Created by PhpStorm.
 * User: Julien
 * Date: 05/11/2016
 * Time: 16:25
 */

namespace WS_SatellysReborn\Controleurs;

/**
 * Controleur de la page de la liste des absences d'un étudiant
 * @package WS_SatellysReborn\Controleurs
 */
class Liste extends Controleur {

    /**
     * Méthode appelée par défaut quand uniquement
     * le contrôleur est indiqué dans l'URL.
     */
    public function index()
    {
        include_once VUES.'Listes/ListeAbsenceEtudiant.phtml';
    }
}