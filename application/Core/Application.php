<?php
    namespace WS_SatellysReborn\Core;

    use WS_SatellysReborn\Controleurs\Controleur;

    /**
     * Classe permettant d'effectuer le routage vers le contrôleur de la page
     * désirée via l'URL et d'appeler la méthode avec les arguments contenues
     * dans l'URL pour afficher la page.
     * @author Paul PAGES
     */
    final class Application {

        /** @var Controleur le contrôleur de la page demandée. */
        private $controleur;

        /** @var string la méthode du contrôleur à appeler. */
        private $methode;

        /** @var array un tableau contenant les arguments de la méthode. */
        private $args;

        /**
         * Donne le contrôle de la page au contrôleur passé en argument dans
         * l'URL et exécute la méthode du contrôleur avec les arguments passés
         * en arguments dans l'URL.<br> Si aucun contrôleur ne correspond au
         * contrôleur passé en argument, affiche une page d'erreur.
         */
        public function __construct() {

            // Par défaut
            $this->methode = 'index';
            $this->params = array();

            // Découpe l'URL
            $url = $this->decouperURL();

            // Le nom du contrôleur.
            $controleur = isset($url) ? ucfirst($url[0]) : 'Accueil';

            // Vérifie si le contrôleur existe
            if (file_exists(CONTROLEURS . $controleur . '.php')) {

                // Affectation du nom du contrôleur
                $this->controleur = $controleur;

                // Enlève le nom du contrôleur du tableau de l'URL
                unset($url[0]);

                // Importe le contrôleur
                require_once CONTROLEURS . $controleur . '.php';

                // Ajout du namespace
                $controleur = NAMESPACE_ROOT . '\Controleurs\\' . $controleur;

                // Créé le contrôleur
                $this->controleur = new $controleur();

                // Vérifie que le contrôleur a appeler est bien un contrôleur.
                if (!($this->controleur instanceof Controleur)) {
                    header('Location: ' . URL_SUB_FOLDER . 'erreur/erreur404/');

                    return;
                }

                /*
                 * Si il y a une méthode passée en argument dans l'URL et qu'elle
                 * existe dans le contrôleur
                 */
                if (isset($url[1]) &&
                    method_exists($this->controleur, $url[1])
                ) {

                    // On l'appelle
                    $this->methode = $url[1];

                    // Enlève le nom de la méthode du tableau de l'URL
                    unset($url[1]);
                }

                // Créé un objet représentant la méthode.
                $methode =
                    new \ReflectionMethod($this->controleur, $this->methode);

                // Vérifie que la méthode est appelable.
                if (!$methode->isPublic()) {
                    header('Location: ' . URL_SUB_FOLDER . 'erreur/erreur404/');

                    return;
                }

                // Si il reste des arguments pour la méthode dans le tableau de
                // l'URL
                $this->params = $url ? array_values($url) : [];

                // On appelle la méthode avec ses arguments
                call_user_func_array(
                    [
                        $this->controleur, $this->methode
                    ], $this->params);
            } else {
                // Si la page n'est pas connue
                header('Location: ' . URL_SUB_FOLDER . 'erreur/erreur404/');
            }
        }

        /**
         * Découpe l'URL en plusieurs chaînes de caractères par rapport au
         * caractère
         * '/' et les placent dans un tableau.
         * @return array le tableau contenant l'URL découpée, ou null si aucune
         *     URL est présente.
         */
        private function decouperURL() {
            if (isset($_GET['url'])) {

                // Vérifie que l'URL est valide.
                $url = filter_var(rtrim($_GET['url']), FILTER_SANITIZE_URL);

                /*
                 * On créé un tableau des éléments de l'URL en séparant chaque
                 * élément du tableau en fonction du '/' dans l'URL.
                 */

                return explode('/', $url);
            }

            // else
            return null;
        }
    }