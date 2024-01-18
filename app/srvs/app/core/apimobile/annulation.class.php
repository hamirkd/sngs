<?php

require_once ("../api-class/model.php");
require_once ("../api-class/helpers.php");
require_once ("../api-class/audit_log.class.php");
require_once ("../api-class/authentification.php");

class annulationController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function etatFacture() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        if (!empty($search['tx']) && $search['tx'] != "") {

            $query = "SELECT f.id_fact,date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
            INNER JOIN t_user u ON f.caissier_fact=u.id_user
            WHERE 1=1";
        } else {
            $query = "SELECT f.id_fact,date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
            WHERE 1=1";
            if ($_SESSION['userMag'] > 0)
                $query = "SELECT f.id_fact,date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
            INNER JOIN t_user u ON f.caissier_fact=u.id_user
            WHERE id_mag=" . intval($_SESSION['userMag']);
        }

        if (!empty($search['magasin']))
            $query.=" AND m.id_mag=" . intval($search['magasin']);

        if (!empty($search['user']))
            $query.=" AND f.caissier_fact='" . $this->esc($search['user']) . "'";

        if (!empty($search['client']))
            $query.=" AND c.id_clt=" . intval($search['client']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        if (isset($search['bc']) && $search['bc'] != "" && $search['bc'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['bc']);

        if (isset($search['bc']) && $search['bc'] != "" && $search['bc'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY Date_vnt DESC,id_fact DESC  ";



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

    public function queryEtatFacture() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $qc = "";
        $q = $_GET['q'];
        if (strtolower($q) == "annulee")
            $qc = " OR f.sup_fact=1";

        if (strtolower($q) == "remise")
            $qc = " OR f.remise_vnt_fact>0";

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
            WHERE f.code_fact LIKE '%$q%' OR c.nom_clt LIKE '%$q%' OR f.date_fact LIKE '%$q%' $qc ";
        if ($_SESSION['userMag'] > 0)
            $query = "SELECT f.id_fact,date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
            INNER JOIN t_user u ON f.caissier_fact=u.id_user
            WHERE f.code_fact LIKE '%$q%' OR c.nom_clt LIKE '%$q%' OR f.date_fact LIKE '%$q%' $qc 
                AND id_mag=" . intval($_SESSION['userMag']);


        $query.=" ORDER BY f.date_fact DESC  ";


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

    public function getFactures() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,
            f.date_fact,f.date_sup_fact,f.code_fact,bl_fact_crdt,f.bl_crdt_regle,
            f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
            f.bl_fact_crdt,f.bl_fact_grt,f.date_reg_fact,f.date_enr,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_fact_crdt=0 AND f.date_fact >= DATE_SUB(now(), INTERVAL 1 MONTH) $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFacturesGrtEnc() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,f.date_sup_fact,f.code_fact,bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
            f.bl_fact_crdt,f.date_enr,f.bl_fact_grt,c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_fact_crdt=1 AND f.bl_encaiss_grt=1 AND f.date_fact >= DATE_SUB(now(), INTERVAL 1 MONTH) $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFacturesFa() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,
            f.date_sup_fact,f.code_fact,bl_fact_crdt,
            f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.date_enr,
            f.remise_vnt_fact,f.bl_bic,f.bl_tva,
            f.bl_fact_crdt,f.bl_fact_grt,f.date_reg_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.date_fact >= DATE_SUB(now(), INTERVAL 14 DAY) AND f.crdt_fact>0 $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFacturesGrt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,f.date_sup_fact,f.code_fact,bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
            f.bl_fact_crdt,f.date_enr,f.bl_fact_grt,c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_fact_grt=1 AND f.bl_encaiss_grt=0 AND f.crdt_fact>0 AND f.date_fact >= DATE_SUB(now(), INTERVAL 1 MONTH) $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFacturesCrdt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,
            f.date_sup_fact,f.code_fact,bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,
            f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
            f.bl_fact_crdt,f.date_enr,f.bl_fact_grt,f.date_reg_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_fact_grt=0 AND f.bl_fact_crdt=1 AND f.crdt_fact>0 AND f.date_fact >= DATE_SUB(now(), INTERVAL 1 MONTH) $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFacturesCrdtRest($magasin) {

        $condmag = "";
        if ($magasin > 0)
            $condmag = " AND m.id_mag=" . intval($magasin);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,f.date_sup_fact,f.code_fact,bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
            f.bl_fact_crdt,f.bl_fact_grt,f.date_enr,c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_fact_grt=0 AND f.bl_fact_crdt=1 AND f.crdt_fact>0 AND f.date_fact >= DATE_SUB(now(), INTERVAL 1 MONTH) $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            return $response;
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "");
            return $response;
        }
    }
    
    

    public function getFacturesPro() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
        /* $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']); */
            $condmag = " ";

        $query = "SELECT f.id_pro,f.code_pro,bl_pro_crdt,f.bl_crdt_regle,f.crdt_pro,f.som_verse_crdt,f.remise_items_pro,f.bl_bic,f.bl_tva,
            f.bl_pro_crdt,f.bl_pro_grt,f.date_enr,c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_pro,f.caissier_pro,u.mag_user
                           FROM 
                           t_proforma f
                            INNER JOIN t_user u ON f.caissier_pro=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_pro=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_pro=m.id_mag 
                           WHERE f.date_pro >= DATE_SUB(now(), INTERVAL 1 MONTH) $condmag
                           ORDER BY f.id_pro DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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
    
    
    public function getFacturesProBycode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
        /* $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']); */
            $condmag = " ";

        $query = "SELECT f.id_pro,f.code_pro,bl_pro_crdt,f.bl_crdt_regle,f.crdt_pro,f.som_verse_crdt,f.remise_items_pro,f.bl_bic,f.bl_tva,
            f.bl_pro_crdt,f.date_enr,f.bl_pro_grt,c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_pro,f.caissier_pro,u.mag_user
                           FROM 
                           t_proforma f
                            INNER JOIN t_user u ON f.caissier_pro=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_pro=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_pro=m.id_mag 
                           WHERE f.date_pro >= DATE_SUB(now(), INTERVAL 1 MONTH) $condmag
                           ORDER BY f.id_pro DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getAllFactures() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.id_fact,f.code_fact,bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
            f.bl_fact_crdt,f.bl_fact_grt,f.date_reg_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,
            COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,
            f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE date(f.date_fact)=date(now()) AND bl_caiss=0 $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function gettvaFactures() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        /* if ($_SESSION['userMag'] > 0)
          $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']); */

        $query = "SELECT f.id_fact,f.code_fact,f.date_fact,f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.bl_fact_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
            f.bl_fact_grt,c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_tva=1 $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function undoFacture() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $motif_fact = $fact['motif'];
        $id_fact = intval($fact['id_fact']);


        $r = $this->fctUndoRegClntByFact($id_fact);


        $r = $this->fctSupVntByFact($id_fact);


        $r = $this->fctSupFactVnt($id_fact);

        $r = $this->fctDefMotifSupFactVnt($id_fact, $motif_fact);

        $response = array("status" => 0,
            "datas" => $r,
            "message" => "Annulation Facture effectuee avec success!");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }

    public function undoVnt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;
        $r = $this->fctUndoVnt($fact);
        $response = array("status" => 0,
            "datas" => $r,
            "message" => "");
        $this->response($this->json($response), 200);
    }

    public function undoProVnt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;
        $r = $this->fctUndoProVnt($fact);
        $response = array("status" => 0,
            "datas" => $r,
            "message" => "");
        $this->response($this->json($response), 200);
    }

    public function fctUndoVnt($fact) {
        $fact = $fact;

        $qte_vnt_p = doubleval($fact['qte_vnt']);
        $mnt_theo_vnt_p = doubleval($fact['mnt_theo_vnt']);
        $id_vnt = doubleval($fact['id_vnt']);
        $tva = doubleval($fact['bl_tva']);
        $bic = doubleval($fact['bl_bic']);
        $id_fact = doubleval($fact['id_fact']);

        try {
            $this->mysqli->autocommit(FALSE);


            $kry = "SELECT f.bl_fact_crdt,f.code_fact,f.mag_fact,
            v.clnt_vnt,a.nom_art,a.id_art,
            v.qte_vnt,v.mnt_theo_vnt,v.date_vnt 
                FROM t_vente v
                inner join t_facture_vente f on v.facture_vnt=f.id_fact
                inner join t_article a on v.article_vnt=a.id_art
                   WHERE v.id_vnt =$id_vnt LIMIT 1";

            $rez = $this->mysqli->query($kry);

            $rezult = $rez->fetch_assoc();
            $bl_fact_crdt = intval($rezult['bl_fact_crdt']);
            $client = $rezult['clnt_vnt'];
            $quant = $rezult['qte_vnt'];
            $article = $rezult['id_art'];
            $magasin = $rezult['mag_fact'];
            $dat = $rezult['date_vnt'];
            $anc = $rezult['mnt_theo_vnt'];
            $nouv = 0;
            $comm = "Facture : " . $rezult['code_fact'] . " Client : " . $rezult['clnt_vnt'] . " Article : " . $rezult['nom_art'] . "  Qte : " . $rezult['qte_vnt'] . " Montant : " . $rezult['mnt_theo_vnt'];
            $log = $_SESSION['userLogin'];
            $cod = $_SESSION['userCode'];
            $ide = $_SESSION['userId'];

            if ($rezult['qte_vnt'] == $qte_vnt_p) {/* suppression de tout */

                $query = "DELETE FROM t_vente WHERE id_vnt=$id_vnt ";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $aud = new aditlogController;
                $aud->auditlog("SUPPRESSION", "Vente", "ligne vente", $anc, $nouv, $dat, $ide, $log, $cod, $comm
                );
            } else { /* modification */


                $qu = "UPDATE t_stock SET qte_stk = (qte_stk + $qte_vnt_p) WHERE art_stk = $article AND mag_stk = $magasin";
                $r = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);


                $query = "Update t_vente set qte_vnt=qte_vnt-$qte_vnt_p,mnt_theo_vnt=mnt_theo_vnt-$mnt_theo_vnt_p WHERE id_vnt=$id_vnt ";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


                $query = "select mnt_theo_fact FROM t_facture_vente 
                  WHERE t_facture_vente.id_fact=$id_fact";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($r->num_rows > 0) {
                    $row = $r->fetch_assoc();
                    $crdt = doubleval($row['mnt_theo_fact'] - $mnt_theo_vnt_p);
                    $qu = "Update t_facture_vente 
                  set mnt_theo_fact=$crdt  WHERE id_fact=$id_fact";
                    $r = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
                }


                $query = "select f.bl_tva,f.bl_bic,c.exo_tva_clt from t_client c inner join t_facture_vente f ON f.clnt_fact=c.id_clt WHERE f.id_fact =$id_fact";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


                if ($r->num_rows > 0) {
                    $result = $r->fetch_assoc();
                    $exoclt = $result['exo_tva_clt'];
                    $bltva = $result['bl_tva'];
                    $blbic = $result['bl_bic'];
                } else {
                    $query = "select f.bl_tva,f.bl_bic from  t_facture_vente f  WHERE f.id_fact =$id_fact";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                    $result = $r->fetch_assoc();
                    $exoclt = 0;
                    $bltva = $result['bl_tva'];
                    $blbic = $result['bl_bic'];
                }




                $query = "UPDATE t_facture_vente SET tva_fact=((mnt_theo_fact - remise_vnt_fact) * $bltva * " . $_SESSION['tva'] . ") WHERE id_fact =$id_fact";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $query = "UPDATE t_facture_vente SET bic_fact=((((mnt_theo_fact - remise_vnt_fact)+((mnt_theo_fact - remise_vnt_fact) * $bltva * " . $_SESSION['tva'] . "))) * $blbic * " . $_SESSION['bic'] . ")
                           WHERE id_fact =$id_fact";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($exoclt > 0) {
                    $query = "UPDATE t_facture_vente SET crdt_fact=(mnt_theo_fact-remise_vnt_fact)  WHERE id_fact =$id_fact";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                } else {
                    $query = "UPDATE t_facture_vente SET crdt_fact=(mnt_theo_fact-remise_vnt_fact+tva_fact+bic_fact)  WHERE id_fact =$id_fact";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }
                /* on met a jour si paye ou pas */
                $query = "select crdt_fact,som_verse_crdt,remise_vnt_fact FROM t_facture_vente 
                WHERE t_facture_vente.id_fact=$id_fact";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($r->num_rows > 0) {
                    $row = $r->fetch_assoc();
                    if ($row['som_verse_crdt'] >= ($row['crdt_fact'])) {
                        $qu = "Update t_facture_vente 
                  set bl_crdt_regle=1  WHERE id_fact=$id_fact";
                        $r = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
                    }
                }
            }

            $this->mysqli->commit();
            $this->mysqli->autocommit(TRUE);
            return $r;
        } catch (Exception $exc) {
            $this->mysqli->rollback();
            $this->mysqli->autocommit(TRUE);
            $this->response($exc, 204);
        }
    }

    public function fctSupVnt($fact) {
        $fact = $fact;

        $id_vnt = doubleval($fact['id_vnt']);

        try {
            $this->mysqli->autocommit(FALSE);


            $kry = "SELECT   f.mag_fact,  a.id_art,  v.qte_vnt 
                FROM t_vente v
                inner join t_facture_vente f on v.facture_vnt=f.id_fact
                inner join t_article a on v.article_vnt=a.id_art
                   WHERE v.id_vnt =$id_vnt LIMIT 1";

            $rez = $this->mysqli->query($kry);

            $rezult = $rez->fetch_assoc();
            $article = $rezult['id_art'];
            $magasin = $rezult['mag_fact'];
            $quant = $rezult['qte_vnt'];

            $qu = "UPDATE t_stock SET qte_stk = (qte_stk + $quant) WHERE art_stk = $article AND mag_stk = $magasin";
            $r = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);

            $qu = "UPDATE t_vente SET sup_vnt=1,date_sup_vnt=now() WHERE id_vnt = $id_vnt";
            $r = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);

            $this->mysqli->commit();
            $this->mysqli->autocommit(TRUE);
            return $r;
        } catch (Exception $exc) {
            $this->mysqli->rollback();
            $this->mysqli->autocommit(TRUE);
            $this->response($exc, 204);
        }
    }

    public function fctUndoProVnt($fact) {
        $fact = $fact;

        $qte_items_p = doubleval($fact['qte_items']);
        $mnt_theo_items_p = doubleval($fact['mnt_theo_items']);
        $id_items = doubleval($fact['id_items']);
        $tva = doubleval($fact['bl_tva']);
        $bic = doubleval($fact['bl_bic']);
        $id_pro = doubleval($fact['id_pro']);

        try {
            $this->mysqli->autocommit(FALSE);


            $kry = "SELECT f.bl_pro_crdt,f.code_pro,f.mag_pro,
            v.clnt_items,a.nom_art,a.id_art,
            v.qte_items,v.mnt_theo_items,v.date_items 
                FROM t_proforma_items v
                inner join t_proforma f on v.facture_items=f.id_pro
                inner join t_article a on v.article_items=a.id_art
                   WHERE v.id_items =$id_items LIMIT 1";

            $rez = $this->mysqli->query($kry);

            $rezult = $rez->fetch_assoc();
            $bl_pro_crdt = intval($rezult['bl_pro_crdt']);
            $client = $rezult['clnt_items'];
            $quant = $rezult['qte_items'];
            $article = $rezult['id_art'];
            $magasin = $rezult['mag_pro'];
            $dat = $rezult['date_items'];
            $anc = $rezult['mnt_theo_items'];
            $nouv = 0;
            $comm = "Facture : " . $rezult['code_pro'] . " Client : " . $rezult['clnt_items'] . " Article : " . $rezult['nom_art'] . "  Qte : " . $rezult['qte_items'] . " Montant : " . $rezult['mnt_theo_items'];
            $log = $_SESSION['userLogin'];
            $cod = $_SESSION['userCode'];
            $ide = $_SESSION['userId'];

            if ($rezult['qte_items'] == $qte_items_p) {/* suppression de tout */

                $query = "DELETE FROM t_proforma_items WHERE id_items=$id_items ";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $aud = new aditlogController;
                $aud->auditlog("SUPPRESSION", "Vente", "ligne vente", $anc, $nouv, $dat, $ide, $log, $cod, $comm
                );
            } else { /* modification */


                $query = "Update t_proforma_items set qte_items=qte_items-$qte_items_p,mnt_theo_items=mnt_theo_items-$mnt_theo_items_p WHERE id_items=$id_items ";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


                $query = "select mnt_theo_pro FROM t_proforma 
                  WHERE t_proforma.id_pro=$id_pro";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($r->num_rows > 0) {
                    $row = $r->fetch_assoc();
                    $crdt = doubleval($row['mnt_theo_pro'] - $mnt_theo_items_p);
                    $qu = "Update t_proforma 
                  set mnt_theo_pro=$crdt  WHERE id_pro=$id_pro";
                    $r = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
                }


                $query = "select f.bl_tva,f.bl_bic,c.exo_tva_clt from t_client c inner join t_proforma f ON f.clnt_pro=c.id_clt WHERE f.id_pro =$id_pro";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $result = $r->fetch_assoc();

                $exoclt = $result['exo_tva_clt'];
                $bltva = $result['bl_tva'];
                $blbic = $result['bl_bic'];

                $query = "UPDATE t_proforma SET tva_pro=((mnt_theo_pro - remise_items_pro) * $bltva * " . $_SESSION['tva'] . ") WHERE id_pro =$id_pro";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $query = "UPDATE t_proforma SET bic_pro=((((mnt_theo_pro - remise_items_pro)+((mnt_theo_pro - remise_items_pro) * $bltva * " . $_SESSION['tva'] . "))) * $blbic * " . $_SESSION['bic'] . ")
                           WHERE id_pro =$id_pro";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($exoclt > 0) {
                    $query = "UPDATE t_proforma SET crdt_pro=(mnt_theo_pro-remise_items_pro)  WHERE id_pro =$id_pro";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                } else {
                    $query = "UPDATE t_proforma SET crdt_pro=(mnt_theo_pro-remise_items_pro+tva_pro+bic_pro)  WHERE id_pro =$id_pro";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }
                /* on met a jour si paye ou pas */
                $query = "select crdt_pro,som_verse_crdt,remise_items_pro FROM t_proforma 
                WHERE t_proforma.id_pro=$id_pro";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($r->num_rows > 0) {
                    $row = $r->fetch_assoc();
                    if ($row['som_verse_crdt'] >= ($row['crdt_pro'])) {
                        $qu = "Update t_proforma 
                  set bl_crdt_regle=1  WHERE id_pro=$id_pro";
                        $r = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
                    }
                }
            }

            $this->mysqli->commit();
            $this->mysqli->autocommit(TRUE);
            return $r;
        } catch (Exception $exc) {
            $this->mysqli->rollback();
            $this->mysqli->autocommit(TRUE);
            $this->response($exc, 204);
        }
    }

    public function fctUndoVntByFact($fact) {
        $id_fact = intval($fact);

        $query = "SELECT f.id_fact,f.bl_tva,f.bl_bic,v.id_vnt,v.pu_theo_vnt,v.qte_vnt,v.mnt_theo_vnt
            FROM t_facture_vente f
            inner join t_vente v on v.facture_vnt=f.id_fact
                WHERE v.facture_vnt = $id_fact";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $re = "";
        if ($r->num_rows > 0) {
            while ($row = $r->fetch_assoc()) {
                $re = $this->fctUndoVnt($row);
            }
        }
        return $re;
    }

    public function fctSupVntByFact($fact) {
        $id_fact = intval($fact);

        $query = "SELECT f.id_fact,f.bl_tva,f.bl_bic,v.id_vnt,v.pu_theo_vnt,v.qte_vnt,v.mnt_theo_vnt
            FROM t_facture_vente f
            inner join t_vente v on v.facture_vnt=f.id_fact
                WHERE v.facture_vnt = $id_fact";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $re = "";
        if ($r->num_rows > 0) {
            while ($row = $r->fetch_assoc()) {
                $re = $this->fctSupVnt($row);
            }
        }
        return $re;
    }

    public function showFactureDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id_fact = intval($fact['id_fact']);

        $query = "SELECT a.code_art,a.nom_art,
                         v.id_vnt,f.id_fact,f.bl_fact_grt,f.bl_bic,f.bl_tva,v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,f.caissier_fact
                          FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE f.id_fact=$id_fact ORDER BY a.nom_art ASC";

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

    public function showProDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id_pro = intval($fact['id_pro']);

        $query = "SELECT a.code_art,a.nom_art,
                         v.id_items,f.id_pro,f.bl_pro_grt,f.bl_bic,f.bl_tva,v.qte_items,v.pu_theo_items,v.mnt_theo_items,v.date_items,f.caissier_pro
                          FROM t_proforma_items v 
                         INNER JOIN t_article a ON v.article_items=a.id_art
                         INNER JOIN t_proforma f ON v.facture_items=f.id_pro
                          WHERE f.id_pro=$id_pro ORDER BY a.nom_art ASC";

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

    public function encaissGrt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id_fact = intval($fact['id_fact']);

        $query = "UPDATE t_facture_vente set bl_encaiss_grt=1,bl_crdt_regle=1, date_encaiss_grt=now() where id_fact=$id_fact ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $response = array("status" => 0,
            "datas" => $r,
            "message" => "");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }

    public function encaiss() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id_fact = intval($fact['id_fact']);

        $query = "UPDATE t_facture_vente set bl_caiss=1 where id_fact=$id_fact ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $response = array("status" => 0,
            "datas" => $r,
            "message" => "");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }

    public function showFacturegDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $client = $_POST;

        $id_clt = intval($client['id_clt']);

        $query = "SELECT a.code_art,a.nom_art,
                         v.id_vnt,f.bl_bic,f.bl_tva,v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,f.caissier_fact,
                         f.code_fact,f.bl_fact_grt,date(f.date_fact) as date_fact,f.code_caissier_fact
                          FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE f.clnt_fact=$id_clt AND f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.bl_crdt_regle=0 ORDER BY v.id_vnt DESC,a.nom_art ASC";

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

    public function showFacturegGrtDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $client = $_POST;

        $id_clt = intval($client['id_clt']);

        $query = "SELECT a.code_art,a.nom_art,
                         v.id_vnt,f.bl_bic,f.bl_tva,v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,f.caissier_fact,
                         f.code_fact,f.bl_fact_grt,date(f.date_fact) as date_fact,f.code_caissier_fact
                          FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE f.clnt_fact=$id_clt AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND f.bl_crdt_regle=0 ORDER BY v.id_vnt DESC,a.nom_art ASC";

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

    public function getFactBycode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['vr']);
        $dt = "";

        $qc = "";
        if (strtolower($code) == "annulee")
            $qc = " OR f.sup_fact=1";

        if (strtolower($code) == "remise")
            $qc = " OR f.remise_vnt_fact>0";

        if (isDate($code))
            $dt = " OR date(f.date_fact)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,
            f.date_sup_fact,f.bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,f.bl_fact_grt,
            f.remise_vnt_fact,f.som_verse_crdt,f.code_fact,
            f.bl_fact_crdt,f.bl_bic,f.bl_tva,f.date_fact,f.date_reg_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,
            COALESCE(c.id_clt,0) as id_clt,
            m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f 
                           INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_fact_crdt=0 AND (f.code_fact LIKE '%$code%' $qc OR c.nom_clt LIKE '%$code%' $dt) $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFactGrtEncBycode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['vr']);
        $dt = "";

        $qc = "";
        if (strtolower($code) == "annulee")
            $qc = " OR f.sup_fact=1";

        if (strtolower($code) == "remise")
            $qc = " OR f.remise_vnt_fact>0";

        if (isDate($code))
            $dt = " OR date(f.date_fact)='" . isoToMysqldate($code) . "' OR date(f.date_encaiss_grt)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,f.date_sup_fact,
            f.bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,f.bl_fact_grt,f.remise_vnt_fact,
            f.som_verse_crdt,f.code_fact,f.bl_fact_crdt,f.bl_bic,f.bl_tva,f.date_fact,f.date_reg_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,
            COALESCE(c.id_clt,0) as id_clt,
            m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f 
                           INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE  f.bl_fact_crdt=1 AND f.bl_encaiss_grt=1 AND (f.code_fact LIKE '%$code%' $qc OR c.nom_clt LIKE '%$code%' $dt) $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFactFaBycode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['vr']);
        $dt = "";

        $qc = "";
        if (strtolower($code) == "annulee")
            $qc = " OR f.sup_fact=1";

        if (strtolower($code) == "remise")
            $qc = " OR f.remise_vnt_fact>0";

        if (isDate($code))
            $dt = " OR date(f.date_fact)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,
            f.date_fact,f.date_sup_fact,f.bl_fact_crdt,f.bl_crdt_regle,
            f.crdt_fact,f.bl_fact_grt,f.remise_vnt_fact,f.som_verse_crdt,
            f.code_fact,f.bl_fact_crdt,f.bl_bic,f.bl_tva,f.date_fact,f.date_reg_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,
            COALESCE(c.id_clt,0) as id_clt,
            m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f 
                           INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE 1=1 AND (f.code_fact LIKE '%$code%' $qc OR c.nom_clt LIKE '%$code%' $dt) $condmag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFactGrtBycode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['vr']);
        $dt = "";

        $qc = "";
        if (strtolower($code) == "annulee")
            $qc = " OR f.sup_fact=1";

        if (strtolower($code) == "remise")
            $qc = " OR f.remise_vnt_fact>0";

        if (isDate($code))
            $dt = " OR date(f.date_fact)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,
            f.date_sup_fact,f.bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,
            f.bl_fact_grt,f.remise_vnt_fact,f.som_verse_crdt,
            f.code_fact,f.bl_fact_crdt,f.bl_bic,f.bl_tva,f.date_fact,f.date_reg_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,
            COALESCE(c.id_clt,0) as id_clt,
            m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_fact_grt=1 AND f.bl_encaiss_grt=0 AND f.crdt_fact>0 AND (f.code_fact LIKE '%$code%' $qc OR c.nom_clt LIKE '%$code%' $dt) $condmag
                           ORDER BY f.id_fact DESC";



        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function getFactCrdtBycode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['vr']);
        $dt = "";

        $qc = "";
        if (strtolower($code) == "annulee")
            $qc = " OR f.sup_fact=1";

        if (strtolower($code) == "remise")
            $qc = " OR f.remise_vnt_fact>0";

        if (isDate($code))
            $dt = " OR date(f.date_fact)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT f.motif_sup_fact,f.sup_by_fact,f.id_fact,f.sup_fact,f.date_fact,
            f.date_sup_fact,f.bl_fact_crdt,f.bl_crdt_regle,f.crdt_fact,f.bl_fact_grt,
            f.remise_vnt_fact,f.som_verse_crdt,f.code_fact,
            f.bl_fact_crdt,f.bl_bic,f.bl_tva,f.date_fact,f.date_reg_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,COALESCE(c.exo_tva_clt,0) as exo_tva_clt,
            COALESCE(c.id_clt,0) as id_clt,
            m.nom_mag,m.code_mag,f.code_caissier_fact,f.caissier_fact,u.mag_user
                           FROM 
                           t_facture_vente f
                            INNER JOIN t_user u ON f.caissier_fact=u.id_user 
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=0  AND f.crdt_fact>0 AND (f.code_fact LIKE '%$code%' $qc OR c.nom_clt LIKE '%$code%' $dt) $condmag
                           ORDER BY f.id_fact DESC";



        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['supok'] = false;
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

    public function gettvaFactBycode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['vr']);
        $dt = "";

        $qc = "";
        if (strtolower($code) == "annulee")
            $qc = " OR f.sup_fact=1";

        if (strtolower($code) == "remise")
            $qc = " OR f.remise_vnt_fact>0";

        if (isDate($code))
            $dt = " OR date(f.date_fact)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        /* if ($_SESSION['userMag'] > 0)
          $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']); */

        $query = "SELECT f.id_fact,f.code_fact,f.bl_crdt_regle,f.crdt_fact,f.remise_vnt_fact,f.som_verse_crdt,f.bl_fact_crdt,f.bl_fact_grt,f.bl_bic,f.bl_tva,f.date_fact,
            c.code_clt,COALESCE(c.nom_clt,'-') as nom_clt,
            COALESCE(c.id_clt,0) as id_clt,
            m.nom_ma