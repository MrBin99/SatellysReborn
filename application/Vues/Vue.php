<?php
    namespace SatellysReborn\Vues;

    use SatellysReborn\Controleurs\Controleur;

    /**
     * Représente une vue de l'application.
     * @package SatellysReborn\Vues
     */
    class Vue {

        /** @var Controleur le contrôleur correspondant à la vue. */
        private $controleur;

        /** @var string le fichier de la vue. */
        private $vue;

        /** @var string le titre de la page. */
        private $titre;

        /**
         * Créé une nouvelle vue.
         * @param Controleur $controleur le du contrôleur de la vue.
         * @param string $vue le fichier de la vue.
         * @param string $titre le titre de la page.
         */
        public function __construct(Controleur $controleur, $vue, $titre = '') {
            $this->controleur = $controleur;
            $this->vue = $vue;
            $this->titre =
                $titre == '' ? SITE_NAME : $titre . ' - ' . SITE_NAME;
        }

        /**
         * Affiche la vue sue la page Web.
         * @param mixed $obj une variable contenant les informations à afficher
         *     dans la vue.
         */
        public function render($obj = null) {
            // Récupère le nom du contrôleur.
            $path = explode('\\', get_class($this->controleur));
            $path = array_slice($path, count($path) - 1);
            $path[0] = str_replace('Controleur', '', $path[0]);

            // Vues existe ?
            if (file_exists(VUES . $path[0] . '/' . $this->vue .
                            '.phtml')) {

                // Ajoute l'en-tête et le menu.
                require_once COMMON . "Header.phtml";
                require_once COMMON . "Menu.phtml";

                // Ajoute la page.
                require_once VUES . $path[0] . '/' . $this->vue .
                             '.phtml';
            } else {
                Controleur::redirect('erreur/erreur42');
            }
        }

        /**
         * @return string le titre de la page.
         */
        public function getTitre() {
            return $this->titre;
        }
    }