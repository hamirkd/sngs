<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class demandeController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getEtatDemandes() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condition =  $_SESSION['userMag'] > 0 ? " AND mag_demandeur=".$_SESSION['userMag']:" ";
        $query =  "SELECT *,td.lib_type_dep as type_demande from t_demande dem
                        INNER JOIN t_type_depense td ON dem.type_demande=td.id_type_dep
                        WHERE 1=1 ";

if (!empty($search['type_demande'])) $query.=" AND dem.type_demande=" . intval($search['type_demande']);
if (!empty($search['demandeur'])) $query.=" AND dem.code_user_demandeur='" .$search['demandeur']."'";

        if (!empty($search['date_deb']) && empty($search['date_fin'])) 
        $query.=" AND date(dem.date_demande)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
        $query.=" AND date(dem.date_demande) between '" . isoToMysqldate($search['date_deb']) . "' 
            AND '" . isoToMysqldate($search['date_fin']) . "'";
        $query.= $condition." ORDER BY dem.date_demande DESC";

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

    public function getDemandesByRole() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condition =  "";//$_SESSION['userMag'] > 0 ? " AND mag_demandeur=".$_SESSION['userMag']:" ";
        $query = "SELECT *,td.lib_type_dep as type_demande from t_demande dem
                        INNER JOIN t_type_depense td ON dem.type_demande=td.id_type_dep
                        WHERE next_role='".$search['role']."' 
                        AND (etat=0 || etat is null) $condition ORDER BY dem.date_demande DESC";
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

    public function getDemandes() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condition = $_SESSION['userMag'] > 0 ? " AND mag_demandeur=".$_SESSION['userMag'] :" ";
            $query = "SELECT *,td.lib_type_dep as type_demande from t_demande dem
                           INNER JOIN t_type_depense td ON dem.type_demande=td.id_type_dep
                           WHERE date(dem.date_demande)='" . date("Y-m-d") . "'
                               $condition ORDER BY dem.date_demande DESC";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "message" => "recuperer avec success");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "La liste est vide");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }
    

    
    public function saveDemande() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $demande = $_POST;
        $montant = intval($demande['montant']);
        $type_demande = intval($demande['type_demande']);
        $details = $this->esc($demande['details']);
        $date_demande = (!empty($demande['date_demande'])) ? isoToMysqldate($demande['date_demande']) : date("Y-m-d");

        $response = array();

        if (!empty($type_demande) && !empty($details) && !empty($montant) && $montant > 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_demande (
                     	type_demande,
                        montant,
                        date_demande,
                        user_demandeur_id,
                        login_demandeur,
                        next_role,
                        code_user_demandeur,
                        mag_demandeur,
                        details) 
                     VALUES(" . $type_demande . ",
                          " . $montant . ", 
                              '$date_demande $heure_vnt',
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "','RESPMARKE',
                         '" . $_SESSION['userCode'] . "',
                         " . $_SESSION['userMag'] . ",
                             '" . $details . "')";
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Demande initiée avec success!");

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
    
       
    public function actionSurDemande() {
        if ($this->get_request_method() != "POST") {
        $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_dem']);
        $role = $fact['role'];
        $action = intval($fact['action']);
        $motif = $fact['motif'];
        // **** ROLE
        // RESPMARKE
        // RESPACHAT
        // CONTROGES
        // PDG
        // **** ACTION ETAT
        // 1 - AUTORISER
        // 0 - EN ATTENTE
        // 2 - REJETER

        try {
            // WorkFlow
            // $query = "UPDATE t_demande set etat=$action, motif='$motif',last_user='" . $_SESSION['userLogin'] . "',last_user_id=" . $_SESSION['userId'] . " WHERE id_dem=$id ";
            $queryAu = "INSERT INTO t_historique_workflow SET actu_action=$action, actu_role='$role', actu_user='" . $_SESSION['nom_prenom_user'] . "',actu_user_id=" . $_SESSION['userId'];
            if($role == 'RESPMARKE' && $action == 1){
                $query = "UPDATE t_demande set next_role='RESPACHAT', last_user='" . $_SESSION['nom_prenom_user'] . "',last_user_id=" . $_SESSION['userId'] . " WHERE id_dem=$id ";
            }else if($role == 'RESPACHAT' && $action == 1){
                $query = "UPDATE t_demande set next_role='CONTROGES', last_user='" . $_SESSION['nom_prenom_user'] . "',last_user_id=" . $_SESSION['userId'] . " WHERE id_dem=$id ";
            }else if($role == 'CONTROGES' && $action == 1){
                $query = "UPDATE t_demande set next_role='PDG', last_user='" . $_SESSION['nom_prenom_user'] . "',last_user_id=" . $_SESSION['userId'] . " WHERE id_dem=$id ";
            }else if($role == 'PDG' && $action == 1){
                $query = "UPDATE t_demande set  etat=$action, last_user='" . $_SESSION['nom_prenom_user'] . "',last_user_id=" . $_SESSION['userId'] . " WHERE id_dem=$id ";
            }
            else if($action == 2){
                $query = "UPDATE t_demande set etat=$action, motif='$motif',last_user='" . $_SESSION['nom_prenom_user'] . "',last_user_id=" . $_SESSION['userId'] . " WHERE id_dem=$id ";
            }
            // echo $query;
            // echo $queryAu;
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            $r = $this->mysqli->query($queryAu) or die($this->mysqli->error . __LINE__);

            $response = array("status" => 0,
            "datas" => $r,
            "message" => $action==1?"Autoriser avec success!!!":"Rejeter avec success!!!");
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
if (isset($_SESSION['userId'])) {
    $app = new demandeController;
    $app->processApp();
}
?>