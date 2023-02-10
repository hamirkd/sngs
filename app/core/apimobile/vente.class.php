<?php

require_once ("../api-class/model.php");
require_once ("../api-class/helpers.php");
require_once ("../api-class/authentification.php");

class venteController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }
   
    public function getVentes() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $condMag = "";
        $pst = $_POST;
        $limit_debut = 0;
        $taille = 100;
        if(isset($pst['limit_debut'])){
            $limit_debut = $pst['limit_debut'];
            if($limit_debut<0){
                $limit_debut = 0;
            }
        }
        if(isset($pst['taille'])){
            $taille = $pst['taille'];
            if($taille<0){
                $taille = 100;
            }
        }
        
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";
        $query = "SELECT f.*, (SELECT nom_clt FROM t_client WHERE id_clt=f.clnt_fact) as clt, 
                        (select nom_mag FROM t_magasin WHERE id_mag = f.mag_fact) as magasin
                         FROM t_facture_vente f
                         WHERE 1=1 $condMag ORDER BY date_fact DESC limit $limit_debut , $taille";
                     
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();    
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            

            $response = array("status" => 0,
                "datas" =>  $result);
                
            $this->response($this->json($response), 200);
        } else { 
            $response = array("status" => 0,
                "datas" => null,
                "message" => "NOT FOUND");
            $this->response($this->json($response), 404);
        }
        $this->response('', 500);
    }
    
   
    public function getVentesSearch() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $condMag = "";
        $pst = $_POST;
        $limit_debut = 0;
        $taille = 100;
        $val = "";
        if(isset($pst['limit_debut'])){
            $limit_debut = $pst['limit_debut'];
            if($limit_debut<0){
                $limit_debut = 0;
            }
        }
        if(isset($pst['taille'])){
            $taille = $pst['taille'];
            if($taille<0){
                $taille = 100;
            }
        }

        if(isset($pst['val'])){
            $val = $pst['val'];
        }
        
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";
        $query = "SELECT f.*, (SELECT nom_clt FROM t_client WHERE id_clt=f.clnt_fact) as clt, 
                        (select nom_mag FROM t_magasin WHERE id_mag = f.mag_fact) as magasin
                         FROM t_facture_vente f
                         WHERE 1=1 $condMag AND 
                         (code_fact LIKE %$code_fact% 
                         OR ref_fact_vnt LIKE %$ref_fact_vnt% 
                         OR date_fact LIKE %$date_fact% 
                         OR login_caissier_fact LIKE %$login_caissier_fact% 
                         OR magasin LIKE %$magasin%)
                         ORDER BY date_fact DESC limit $limit_debut , $taille";
                     
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();    
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            

            $response = array("status" => 0,
                "datas" =>  $result);
                
            $this->response($this->json($response), 200);
        } else { 
            $response = array("status" => 0,
                "datas" => null,
                "message" => "NOT FOUND");
            $this->response($this->json($response), 404);
        }
        $this->response('', 500);
    }

    public function getfactureDetail() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $condMag = "";
        $pst = $_POST;
        $id_fact = $_POST['id_fact'];
        
        $query = "SELECT v.*,(SELECT nom_art FROM t_article WHERE id_art=v.article_vnt) as article FROM t_vente v
                         WHERE facture_vnt = $id_fact";
                     
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();    
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" =>  $result);
                
            $this->response($this->json($response), 200);
        } else { 
            $response = array("status" => 0,
                "datas" => null,
                "message" => "NOT FOUND");
            $this->response($this->json($response), 404);
        }
        $this->response('', 500);
    }
}

session_name('SessSngS');
session_start();
authentication();
if (isset($_SESSION['userId'])) {
    $app = new venteController;
    $app->processApp();
}
?>