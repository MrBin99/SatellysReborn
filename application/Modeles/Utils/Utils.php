<?php
    namespace SatellysReborn\Modeles\Utils;

    /**
     * Classe ne contenant que des fonctions utilitaires de classe.
     * @package SatellysReborn\Modeles\Utils
     */
    final class Utils {

        /**
         * Classe non instantiable.
         */
        private function __construct() {
        }

        /**
         * Enlève les accents d'un chaine de caractère.
         * @param $str string la chaine don on veut enlever les accents.
         * @return string la chaine sans les accents.
         */
        public static function enleverAccents($str) {
            return strtr(utf8_decode($str),
                         utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                         'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        }
    }