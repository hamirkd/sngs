<?php

require_once ("api-class/model.php");
require_once ("api-class/backup.class.php");

class frontController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function login() {
        
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        if (!empty($this->_request['login']) and !empty($this->_request['password'])) {
            $login = $this->esc($this->_request['login']);
            $password = $this->esc($this->_request['password']);
            $query = "SELECT id_user,login_user,nom_user,prenom_user,sexe_user,code_user,profil_user,vente_credit,facture_vente_annulee,droit_facture_vente_annulee_today,droit_controle_prix_vente,droit_reglement_facture_credit,regl_credit,COALESCE(act_mag,3) as act_mag ,COALESCE(mag_user,0) as mag_user ,COALESCE(resa_mag,0) as resa_mag ,COALESCE(nom_mag,'TOUS') as nom_mag,COALESCE(code_mag,'MT') as code_mag FROM t_user LEFT JOIN t_magasin ON t_user.mag_user=t_magasin.id_mag WHERE login_user = '$login' AND pass_user = '" . md5($password) . "' AND (actif=1 OR actif IS NULL) LIMIT 1";
            
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $result = $r->fetch_assoc();

                $query = "SELECT * FROM t_configs WHERE 1=1 LIMIT 1";


                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $resultoptions = $r->fetch_assoc();
                foreach ($resultoptions as $key => $value) {
                    $resultoptions[$key] = (boolean) $value;
                    if ($key == "val_tva" || $key == "val_bic" || $key == "user_sms" || $key == "pass_sms" || $key == "mode_fact_uniq" || $key == "mode_clnt_uniq" || $key == "delai_bons"
                    )
                        $resultoptions[$key] = $value;
                }

                $result = array_merge($result, $resultoptions);

                /* user */
                $_SESSION['userLogin'] = $result['login_user'];
                $_SESSION['userId'] = $result['id_user'];
                $_SESSION['userCode'] = $result['code_user'];
                $_SESSION['userProfil'] = $result['profil_user'];
                $_SESSION['userMag'] = $result['mag_user'];
                $_SESSION['userMagAct'] = $result['act_mag'];
                $_SESSION['codeMag'] = $result['code_mag'];
                $_SESSION['nomMag'] = $result['nom_mag'];
                $_SESSION['reglCredit'] = $result['regl_credit'];
                $_SESSION['venteCredit'] = $result['vente_credit'];
                $_SESSION['factureVenteAnnulee'] = $result['facture_vente_annulee'];
                $_SESSION['droitFactureVenteAnnuleeToday'] = $result['droit_facture_vente_annulee_today'];
                $_SESSION['droitControlePrixVente'] = $result['droit_controle_prix_vente'];
                $_SESSION['droitReglementFactureCredit'] = $result['droit_reglement_facture_credit'];
                
                /* options */
                $_SESSION['tf'] = $result['tva_fact'];
                $_SESSION['bf'] = $result['bic_fact'];
                $_SESSION['pv'] = $result['prix_vari'];
                $_SESSION['pg'] = $result['prix_gros'];
                $_SESSION['cat'] = $result['categorie_art'];
                $_SESSION['apu'] = $result['aff_pu'];
				/* fusions des droits restrictan */
                if($result['resa_mag']==1)
                $result['restrict_annul'] = true;
                $_SESSION['resa'] = $result['restrict_annul'];
                $_SESSION['dynl'] = $result['dyn_load'];
                $_SESSION['tva'] = $result['val_tva'];
                $_SESSION['bic'] = $result['val_bic'];
                $_SESSION['grt'] = $result['grt'];
                $_SESSION['load_ipagld'] = $result['load_ipagld'];
                $_SESSION['pfl_str'] = $result['pfl_strict'];
                $_SESSION['prx_art'] = $result['prix_achat'];
                $_SESSION['cmd'] = $result['mdl_cmd'];
                $_SESSION['bon_att'] = $result['mdl_bon_att'];
                $_SESSION['usersms'] = $result['user_sms'];
                $_SESSION['passwordsms'] = $result['pass_sms'];
                
                /* delai + mode */
                $_SESSION['ret_pay'] = 15;
                $_SESSION['mode_fact_uniq'] = $result['mode_fact_uniq'];
                $_SESSION['mode_clnt_uniq'] = $result['mode_clnt_uniq'];
                $_SESSION['delai_bons'] = $result['delai_bons'];
                /**LE PROBLEME DE CHARGEMENT VIENT D ICI */
                $response = array("status" => 0,
                    "datas" => $result,
                    "message" => "rien");

                $query = "UPDATE t_user SET prev_cnx_user=last_cnx_user WHERE login_user = '$login' AND pass_user = '" . md5($password) . "'";
                $re = $this->mysqli->query($query);
                $query = "UPDATE t_user SET last_cnx_user=now() WHERE login_user = '$login' AND pass_user = '" . md5($password) . "'";
                $re = $this->mysqli->query($query);


                if ($_SESSION['userMag'] > 0) {
                    if ($_SESSION['delai_bons'] > 0) {

                        $query2 = "UPDATE t_approvisionnement SET actif=0 WHERE actif=1 AND user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ") AND DATEDIFF(date(now()),date(date_appro))>=" . $_SESSION['delai_bons'];
                        $re = $this->mysqli->query($query2);
                    } else {
                        $query2 = "UPDATE t_approvisionnement SET actif=0 WHERE actif=1 AND user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";
                        $re = $this->mysqli->query($query2);
                    }
                }

                if ($_SESSION['userMag'] > 0) {
                    if ($_SESSION['delai_bons'] > 0) {
                        $query2 = "UPDATE t_sortie SET actif=0 WHERE actif=1 AND user_sort in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ") AND DATEDIFF(date(now()),date(date_sort))>=" . $_SESSION['delai_bons'];
                        $re = $this->mysqli->query($query2);
                    } else {
                        $query2 = "UPDATE t_sortie SET actif=0 WHERE actif=1 AND user_sort in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";
                        $re = $this->mysqli->query($query2);
                    }
                }

                $this->response($this->json($response), 200);
            }
            $response = array("status" => 1,
                "datas" => "",
                "message" => "login ou mot de passe incorrect(s)");
                $this->response($this->json($response), 200);
                // $this->response($this->json($response), 404);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Veuillez remplir les champs convenablement !");
        $this->response($this->json($response), 200);
    }

    public function logout() {

        if ($_SESSION['userMag'] <= 0) {
            $t_t = strtotime(date("Y-m-d 19:10:00"));
            $t_n = strtotime(date("Y-m-d H:i:s"));
            if ($t_n > $t_t)
                ; //$bkp = new BackupMySQL();
        }
        $_SESSION = array();
        unset($_SESSION);
        session_destroy();

        $response = array("status" => 0,
            "datas" => "",
            "message" => "");
        $this->response($this->json($response), 200);
    }

}

session_name('SessSngS');
session_start();
$app = new frontController;
$app->processApp();

?>