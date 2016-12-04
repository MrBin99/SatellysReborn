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

        /**
         * Reformate une date de la forme "aaaa-mm-jj" en "jj-mm-aaaa".
         * @param $date string la date à reformater.
         * @return string la date reformatée.
         */
        public static function reformatDate($date) {
            return implode('-', array_reverse(explode('-', $date)));
        }

        /**
         * Envoie un email à un destinaire (cet email peut être sous la forme
         * d'une page web statique HTML).<br>
         * Pour tout CSS allant avec, les inclure en lien absolu avec les
         * instructions HTML.
         * @param $destinataire string l'adresse mail du destinataire.
         * @param $sujet string le sujet du mail (champs 'objet').
         * @param $contenu string le contenu du mail.
         * @return bool si le mail a bien été envoyé ou non.
         */
        public static function envoyerMail($destinataire, $sujet, $contenu) {
            $headers = "From: no-reply@satellysreborn.fr\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            return mail($destinataire, SITE_NAME . " - " . $sujet, $contenu,
                        $headers);
        }

        /**
         * @return string retourne une chaine de caratère aléatoire.
         */
        public static function genererChaine() {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            return substr(str_shuffle($chars), 0, 10);
        }
    }