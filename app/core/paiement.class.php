<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");
require_once ("api-class/authentification.php");

class paiementController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getPaiements() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query =  "SELECT *, date(date_paiement) as date_paiement  from t_paiement pa
                        LEFT JOIN t_magasin m on m.id_mag = pa.mag_paiement 
                        WHERE 1=1 AND pa.date_paiement >= DATE_SUB(now(), INTERVAL 14 DAY) ";

        

        $query.= " ORDER BY pa.date_paiement DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getEtatPaiements() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;
        // Trouver une condition pour afficher la liste
        $condition = $_SESSION['userMag'] > 0 ? " AND (mag_paiement=".$_SESSION['userMag']." OR login_paiement ='".$_SESSION['userLogin']."') ": "";
        $query =  "SELECT *, date(date_paiement) as date_paiement   from t_paiement pa
                        LEFT JOIN t_magasin m on m.id_mag = pa.mag_paiement 
                        WHERE 1=1 ";

        if (!empty($search['code_paiement'])) $query.=" AND pa.code_paiement=" . intval($search['code_paiement']);
        if (!empty($search['mag_paiement'])) $query.=" AND pa.mag_paiement='" .$search['mag_paiement']."'";
        if (!empty($search['used_paiement_code_user'])) $query.=" AND pa.used_paiement_code_user='" .$search['used_paiement_code_user']."'";

        if (!empty($search['date_deb']) && empty($search['date_fin'])) 
        $query.=" AND date(pa.date_paiement)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
        $query.=" AND date(pa.date_paiement) between '" . isoToMysqldate($search['date_deb']) . "' 
            AND '" . isoToMysqldate($search['date_fin']) . "'";
        $query.= $condition." ORDER BY pa.date_paiement DESC";
        //echo $query;
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }
    
    public function getPaiement() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->_request['code'];

        if (isset($code) && strlen($code)<5) {
            $response = array("status" => -1,
                "datas" => "",
                "message" => "Veuillez saisir un code valide, le code doit être supérieur ou égale à 5 chiffre");
            $this->response($this->json($response), 200);

        }
        $query =  "SELECT *  from t_paiement WHERE code like '%$code' ";

        $query.=  " AND date_paiement >= DATE_SUB(now(), INTERVAL 7 DAY)";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            if (count($result) > 1) {
                $response = array("status" => -1,
                    "message" => "Veuillez augmenter le nombre de code, plusieurs code retrouver",
                    "datas" => "");
                $this->response($this->json($response), 200);
            } else {
                if (!empty($result[0]['ref_facture_vente']) && $result[0]['ref_facture_vente'] !== '') {
                    $response = array("status" => -1,
                    "message" => "Ce code est déjà lié à une facture",
                    "datas" => "");
                    $this->response($this->json($response), 200);
                } else {
                    $response = array("status" => 0,
                        "datas" => $result,
                        "message" => "Code trouvé");
                    $this->response($this->json($response), 200);
                }
            }
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }
    

    
    public function savePaiement() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $paiement = $_POST;
        $montant = intval($paiement['montant']);
        $type_paiement = isset($paiement['type_paiement']) ? $this->esc($paiement['type_paiement']) : $this->esc('ORANGEMONEY');
        $code = isset($paiement['code']) ? $this->esc($paiement['code']) : '';
        $nom_expediteur = isset($paiement['nom_expediteur']) ? $this->esc($paiement['nom_expediteur']):'';
        $date_paiement = (!empty($paiement['date_paiement'])) ? isoToMysqldate($paiement['date_paiement']) : date("Y-m-d");
        $magasin = !empty($paiement['mag_paiement']) ? intval($paiement['mag_paiement']):'NULL';
        $used_user = isset($paiement['used_paiement_code_user']) ? $this->esc($paiement['used_paiement_code_user']) : '';
        $ref_facture_vente = isset($paiement['ref_facture_vente']) ? $this->esc($paiement['ref_facture_vente']) : '';
        $response = array();
        
        if (!empty($code) && !empty($montant) && $montant >= 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_paiement (
                     	type_paiement,
                     	code,
                        montant,
                        date_paiement,
                        nom_expediteur,
                        login_paiement,
                        used_paiement_code_user,
                        ref_facture_vente,
                        mag_paiement,
                        created_at,
                        updated_at) 
                     VALUES('$type_paiement',
                            '$code', 
                            $montant, 
                            '$date_paiement $heure_vnt',
                            '$nom_expediteur',
                            '".$_SESSION['userLogin']."',
                            '$used_user',
                            '$ref_facture_vente',
                            $magasin, now(), now())";
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Deposer avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $this->mysqli->rollback();
                $this->mysqli->autocommit(TRUE);
                $response = array("status" => 1,
                    "datas" => "$query",
                    "message" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } else {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Attention donnees incorrectes!");

            $this->response($this->json($response), 200);
        }
    }
    
       
    public function updatePaiement() {
        if ($this->get_request_method() != "POST") {
        $this->response('', 406);
        }

        $paiement = $_POST;

        $code = $paiement['code'];
        $mag_paiement = $fact['mag_paiement'];
        $ref_facture_vente = $fact['ref_facture_vente'];
        $used_paiement_code_user = $fact['used_paiement_code_user'];
        
        

        try {
            
            $query = "UPDATE t_paiement set mag_paiement='$ref_facture_vente',ref_facture_vente='$ref_facture_vente',
            used_paiement_code_user='$used_paiement_code_user', updated_at=now() ";
           
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $response = array("status" => 0,
            "datas" => $r,
            "message" => "Modifier avec succes!!!");
            $this->response($this->json($response), 200);

        } catch (Exception $exc) {
            $this->mysqli->rollback();
            $this->mysqli->autocommit(TRUE);
            $response = array("status" => 1,
            "datas" => "",
            "message" => $exc->getMessage());
            $this->response($this->json($response), 200);
        } 
    }





}

session_name('SessSngS');
session_start();
authentication();
if (isset($_SESSION['userId'])) {
    $app = new paiementController;
    $app->processApp();
}
?>