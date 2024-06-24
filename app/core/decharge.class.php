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
        $query =  "SELECT *,td.nom_art as article from t_decharge decharge
                        INNER JOIN t td ON decharge.art_decharge_art=td.id_art
                        WHERE 1=1 ";

        if (!empty($search['magasin'])) $query.=" AND decharge.mag_decharge=" . intval($search['magasin']);
        if (!empty($search['dechargeneur'])) $query.=" AND decharge.user_decharge_id='" .$search['dechargeneur']."'";

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
        $quantite = intval($decharge['quantite']);
        $montant = intval($decharge['montant']);
        $article = intval($decharge['article']);
        $motif = $this->esc($decharge['motif']);
        $magasin = !empty($decharge['magasin']) ? intval($decharge['magasin']):$_SESSION['userMag'];
        $date_decharge = (!empty($decharge['date_decharge'])) ? isoToMysqldate($decharge['date_decharge']) : date("Y-m-d");
        $response = array();
        if (!empty($article) && $magasin>0 &&!empty($quantite) &&$quantite>0 &&   !empty($montant) && $montant >= 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_decharge (
                     	art_decharge_art,
                        quantite,
                        montant,
                        date_decharge,
                        user_decharge_id,
                        login_decharge,
                        mag_decharge,
                        motif) 
                     VALUES(" . $article . ",
                          " . $quantite . ", 
                          " . $montant . ", 
                              '$date_decharge $heure_vnt',
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         " . $magasin . ",
                             '" . $motif . "')";
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