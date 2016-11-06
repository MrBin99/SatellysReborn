<?php
/**
 * Created by PhpStorm.
 * User: Julien
 * Date: 06/11/2016
 * Time: 15:52
 */

namespace WS_SatellysReborn\Controleurs;


use WS_SatellysReborn\BaseDonnees\DAO\DAO_Factory;
use WS_SatellysReborn\Modeles\Population\Adresse\Adresse;
use WS_SatellysReborn\Modeles\Population\Login\Utilisateur;
use WS_SatellysReborn\Vues\Vue;

class Administrateur extends Controleur
{

    /**
     * Méthode appelée par défaut quand uniquement
     * le contrôleur est indiqué dans l'URL.
     */
    public function index()
    {
        /*if (Utilisateur::estConnecte() &&
            Utilisateur::getUtilisateur()->estAdmin()
        ) {*/
        $this->vue = new Vue($this, "Nouveau");
        $this->vue->render();
        /* } else {
             $this->vue = new Vue($this, 'ErreurAdmin');
             $this->vue->render();
         }*/
    }

    /**
     * Ajout d'un membre du personnel administratif
     */
    public function ajout()
    {
        /*  if (Utilisateur::estConnecte() &&
              Utilisateur::getUtilisateur()->estAdmin()
          ) {*/
        if (isset($_POST)) {

            $ville = DAO_Factory::getDAO_Ville()->find($_POST['ville']);
            $adr = new Adresse(null,$_POST['adresse'], "", "", $ville);

            $res = DAO_Factory::getDAO_Adresse()->insert($adr);
            $new = new Administratif($_POST['id'], $_POST['nom'],
                $_POST['prenom'], $_POST['tel'], $_POST['email'], $_POST['poste'], $res);

            $exist = false;
            $administratif = DAO_Factory::getDAO_Administratif()->findAll();
            if ($administratif) {
                foreach ($administratif as $obj) {
                    if ($obj->getId() == $new->getId()) {
                        $exist = true;
                    }
                }
            }
            if (!$exist) {
                $res2 = DAO_Factory::getDAO_Administratif()->insert($new);
                $this->ajoutUtilisateur($_POST['id'], $_POST['nom'], $_POST['prenom'], $_POST['email']);
            }

            $this->vue = new Vue($this, "Nouveau");
        } else {
            $this->vue = new Vue($this, "ErreurAjout");
        }
        /*} else {
            $this->vue = new Vue($this, 'ErreurAdmin');
        }*/
        $this->vue->render();
    }

    /**
     * Ajout d'un utilisateur
     * Fonction appelé uniquement dans la fonction ajout de Administrateur
     */
    private function ajoutUtilisateur($id, $nom, $prenom, $mail)
    {
        $log = strtolower($nom) + "." + strtolower($prenom);
        $mdp = $id;
        $util = new Utilisateur($log, $mdp, $mail, null, $id, null);
        $res = DAO_Factory::getDAO_Utilisateur()->insert($util);
    }
}