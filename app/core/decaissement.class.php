<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class decaissementController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }
    
    public function getdepnv() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

       
        $query = "SELECT COUNT(*) as depnv 
            FROM t_depense WHERE vu=0 limit 1";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
             $result = $r->fetch_assoc(); 
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getEtatDepenses() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        if ($_SESSION['userMag'] > 0){
            $query = "SELECT date(dep.date_dep) as date_dep,dep.id_dep,dep.vu, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
            td.lib_type_dep
                           FROM 
                           t_depense dep
                           INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
                           WHERE user_dep in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ") ";
       
            if (!empty($search['user']))
            $query.=" AND dep.code_user_dep='" . $this->esc($search['user']) . "'";


        if (!empty($search['type_dep']))
            $query.=" AND dep.type_dep=" . intval($search['type_dep']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(dep.date_dep)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(dep.date_dep) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.=" Order By dep.date_dep DESC"; 
            
        } else{
            $query1 = "(SELECT date(dep.date_dep) as date_dep,dep.id_dep,dep.vu, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
            td.lib_type_dep
                           FROM 
                           t_depense dep
                           INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
                           WHERE 1=1 ";

        if (!empty($search['user']))
            $query1.=" AND dep.code_user_dep='" . $this->esc($search['user']) . "'";


        if (!empty($search['type_dep']))
            $query1.=" AND dep.type_dep=" . intval($search['type_dep']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query1.=" AND date(dep.date_dep)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query1.=" AND date(dep.date_dep) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query1.=" Order By dep.date_dep DESC)";
        
        //  $query2 = "(SELECT date(dep.date_dep) as date_dep,dep.id_dep,dep.vu, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
        //     td.lib_type_dep
        //                    FROM 
        //                    t_depense dep
        //                    INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
        //                    WHERE 1=1 AND dep.vu=0 ";
 

        // $query2.=" Order By dep.date_dep DESC)";
        
        $query = $query1;
        }

         
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getEtatProvisions() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT date(cais.date_cais) as date_cais,cais.id_cais, cais.mnt_cais,cais.detail_cais,cais.code_caissier_cais
                            FROM 
                           t_caisse cais
                            WHERE user_cais in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ") ";
        else
            $query = "SELECT date(cais.date_cais) as date_cais,cais.id_cais, cais.mnt_cais,cais.detail_cais,cais.code_caissier_cais
                             FROM 
                           t_caisse cais WHERE 1=1 ";

        if (!empty($search['user']))
            $query.=" AND cais.code_caissier_cais='" . $this->esc($search['user']) . "'";

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(cais.date_cais)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(cais.date_cais) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.=" Order By cais.date_cais DESC";



        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getEtatVersements() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT v.id_vrsmnt,DATE(v.date_vrsmnt) as date_vrsmnt, v.mnt_vrsmnt,v.obj_vrsmnt,v.code_caissier_vrsmnt,
            b.nom_bank
                           FROM 
                           t_versement v
                           INNER JOIN t_banque b ON v.bank_vrsmnt=b.id_bank
                           WHERE   v.caissier_vrsmnt in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")
                            ";
        else
            $query = "SELECT v.id_vrsmnt,DATE(v.date_vrsmnt) as date_vrsmnt, v.mnt_vrsmnt,v.obj_vrsmnt,v.code_caissier_vrsmnt,
            b.nom_bank
                           FROM 
                           t_versement v
                           INNER JOIN t_banque b ON v.bank_vrsmnt=b.id_bank
                           WHERE 1=1 ";


        if (!empty($search['user']))
            $query.=" AND v.code_caissier_vrsmnt='" . $this->esc($search['user']) . "'";


        if (!empty($search['bank_vrsmnt']))
            $query.=" AND v.bank_vrsmnt=" . intval($search['bank_vrsmnt']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(v.date_vrsmnt)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(v.date_vrsmnt) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.=" Order By v.date_vrsmnt DESC";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getDepenses() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT dep.id_dep, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
            td.lib_type_dep
                           FROM 
                           t_depense dep
                           INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
                           WHERE date(dep.date_dep)='" . date("Y-m-d") . "'
                               AND user_dep in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")
                           ORDER BY dep.date_dep DESC";
        else
            $query = "SELECT dep.id_dep, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
            td.lib_type_dep
                           FROM 
                           t_depense dep
                           INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
                           WHERE date(dep.date_dep)='" . date("Y-m-d") . "'
                           ORDER BY dep.date_dep DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getDepensesSl() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $sliceT = "";
        $pst = $_POST;
        $slice = $pst['sl'];

        if ($slice > 0)
            $sliceT = " AND dep.id_dep > $slice";

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT dep.id_dep, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
            td.lib_type_dep,time(dep.date_dep) as heure_dep
                           FROM 
                           t_depense dep
                           INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
                           WHERE date(dep.date_dep)='" . date("Y-m-d") . "'
                               AND user_dep in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ") 
                           $sliceT ORDER BY dep.date_dep DESC";
        else
            $query = "SELECT dep.id_dep, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
            td.lib_type_dep,time(dep.date_dep) as heure_dep
                           FROM 
                           t_depense dep
                           INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
                           WHERE date(dep.date_dep)='" . date("Y-m-d") . "'
                           $sliceT ORDER BY dep.date_dep DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['TYPE'] = "DEP";
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getProvisions() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT cais.id_cais, cais.mnt_cais,cais.detail_cais,cais.code_caissier_cais,date(cais.date_cais) as date_cais
                           FROM 
                           t_caisse cais
                            WHERE date(cais.date_cais)='" . date("Y-m-d") . "'
                               AND user_cais in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")
                           ORDER BY cais.date_cais DESC";
        else
            $query = "SELECT cais.id_cais, cais.mnt_cais,cais.detail_cais,cais.code_caissier_cais 
                           FROM 
                           t_caisse cais
                            WHERE date(cais.date_cais)='" . date("Y-m-d") . "'
                           ORDER BY cais.date_cais DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function saveDepense() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $depense = $_POST;
        $mnt_dep = intval($depense['mnt_dep']);
        $type_dep = intval($depense['type_dep']);
        $details_dep = !empty($depense['details_dep']) ? $this->esc($depense['details_dep']) : "Ras";
        $date_dep = (!empty($depense['date_dep'])) ? isoToMysqldate($depense['date_dep']) : date("Y-m-d");



        $response = array();

        if (!empty($type_dep) && !empty($mnt_dep) && $mnt_dep > 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_depense (
                     	type_dep,
                     mnt_dep,
                     date_dep,
                     user_dep,
                     login_dep,
                     code_user_dep,
                     details_dep) 
                     VALUES(" . $type_dep . ",
                          " . $mnt_dep . ", 
                              '$date_dep $heure_vnt',
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "',
                             '" . $details_dep . "')";


                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => " Depense  effectue avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $this->mysqli->rollback();
                $this->mysqli->autocommit(TRUE);
                $response = array("status" => 1,
                    "datas" => "",
                    "msg" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } else {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Attention donnees incorrectes!");

            $this->response($this->json($response), 200);
        }
    }

    public function saveProvision() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $provision = $_POST;
        $mnt_cais = intval($provision['mnt_cais']);
        $detail_cais = !empty($provision['details_cais']) ? $this->esc($provision['details_cais']) : "Ras";
        $date_cais = (!empty($provision['date_cais'])) ? isoToMysqldate($provision['date_cais']) : date("Y-m-d");



        $response = array();

        if (!empty($mnt_cais) && $mnt_cais > 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_caisse ( 
                     mnt_cais,
                     date_cais,
                     user_cais,
                     caissier_login_cais,
                     code_caissier_cais,
                     detail_cais) 
                     VALUES(" . $mnt_cais . ", 
                              '$date_cais $heure_vnt',
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "',
                             '" . $detail_cais . "')";


                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => " Provision  effectue avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $this->mysqli->rollback();
                $this->mysqli->autocommit(TRUE);
                $response = array("status" => 1,
                    "datas" => "",
                    "msg" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } else {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Attention donnees incorrectes!");

            $this->response($this->json($response), 200);
        }
    }

    public function getVersements() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT v.id_vrsmnt, v.mnt_vrsmnt,v.obj_vrsmnt,v.code_caissier_vrsmnt,
                     b.nom_bank
                           FROM 
                           t_versement v
                           INNER JOIN t_banque b ON v.bank_vrsmnt=b.id_bank
                           WHERE date(v.date_vrsmnt)='" . date("Y-m-d") . "'
                               AND v.caissier_vrsmnt in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")
                           ORDER BY v.date_vrsmnt DESC";
        else
            $query = "SELECT v.id_vrsmnt, v.mnt_vrsmnt,v.obj_vrsmnt,v.code_caissier_vrsmnt,
                       b.nom_bank
                           FROM 
                           t_versement v
                           INNER JOIN t_banque b ON v.bank_vrsmnt=b.id_bank
                           WHERE date(v.date_vrsmnt)='" . date("Y-m-d") . "' ORDER BY v.date_vrsmnt DESC";



        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function saveVersement() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $versement = $_POST;
        $mnt_vrsmnt = intval($versement['mnt_vrsmnt']);
        $bank_vrsmnt = intval($versement['bank_vrsmnt']);
        $obj_vrsmnt = !empty($versement['obj_vrsmnt']) ? $this->esc($versement['obj_vrsmnt']) : "Ras";
        $date_vrsmnt = (!empty($versement['date_vrsmnt'])) ? isoToMysqldate($versement['date_vrsmnt']) : date("Y-m-d");



        $response = array();

        if (!empty($bank_vrsmnt) && !empty($mnt_vrsmnt) && $mnt_vrsmnt > 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_versement (
                     	bank_vrsmnt,
                     mnt_vrsmnt,
                     date_vrsmnt,
                     caissier_vrsmnt,
                     caissier_login_vrsmnt,
                     code_caissier_vrsmnt,
                     obj_vrsmnt) 
                     VALUES(" . $bank_vrsmnt . ",
                          " . $mnt_vrsmnt . ", 
                              '$date_vrsmnt $heure_vnt',
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "',
                             '" . $obj_vrsmnt . "')";


                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => " Versement  effectue avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $this->mysqli->rollback();
                $this->mysqli->autocommit(TRUE);
                $response = array("status" => 1,
                    "datas" => "",
                    "msg" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } else {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Attention donnees incorrectes!");

            $this->response($this->json($response), 200);
        }
    }
    
    
    
       
public function vudep() {
if ($this->get_request_method() != "POST") {
$this->response('', 406);
}

$fact = $_POST;

$id = intval($fact['id_dep']);
try {

$query = "UPDATE t_depense set vu=1 WHERE id_dep=$id ";

$r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

$response = array("status" => 0,
 "datas" => $r,
 "msg" => "Depense Marquer comme vu avec success!!!");
$this->response($this->json($response), 200);

} catch (Exception $exc) {
$this->mysqli->rollback();
$this->mysqli->autocommit(TRUE);
$response = array("status" => 1,
 "datas" => "",
 "msg" => $exc->getMessage());

$this->response($this->json($response), 200);
} 
}



public function tvudep() {
if ($this->get_request_method() != "GET") {
$this->response('', 406);
}

 
try {

$query = "UPDATE t_depense set vu=1 WHERE vu=0 ";

$r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

$response = array("status" => 0,
 "datas" => $r,
 "msg" => "Toutes les depenses Marquer comme vu avec success!!!");
$this->response($this->json($response), 200);

} catch (Exception $exc) {
$this->mysqli->rollback();
$this->mysqli->autocommit(TRUE);
$response = array("status" => 1,
 "datas" => "",
 "msg" => $exc->getMessage());

$this->response($this->json($response), 200);
} 
}


}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new decaissementController;
    $app->processApp();
}
?>