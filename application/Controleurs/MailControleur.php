<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Modeles\Utils\Utils;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant d'envoyer des mails.
     * @package SatellysReborn\Controleurs
     */
    class MailControleur extends Controleur {

        /**
         * Redirige vers l'accueil.
         */
        public function index() {
            self::redirect('/SatellysReborn/');
        }

        /**
         * Modifie la liste à envoyer par mail.
         */
        public function setListe() {
            if (isset($_POST)) {
                $_SESSION['liste'] = $_POST['liste'];
                var_dump($_SESSION['liste']);
            }
        }

        /**
         * Affiche la page de sélection des destinataires de la liste.
         */
        public function mail() {
            if (Utilisateur::estConnecte()) {
                if (!isset($_SESSION['liste'])) {
                    self::redirect('/SatellysReborn/mail/errListe/');
                }

                $this->vue =
                    new Vue($this, "Destinataires", "Choisir destinataires");
                $this->vue->render();

            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Envoie la liste courante aux destinataires.
         */
        public function envoyer() {
            if (Utilisateur::estConnecte()) {
                if (!isset($_SESSION['liste']) &&
                    !isset($_POST['destinataires'])
                ) {
                    self::redirect('/SatellysReborn/mail/errListe/');
                }

                // Envoie.
                foreach ($_POST['destinataires'] as $dest) {
                    if (!Utils::envoyerMail($dest, "Liste",
                                            $_SESSION['liste'])
                    ) {
                        self::redirect('/SatellysReborn/mail/errEnvoie/');
                    }
                }

                unset($_SESSION['liste']);
                $this->vue = new Vue($this, "EnvoieOk", "Liste envoyé");
                $this->vue->render();
            } else {
                self::redirect('/SatellysReborn/compte/errNonConnecte/');
            }
        }

        /**
         * Affiche l'erreur quand la liste courante à envoyer est vide.
         */
        public function errListe() {
            $this->vue = new Vue($this, "ErrListe", "Erreur liste inconnu");
            $this->vue->render();
        }

        /**
         * Affiche une erreur quand il est impossible d'envoyer un mail.
         */
        public function errEnvoie() {
            $this->vue = new Vue($this, "ErrEnvoie",
                                 "Problème durant l'envoie des mails");
            $this->vue->render();
        }
    }