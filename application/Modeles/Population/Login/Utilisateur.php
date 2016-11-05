<?php
    namespace WS_SatellysReborn\Modeles\Population\Login;

    use WS_SatellysReborn\Modeles\Modele;
    use WS_SatellysReborn\Modeles\Population\Administratif;
    use WS_SatellysReborn\Modeles\Population\Enseignant;
    use WS_SatellysReborn\Modeles\Population\Etudiant;

    /**
     * Représente un utilisateur utilisant l'application.
     * @package WS_SatellysReborn\Modeles\Population\Login
     */
    class Utilisateur extends Modele {

        /** @var string le login de l'utilisateur. */
        private $login;

        /** @var string le mot de passe de l'utilisateur. */
        private $mdp;

        /** @var string l'email de l'utilisateur. */
        private $email;

        /** @var Enseignant l'enseignant représenté par cet utilisateur. */
        private $enseignant;

        /** @var Administratif l'administratif représenté par cet utilisateur. */
        private $administratif;

        /** @var Etudiant l'étudiant représenté par cet utilisateur. */
        private $etudiant;

        /**
         * Créé un nouvel utilisateur.
         * @param string $login le login de l'utilisateur.
         * @param string $mdp le mot de passe de l'utilisateur.
         * @param string $email l'email de l'utilisateur.
         * @param Enseignant $enseignant l'enseignant représenté par cet
         *     utilisateur.
         * @param Administratif $administratif l'administratif représenté par
         *     cet utilisateur.
         * @param Etudiant $etudiant l'étudiant représenté par cet utilisateur.
         */
        public function __construct($login, $mdp, $email,
                                    Enseignant $enseignant = null,
                                    Administratif $administratif = null,
                                    Etudiant $etudiant = null) {
            $this->login = $login;
            $this->mdp = self::crypterMdp($mdp);
            $this->email = $email;
            $this->enseignant = $enseignant;
            $this->administratif = $administratif;
            $this->etudiant = $etudiant;
        }

        /**
         * Crypte le mot de passe passé en argument suivant l'algorithme MD5.
         * @param $mdp string le mot de passe à crypter.
         * @return string le mot de passe crypté.
         */
        public static function crypterMdp($mdp) {
            return hash('md5', $mdp);
        }

        /**
         * Détermine si un utilisateur est connecté à l'application.
         * @return bool
         * <ul>
         *     <li>True si l'utilisateur est connecté.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public static function estConnecte() {
            return isset($_SESSION['utilisateur']);
        }

        /**
         * Retourne l'utilisateur courant de l'application s'il est connecté.
         * @return Utilisateur l'utilisateur courant de l'application s'il est
         *     connecté, retourne null sinon.
         */
        public static function getUtilisateur() {
            return self::estConnecte() ? $_SESSION['utilisateur'] : null;
        }

        /**
         * Met à jour l'utilisateur courant de l'application.
         * @param Utilisateur $util l'utilisateur courant de l'application.
         */
        public static function setUtilisateur(Utilisateur $util) {
            $_SESSION['utilisateur'] = $util;
        }

        /**
         * @return string le login de l'utilisateur.
         */
        public function getLogin() {
            return $this->login;
        }

        /**
         * @return string le mot de passe de l'utilisateur.
         */
        public function getMdp() {
            return $this->mdp;
        }

        /**
         * @return string l'email de l'utilisateur.
         */
        public function getEmail() {
            return $this->email;
        }

        /**
         * @return Enseignant l'enseignant représenté par cet
         *     utilisateur.
         */
        public function getEnseignant() {
            return $this->enseignant;
        }

        /**
         * @return Administratif l'administratif représenté par
         *     cet utilisateur.
         */
        public function getAdministratif() {
            return $this->administratif;
        }

        /**
         * @return Etudiant l'étudiant représenté par cet utilisateur.
         */
        public function getEtudiant() {
            return $this->etudiant;
        }

        /**
         * @return bool
         * <ul>
         *     <li>True si l'utilisateur est un étudiant.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function estEtudiant() {
            return $this->etudiant != null;
        }

        /**
         * @return bool
         * <ul>
         *     <li>True si l'utilisateur est un administratif.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function estAdministratif() {
            return $this->administratif != null;
        }

        /**
         * @return bool
         * <ul>
         *     <li>True si l'utilisateur est un enseignant.</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function estEnseignant() {
            return $this->enseignant != null;
        }

        /**
         * @return bool
         * <ul>
         *     <li>True si l'utilisateur est un administrateur</li>
         *     <li>False sinon.</li>
         * </ul>
         */
        public function estAdmin() {
            return $this->etudiant == null
                && $this->enseignant == null
                && $this->administratif == null;
        }
    }