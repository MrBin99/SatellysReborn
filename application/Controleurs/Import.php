<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\Modeles\Parser\ICS_Parser;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur pour les importations de fichier ICS et CSV.
     * @package SatellysReborn\Controleurs
     */
    class Import extends Controleur {

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
            if (Utilisateur::estConnecte()
                && (Utilisateur::getUtilisateur()->estAdmin() 
                    || Utilisateur::getUtilisateur()->estAdministratif())) {
                $this->vue = new Vue($this, 'ImportICS', 'Import d\'un fichier ICS');
                $this->vue->render();
            } else {
                $this->vue = new Vue($this, 'ErrNonAdminAdministratif', 'Accès refusé');
                $this->vue->render();
            }
        }

        /**
         * Effectue le préchargement du fichier ICS.
         */
        public function preLoadICS() {
            if (Utilisateur::estConnecte() && isset($_FILES['ics'])
                && (Utilisateur::getUtilisateur()->estAdmin()
                    || Utilisateur::getUtilisateur()->estAdministratif())) {

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
                if (!$ics->hasErreur()) {
                    $_SESSION['ics'] = $ics;
                }

                // Affiche les logs.
                $this->vue = new Vue($this, 'LogsICS', 'Pré-chargement ICS');
                $this->vue->render($ics);

            } else {
                self::redirect('/SatellysReborn/import/errNonAdminAdministratif/');
            }
        }

        /**
         * Insère le fichier ICS dans la base de données.
         */
        public function insererICS() {
            if (Utilisateur::estConnecte() && isset($_SESSION['ics'])
                && (Utilisateur::getUtilisateur()->estAdmin()
                    || Utilisateur::getUtilisateur()->estAdministratif())) {

                // Insertion !
                if (!$_SESSION['ics']->hasErreur() && $_SESSION['ics']->insererBD()) {
                    $this->vue = new Vue($this, 'InsertionICSOk', 'Insertion Succès');
                    $this->vue->render();
                    unset($_SESSION['ics']);
                } else {
                    $this->vue = new Vue($this, 'InsertionICSNOk', 'Erreur insertion ICS');
                    $this->vue->render();
                    unset($_SESSION['ics']);
                }

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

        /**
         * Affiche la page d'erreur quand un utilisateur essaie
         * d'accéder au page d'importation et qu'il n'a pas le droit.
         */
        public function errNonAdminAdministratif() {
            $this->vue = new Vue($this, 'ErrNonAdminAdministratif', 'Accès refusé');
            $this->vue->render();
        }
    }