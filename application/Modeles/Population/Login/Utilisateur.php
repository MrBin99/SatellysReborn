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
            $this->mdp = $mdp;
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
    }