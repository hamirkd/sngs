<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class deffectueuxController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();  
    }

    public function getdefnv() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

       
        $query = "SELECT COUNT(*) as defnv 
            FROM t_deffectueux WHERE vu=0 limit 1";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
             $result = $r->fetch_assoc(); 
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
    
    
    
    
   public function insertStockDeff() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

         $appstock = $_POST;

       $mag = intval($appstock['mag_def']);
       $art = intval($appstock['art_def']);
       $qte = intval($appstock['qte_def']);
      $obj = $this->esc($appstock['obj_def']);
        $response = array();
        $query = "INSERT INTO  t_deffectueux (mag_def,art_def,qte_def,obj_def,login_def,user_def,user_code_def) 
            VALUES($mag,$art,$qte,'".$obj."','" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

        
        if (!empty($appstock)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

              
                    $query = "UPDATE t_stock SET qte_stk=qte_stk - $qte WHERE art_stk =$art AND mag_stk=$mag";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
               

                 

                $response = array("status" => 0,
                    "datas" => $appstock,
                    "message" => "Destockage  effectue avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "message" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204); 
    } 
    
    public function etatDeff() {
if ($this->get_request_method() != "POST") {
$this->response('', 406);
}

$search = $_POST;


if ($_SESSION['userMag'] != 0){
$query = "SELECT t.vu,t.id_def, time(t.date_def) as heure_def,t.user_def,t.user_code_def,t.qte_def,DATE(t.date_def) as date_def,m.code_mag,
                 t.mag_def,t.art_def,t.obj_def,a.nom_art,a.code_art,c.code_cat,c.nom_cat
                FROM t_deffectueux t
                 INNER JOIN t_magasin m  ON t.mag_def=m.id_mag
                 INNER JOIN t_article a ON t.art_def=a.id_art
                 INNER JOIN t_categorie_article c
                 ON a.cat_art=c.id_cat WHERE t.mag_def=" . intval($_SESSION['userMag']);


if (!empty($search['date_deb']) && empty($search['date_fin']))
$query.=" AND date(t.date_def)='" . isoToMysqldate($search['date_deb']) . "'";

if (!empty($search['date_fin']))
$query.=" AND date(t.date_def) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

if (!empty($search['magasin']))
$query.=" AND t.mag_def=" . intval($search['magasin']);


if (!empty($search['article']))
$query.=" AND t.art_def=" . intval($search['article']);

if (!empty($search['categorie']))
$query.=" AND c.id_cat=" . intval($search['categorie']);

$query .= " Order by t.id_def DESC,m.code_mag,c.nom_cat,a.nom_art";


}
else{
$query1 = "(SELECT t.vu,t.id_def, time(t.date_def) as heure_def,t.user_def,t.user_code_def,t.qte_def,DATE(t.date_def) as date_def,m.code_mag,
                 t.mag_def,t.art_def,t.obj_def,a.nom_art,a.code_art,c.code_cat,c.nom_cat
                FROM t_deffectueux t
                 INNER JOIN t_magasin m  ON t.mag_def=m.id_mag
                 INNER JOIN t_article a ON t.art_def=a.id_art
                 INNER JOIN t_categorie_article c
                 ON a.cat_art=c.id_cat WHERE 1=1";

if (!empty($search['date_deb']) && empty($search['date_fin']))
$query1.=" AND date(t.date_def)='" . isoToMysqldate($search['date_deb']) . "'";

if (!empty($search['date_fin']))
$query1.=" AND date(t.date_def) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

if (!empty($search['magasin']))
$query1.=" AND t.mag_def=" . intval($search['magasin']);


if (!empty($search['article']))
$query1.=" AND t.art_def=" . intval($search['article']);

if (!empty($search['categorie']))
$query1.=" AND c.id_cat=" . intval($search['categorie']);

$query1 .= " Order by t.id_def DESC,m.code_mag,c.nom_cat,a.nom_art limit 50)";

$query2 = "(SELECT t.vu,t.id_def, time(t.date_def) as heure_def,t.user_def,t.user_code_def,t.qte_def,DATE(t.date_def) as date_def,m.code_mag,
                 t.mag_def,t.art_def,t.obj_def,a.nom_art,a.code_art,c.code_cat,c.nom_cat
                FROM t_deffectueux t
                 INNER JOIN t_magasin m  ON t.mag_def=m.id_mag
                 INNER JOIN t_article a ON t.art_def=a.id_art
                 INNER JOIN t_categorie_article c
                 ON a.cat_art=c.id_cat WHERE 1=1 AND t.vu = 0";
 

$query2 .= " Order by t.id_def DESC,m.code_mag,c.nom_cat,a.nom_art)";

$query = $query1. " UNION ".$query2;
}

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




public function undoDeff() {
if ($this->get_request_method() != "POST") {
$this->response('', 406);
}

$fact = $_POST;

$id = intval($fact['id_def']);
try {

$query = "DELETE FROM t_deffectueux WHERE id_def=$id ";

$r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

$response = array("status" => 0,
 "datas" => $r,
 "message" => "Destockage Annuler avec success!!!");
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



public function vudef() {
if ($this->get_request_method() != "POST") {
$this->response('', 406);
}

$fact = $_POST;

$id = intval($fact['id_def']);
try {

$query = "UPDATE t_deffectueux set vu=1 WHERE id_def=$id ";

$r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

$response = array("status" => 0,
 "datas" => $r,
 "message" => "Destockage Marquer comme vu avec success!!!");
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



public function tvudef() {
if ($this->get_request_method() != "GET") {
$this->response('', 406);
}

 
try {

$query = "UPDATE t_deffectueux set vu=1 WHERE vu=0 ";

$r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

$response = array("status" => 0,
 "datas" => $r,
 "message" => "Tous les destockage Marquer comme vu avec success!!!");
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
if(isset($_SESSION['userId'])){
$app = new deffectueuxController;
$app->processApp();
}
?>