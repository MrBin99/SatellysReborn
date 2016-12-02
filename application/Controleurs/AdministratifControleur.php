<?php
    namespace SatellysReborn\Controleurs;

    use SatellysReborn\BaseDonnees\DAO\DAO_Factory;
    use SatellysReborn\Modeles\Population\Administratif;
    use SatellysReborn\Modeles\Population\Adresse\Adresse;
    use SatellysReborn\Modeles\Population\Login\Utilisateur;
    use SatellysReborn\Modeles\Utils\Utils;
    use SatellysReborn\Vues\Vue;

    /**
     * Contrôleur permettant de gérer les administratifs
     * @package SatellysReborn\Controleurs
     */
    class AdministratifControleur extends Controleur {

        /**
         * Affiche la liste des administratifs.
         */
        public function index() {
            // Droits.
            if (Utilisateur::utilCourantEstSuperAdmin()) {
                // Tous les administratifs.
                $admins = DAO_Factory::getDAO_Administratif()->findAll();

                // Affichage.
                $this->vue =
                    new Vue($this, "Liste", "Liste des administratifs");
                $this->vue->render($admins);

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la page de creation d'un administratif.
         */
        public function creation() {
            // Droits.
            if (Utilisateur::utilCourantEstSuperAdmin()) {

                $this->vue =
                    new Vue($this, 'Creation', "Créer un administratif");
                $this->vue->render();

            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Créé un nouvel administratif dans la base de données.
         */
        public function creer() {
            // Droits.
            if (Utilisateur::utilCourantEstSuperAdmin()
            ) {
                if (isset($_POST['id']) && isset($_POST['nom']) &&
                    isset($_POST['prenom']) && isset($_POST['poste']) &&
                    isset($_POST['tel']) && isset($_POST['email']) &&
                    isset($_POST['adresse1']) && isset($_POST['adresse2']) &&
                    isset($_POST['adresse3']) && isset($_POST['ville'])
                ) {

                    // Insère.
                    $adresse =
                        DAO_Factory::getDAO_Adresse()
                                   ->insert(new Adresse(null,
                                                        $_POST['adresse1'],
                                                        $_POST['adresse2'],
                                                        $_POST['adresse3'],
                                                        DAO_Factory::getDAO_Ville()
                                                                   ->find($_POST['ville'])));

                    $admin = DAO_Factory::getDAO_Administratif()
                                        ->insert(new Administratif($_POST['id'],
                                                                   strtoupper($_POST['nom']),
                                                                   ucfirst(strtolower($_POST['prenom'])),
                                                                   $_POST['tel'],
                                                                   $_POST['poste'],
                                                                   $adresse));

                    if ($adresse != false && $admin != false &&
                        $this->creerUtilisateur($admin)
                    ) {
                        self::redirect('/SatellysReborn/administratif/details/' .
                                       $_POST['id']);
                    } else {
                        self::redirect('/SatellysReborn/administratif/errCreer/');
                    }
                } else {
                    self::redirect('/SatellysReborn/administratif/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Insère l'utilisateur correspondant à un administratif dans la base de
         * données.
         * @param Administratif $admin l'administratif correspond à
         *     l'utilisateur.
         * @return bool si l'insertion a bien eu lieu.
         */
        private function creerUtilisateur(Administratif $admin) {
            // Construit le login.
            $login =
                strtolower(Utils::enleverAccents($admin->getPrenom()) .
                           "." .
                           Utils::enleverAccents($admin->getNom()));

            // Insère.
            return DAO_Factory::getDAO_Utilisateur()
                              ->insert(new Utilisateur($login,
                                                       Utilisateur::crypterMdp($admin->getId()),
                                                       $_POST['email'],
                                                       null, $admin)) != false;
        }

        /**
         * Affiche les détails d'un administratif.
         * @param $id string l'identifiant de l'administratif.
         */
        public function details($id) {
            // Droits.
            if (Utilisateur::utilCourantEstSuperAdmin()) {
                if (isset($id)) {
                    // Récupère l'administratif.
                    $admin = DAO_Factory::getDAO_Administratif()->find($id);

                    if ($admin == false) {
                        self::redirect('/SatellysReborn/administratif/inconnu/');
                    }

                    // Récupère l'utilisateur pour l'email.
                    $util = DAO_Factory::getDAO_Utilisateur()
                                       ->findUtilisateurAdministratif($admin->getId());

                    $this->vue =
                        new Vue($this, 'Details', $admin->getNomComplet());
                    $this->vue->render(array($admin, $util));
                } else {
                    self::redirect('/SatellysReborn/administratif/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/administratif/errNonConnecte/');
            }
        }

        /**
         * Supprime un administratif de la base de données.
         * @param $id string l'identifiant de l'administratif.
         */
        public function supprimer($id) {
            // Droits.
            if (Utilisateur::utilCourantEstAdmin() ||
                Utilisateur::utilCourantEstSuperAdmin()
            ) {
                if (isset($id)) {

                    // L'administratif.
                    $admin = DAO_Factory::getDAO_Administratif()->find($id);

                    if ($admin == false) {
                        self::redirect('/SatellysReborn/administratif/inconnu/');
                    }

                    // L'utilisateur.
                    $util = DAO_Factory::getDAO_Utilisateur()
                                       ->findUtilisateurAdministratif($id);

                    if (DAO_Factory::getDAO_Utilisateur()->delete($util) &&
                        DAO_Factory::getDAO_Administratif()->delete($admin)
                    ) {
                        self::redirect('/SatellysReborn/administratif/');
                    } else {
                        self::redirect('/SatellysReborn/administratif/errSupprimer/');
                    }
                } else {
                    self::redirect('/SatellysReborn/administratif/inconnu/');
                }
            } else {
                self::redirect('/SatellysReborn/compte/errNonAdministratif/');
            }
        }

        /**
         * Affiche la page quand un administratif n'existe pas.
         */
        public function inconnu() {
            $this->vue = new Vue($this, "Inconnu", "Administratif Inconnu");
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand une erreur est survenu lors de la suppression
         * d'un administratif.
         */
        public function errSupprimer() {
            $this->vue =
                new Vue($this, 'ErrSupprimer', 'Erreur dans la suppression');
            $this->vue->render();
        }

        /**
         * Affiche l'erreur quand il est impossible de créer un nouvel
         * administratif.
         */
        public function errCreer() {
            $this->vue =
                new Vue($this, 'ErrCreer', 'Erreur dans la création');
            $this->vue->render();
        }
    }