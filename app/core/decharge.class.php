<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class dechargeController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getEtatDecharges() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condition =  $_SESSION['userMag'] > 0 ? " AND mag_decharge=".$_SESSION['userMag']:" ";
        $query =  "SELECT * from t_decharge decharge WHERE 1=1 ";

        if (!empty($search['magasin'])) $query.=" AND decharge.mag_decharge=" . intval($search['magasin']);
        if (!empty($search['dechargeur'])) $query.=" AND decharge.user_decharge_id='" .$search['dechargeur']."'";
        if (!empty($search['client'])) $query.=" AND decharge.client_id='" .$search['client']."'";

        if (!empty($search['date_deb']) && empty($search['date_fin'])) 
        $query.=" AND date(decharge.date_decharge)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
        $query.=" AND date(decharge.date_decharge) between '" . isoToMysqldate($search['date_deb']) . "' 
            AND '" . isoToMysqldate($search['date_fin']) . "'";
        $query.= $condition." ORDER BY decharge.date_decharge DESC";
        // echo $query;

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

    
    public function saveDecharge() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $decharge = $_POST;
        $montant = intval($decharge['montant']);
        $motif = $this->esc($decharge['motif']);
        $magasin = !empty($decharge['magasin']) ? intval($decharge['magasin']):$_SESSION['userMag'];
        $date_decharge = (!empty($decharge['date_decharge'])) ? isoToMysqldate($decharge['date_decharge']) : date("Y-m-d");
        $user_decharge_id = $decharge['user_decharge_id'];
        $type_decharge = $decharge['type_decharge'];
        $client_id = $decharge['client_id'];
        $type_decharge = $decharge['type_decharge'];
        $nom_prenom_dechargeur = $decharge['nom_prenom_dechargeur'];
        $nom_prenom_client = $decharge['nom_prenom_client'];
        
        $response = array();
        if ($magasin>0 && !empty($montant) && $montant >= 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_decharge(mag_decharge, user_decharge_id,
                client_id, date_decharge, montant, login_decharge, motif_decharge,
                type_decharge, nom_prenom_dechargeur, nom_prenom_client) 
                    VALUES($magasin, $user_decharge_id, $client_id , '$date_decharge $heure_vnt',
                    $montant,'" . $_SESSION['userLogin'] . "','" . $motif . "','" . $type_decharge . "',
                    '$nom_prenom_dechargeur','$nom_prenom_client')";
                    // echo $query;
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Déchargé avec success!");

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





}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new dechargeController;
    $app->processApp();
}
?>