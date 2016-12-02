<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\Modeles\Parser\ICS_Parser;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur pour les importations de fichier ICS.
     * @package SatellysReborn\Controleurs
     */
    class ImportControleur extends Controleur {

        /**
         * Méthode appelée par défaut quand uniquement
         * le contrôleur est indiqué dans l'URL.
         */
        public function index() {
            self::redirect('/SatellysReborn/import/ics/');
        }

        /**
         * Affiche la page d'importation du fichier ICS.
         */
        public function ics() {
            // Est bien administratif ou super-admin.
            if (Utilisateur::utilCourantEstSuperAdmin()
                || Utilisateur::utilCourantEstAdmin()
            ) {
                $this->vue =
                    new Vue($this, 'ImportICS', 'Import d\'un fichier ICS');
                $this->vue->render();
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Effectue le préchargement du fichier ICS.
         */
        public function preLoadICS() {
            if (isset($_FILES['ics'])
                && (Utilisateur::utilCourantEstSuperAdmin()
                    || Utilisateur::utilCourantEstAdmin())
            ) {

                // Lit le fichier.
                $f = fopen($_FILES['ics']['tmp_name'], 'r');
                $contenu = array();

                while (($ligne = fgets($f)) != null) {
                    array_push($contenu, trim($ligne));
                }

                // On le parse.
                $ics = new ICS_Parser($contenu);
                $ics->parse();

                // On le stocke pour la page d'insertion.
                $_SESSION['ics'] = $ics;

                // Affiche les logs.
                $this->vue = new Vue($this, 'LogsICS', 'Pré-chargement ICS');
                $this->vue->render($ics);

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Insère le fichier ICS dans la base de données.
         */
        public function insererICS() {
            if (isset($_SESSION['ics'])
                && (Utilisateur::utilCourantEstSuperAdmin()
                    || Utilisateur::utilCourantEstAdmin())
            ) {

                // Insertion !
                $insertions = $_SESSION['ics']->insererBD();
                $this->vue = new Vue($this, 'ResImportICS', 'Résultats');
                $this->vue->render($insertions);
                unset($_SESSION['ics']);

            } else {
                self::redirect('/SatellysReborn/import/icsInconnu/');
            }
        }

        /**
         * Affiche la page d'erreur quand le fichier ICS n'a pas été importé
         * correctement.
         */
        public function icsInconnu() {
            $this->vue = new Vue($this, 'ICSInconnu', 'Pas de fichier ICS');
            $this->vue->render();
        }
    }