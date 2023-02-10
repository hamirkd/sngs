<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class reglementController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getcrnv() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        $query = "SELECT COUNT(*) as crnv 
            FROM t_creance_client WHERE vu=0 limit 1";

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

    public function etatReglementsClt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT date(c.date_crce_clnt) as date_crce_clnt,
            c.code_caissier_crce,
            c.caissier_login_crce,
            c.mnt_glob_crce_clnt,
            c.mnt_paye_crce_clnt,
            c.id_crce_clnt,
            c.ref_crce_clnt,
            c.id_reg,
            c.vu,
            fv.code_fact,
            fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            m.code_mag,
            (crdt_fact-som_verse_crdt) as reste
            FROM t_creance_client c 
            INNER JOIN t_facture_vente fv ON c.fact_crce_clnt=fv.id_fact
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            INNER JOIN t_magasin m ON fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_grt=0 $condmag";
        if (!empty($search['user']))
            $query.=" AND c.code_caissier_crce='" . $this->esc($search['user']) . "'";


        if (!empty($search['ref']))
            $query.=" AND c.ref_crce_clnt LIKE '%" . $this->esc($search['ref']) . "%'";


        if (!empty($search['client']))
            $query.=" AND fv.clnt_fact=" . intval($search['client']);

        if (!empty($search['magasin']))
            $query.=" AND m.id_mag=" . intval($search['magasin']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(c.date_crce_clnt)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(c.date_crce_clnt) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.=" Order BY c.date_crce_clnt DESC ";



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

    public function queryEtatRetardPayements() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['q']);
        $dt = "";
        if (isDate($code))
            $dt = " OR date(fv.date_fact)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT  
            fv.code_fact,
            fv.date_fact,
            fv.delai_pay_fact,
            datediff(fv.delai_pay_fact,now()) as nbjours,
             fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            fv.code_caissier_fact,
            (crdt_fact-som_verse_crdt) as reste
            FROM   t_facture_vente fv
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt 
            where fv.sup_fact=0 AND (fv.code_fact LIKE '%$code%' OR cl.nom_clt LIKE '%$code%' $dt) AND fv.bl_fact_crdt=1 $condmag AND fv.bl_crdt_regle=0 AND datediff(fv.delai_pay_fact,now())<" . $_SESSION['ret_pay'] . " order by nbjours ASC
             ";

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

    public function queryEtatRegClt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['q']);
        $dt = "";
        if (isDate($code))
            $dt = " OR date(c.date_crce_clnt)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT date(c.date_crce_clnt) as date_crce_clnt,
            c.code_caissier_crce,
            c.caissier_login_crce,
             c.mnt_glob_crce_clnt,
            c.mnt_paye_crce_clnt,
            c.id_crce_clnt,
            c.ref_crce_clnt,
             c.id_reg,
            c.vu,
            fv.code_fact,
            fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            (crdt_fact-som_verse_crdt) as reste
            FROM t_creance_client c 
            INNER JOIN t_facture_vente fv ON c.fact_crce_clnt=fv.id_fact
            INNER JOIN t_magasin m on fv.mag_fact=m.id_mag
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            WHERE fv.sup_fact=0 AND fv.bl_fact_grt=0 AND (fv.code_fact LIKE '%$code%' OR cl.nom_clt LIKE '%$code%' OR c.ref_crce_clnt LIKE '%$code%' $dt) $condmag";


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

    public function etatReglementsGrtClt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT date(c.date_crce_clnt) as date_crce_clnt,
            c.code_caissier_crce,
            c.caissier_login_crce,
            c.mnt_paye_crce_clnt,
             c.mnt_glob_crce_clnt,
            c.id_crce_clnt,
            c.vu,
            fv.code_fact,
            fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            m.code_mag,
            (crdt_fact-som_verse_crdt) as reste
            FROM t_creance_client c 
            INNER JOIN t_facture_vente fv ON c.fact_crce_clnt=fv.id_fact
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            INNER JOIN t_magasin m ON fv.mag_fact=m.id_mag
            WHERE fv.sup_fact=0 AND fv.bl_fact_grt=1 $condmag";
        if (!empty($search['user']))
            $query.=" AND c.code_caissier_crce='" . $this->esc($search['user']) . "'";


        if (!empty($search['client']))
            $query.=" AND fv.clnt_fact=" . intval($search['client']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(c.date_crce_clnt)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(c.date_crce_clnt) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.=" Order BY c.date_crce_clnt DESC ";



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

    public function queryEtatRegGrtClt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $code = $this->esc($_GET['q']);
        $dt = "";
        if (isDate($code))
            $dt = " OR date(c.date_crce_clnt)='" . isoToMysqldate($code) . "'";

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND m.id_mag=" . intval($_SESSION['userMag']);

        $query = "SELECT date(c.date_crce_clnt) as date_crce_clnt,
            c.code_caissier_crce,
            c.caissier_login_crce,
             c.mnt_glob_crce_clnt,
            c.mnt_paye_crce_clnt,
            c.id_crce_clnt,
            c.vu,
            fv.code_fact,
            fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            (crdt_fact-som_verse_crdt) as reste
            FROM t_creance_client c 
            INNER JOIN t_facture_vente fv ON c.fact_crce_clnt=fv.id_fact
            INNER JOIN t_magasin m on fv.mag_fact=m.id_mag
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            WHERE fv.sup_fact=0 AND fv.bl_fact_grt=1 AND (fv.code_fact LIKE '%$code%' OR cl.nom_clt LIKE '%$code%' $dt) $condmag";

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

    public function etatReglementsLastClt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";


        $query = "(SELECT date(c.date_crce_clnt) as date_crce_clnt,
            c.code_caissier_crce,
            c.caissier_login_crce,
             c.mnt_glob_crce_clnt,
            c.mnt_paye_crce_clnt,
             c.id_crce_clnt,
             c.ref_crce_clnt,
             c.id_reg,
             c.vu,
            fv.code_fact,
             fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            m.code_mag,
            (crdt_fact-som_verse_crdt) as reste
            FROM t_creance_client c 
            INNER JOIN t_facture_vente fv ON c.fact_crce_clnt=fv.id_fact
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt 
            INNER JOIN t_magasin m ON fv.mag_fact=m.id_mag
            where fv.sup_fact=0 AND fv.bl_fact_grt=0 AND date(c.date_crce_clnt)=date(now()) $condmag order by c.date_crce_clnt DESC)
                UNION
                (SELECT date(c.date_crce_clnt) as date_crce_clnt,
            c.code_caissier_crce,
            c.caissier_login_crce,
             c.mnt_glob_crce_clnt,
            c.mnt_paye_crce_clnt,
             c.id_crce_clnt,
             c.ref_crce_clnt,
              c.id_reg,
             c.vu,
            fv.code_fact,
             fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            m.code_mag,
            (crdt_fact-som_verse_crdt) as reste
            FROM t_creance_client c 
            INNER JOIN t_facture_vente fv ON c.fact_crce_clnt=fv.id_fact
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt 
            INNER JOIN t_magasin m ON fv.mag_fact=m.id_mag
            where fv.sup_fact=0 AND fv.bl_fact_grt=0 AND c.vu = 0 $condmag 
             order by c.date_crce_clnt DESC)
                ";


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

    public function etatRetardPayementsLastClt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";


        $query = "SELECT  
            fv.code_fact,
            fv.date_fact,
            fv.delai_pay_fact,
            datediff(fv.delai_pay_fact,now()) as nbjours,
             fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            m.code_mag,
            fv.code_caissier_fact,
            (crdt_fact-som_verse_crdt) as reste
            FROM   t_facture_vente fv
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            INNER JOIN t_magasin m ON m.id_mag=fv.mag_fact
            WHERE fv.sup_fact=0 AND fv.bl_fact_crdt=1 $condmag AND fv.bl_crdt_regle=0 AND datediff(fv.delai_pay_fact,now())<" . $_SESSION['ret_pay'] . " order by nbjours ASC
             ";

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

    public function etatRetardPayementsClt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";


        $query = "SELECT  
            fv.code_fact,
            fv.date_fact,
            fv.delai_pay_fact,
            datediff(fv.delai_pay_fact,now()) as nbjours,
             fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            m.code_mag,
            fv.code_caissier_fact,
            (crdt_fact-som_verse_crdt) as reste
            FROM   t_facture_vente fv
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt 
            INNER JOIN t_magasin m ON m.id_mag=fv.mag_fact
            WHERE fv.sup_fact=0 AND fv.bl_fact_crdt=1 $condmag AND fv.bl_crdt_regle=0 AND datediff(fv.delai_pay_fact,now())<" . $_SESSION['ret_pay'] . " 
             ";

        if (!empty($search['user']))
            $query.=" AND fv.login_caissier_fact='" . $this->esc($search['user']) . "'";

        if (!empty($search['client']))
            $query.=" AND fv.clnt_fact=" . intval($search['client']);

        if (!empty($search['magasin']))
            $query.=" AND m.id_mag=" . intval($search['magasin']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(fv.delai_pay_fact)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(fv.delai_pay_fact) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query .="order by nbjours ASC";

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

    public function etatReglementsGrtLastClt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";


        $query = "SELECT date(c.date_crce_clnt) as date_crce_clnt,
            c.code_caissier_crce,
            c.caissier_login_crce,
             c.mnt_glob_crce_clnt,
            c.mnt_paye_crce_clnt,
             c.id_crce_clnt,
             c.vu,
            fv.code_fact,
             fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            m.code_mag,
            (crdt_fact-som_verse_crdt) as reste
            FROM t_creance_client c 
            INNER JOIN t_facture_vente fv ON c.fact_crce_clnt=fv.id_fact
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt 
            INNER JOIN t_magasin m ON fv.mag_fact=m.id_mag
            where fv.sup_fact=0 AND fv.bl_fact_grt=1 AND date(c.date_crce_clnt)=date(now()) $condmag order by c.date_crce_clnt DESC ";

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

    public function etatReglementsFrns() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;


        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND ap.user_appro in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT date(d.date_dette_frns) as date_dette_frns,
            d.code_caissier_frns,
            d.caissier_login_frns,
            d.mnt_paye_dette_frns,
            d.ref_dette_frns,
            ap.bon_liv_appro,
            ap.id_appro,
            ap.date_appro,
            f.code_frns,
            f.nom_frns 
            FROM t_dette_fournisseur d
            INNER JOIN t_approvisionnement ap ON ap.id_appro=d.bon_dette_frns
            INNER JOIN t_fournisseur f ON ap.frns_appro=f.id_frns
            WHERE 1=1 $condmag";

        if (!empty($search['user']))
            $query.=" AND d.code_caissier_frns='" . $this->esc($search['user']) . "'";


        if (!empty($search['ref']))
            $query.=" AND d.ref_dette_frns LIKE '%" . $this->esc($search['ref']) . "%'";


        if (!empty($search['fournisseur']))
            $query.=" AND f.id_frns=" . intval($search['fournisseur']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(d.date_dette_frns)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(d.date_dette_frns) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.=" order by d.date_dette_frns DESC";


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

    public function etatReglementsLastFrns() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND ap.user_appro in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";


        $query = "SELECT date(d.date_dette_frns) as date_dette_frns,
            d.code_caissier_frns,
            d.caissier_login_frns,
            d.mnt_paye_dette_frns,
            d.ref_dette_frns,
            ap.bon_liv_appro,
             ap.id_appro,
            ap.date_appro,
            f.code_frns,
            f.nom_frns 
            FROM t_dette_fournisseur d
            INNER JOIN t_approvisionnement ap ON ap.id_appro=d.bon_dette_frns
            INNER JOIN t_fournisseur f ON ap.frns_appro=f.id_frns
            where 1=1 $condmag order by d.date_dette_frns DESC
            LIMIT 10";

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

    public function getDettesFournisseurs() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND a.user_appro in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT a.id_appro,a.bon_liv_appro,f.code_frns,f.nom_frns
                           FROM 
                           t_approvisionnement a
                           INNER JOIN t_fournisseur f ON a.frns_appro=f.id_frns 
                           WHERE a.bl_bon_dette=1 
                           AND a.bl_dette_regle=0 AND a.mnt_revient_appro>0 AND (a.mnt_revient_appro-a.som_verse_dette)>0 $condmag
                           ORDER BY a.date_appro  ASC";

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

    public function getDettesgFournisseurs() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND a.user_appro in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT f.id_frns,f.code_frns,f.nom_frns
                           FROM 
                           t_approvisionnement a
                           INNER JOIN t_fournisseur f ON a.frns_appro=f.id_frns 
                           WHERE a.bl_bon_dette=1 
                           AND a.bl_dette_regle=0 AND a.mnt_revient_appro>0 AND (a.mnt_revient_appro-a.som_verse_dette)>0 $condmag
                          GROUP BY f.id_frns ORDER BY f.nom_frns ASC";

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

    public function getDetteDetails() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_appro'])) {
            $id_appro = intval($this->_request['id_appro']);
            $query = "SELECT a.id_appro,a.mnt_revient_appro,a.som_verse_dette,a.date_appro,a.code_user_appro,a.login_appro,
                f.id_frns,f.nom_frns 
                 FROM t_approvisionnement a
                  INNER JOIN t_fournisseur f ON a.frns_appro=f.id_frns
                 WHERE a.id_appro=$id_appro AND a.bl_bon_dette=1 AND bl_dette_regle=0 LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();

            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Valeurs incorrectes de la dette!");
        $this->response($this->json($response), 200);
    }

    public function getDettegDetails() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_frns'])) {
            $id_frns = intval($this->_request['id_frns']);
            $query = "SELECT f.id_frns,f.code_frns,sum(a.mnt_revient_appro) as mnt_revient_appro,
    sum(a.som_verse_dette) as som_verse_dette 
                 FROM t_approvisionnement a
                  INNER JOIN t_fournisseur f ON a.frns_appro=f.id_frns
                 WHERE f.id_frns=$id_frns AND a.mnt_revient_appro>0 AND a.bl_bon_dette=1 AND bl_dette_regle=0 
                     GROUP BY f.id_frns LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();

            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Valeurs incorrectes de la dette!");
        $this->response($this->json($response), 200);
    }

    public function paidReglementFrns() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appReglements = $_POST;
        $id_appro = intval($appReglements['id_appro']);
        $id_frns = intval($appReglements['id_frns']);
        $mnt_avance = doubleval($appReglements['mnt_versement']);
        $ref = $this->esc($appReglements['ref']);
        $mnt_dette = doubleval($appReglements['mnt_dette']);
        $date_reg = (!empty($appReglements['date_dette_frns'])) ? isoToMysqldate($appReglements['date_dette_frns']) : date("Y-m-d");



        $response = array();

        if (!empty($id_appro) && !empty($mnt_avance)) {

            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_dette_fournisseur (
                     	bon_dette_frns,
                        frns_dette,
                     mnt_paye_dette_frns,
                     ref_dette_frns,
                     date_dette_frns,
                     caissier_dette_frns,
                     caissier_login_frns,
                     code_caissier_frns) 
                     VALUES(" . $id_appro . ",
                         " . $id_frns . ",
                          " . $mnt_avance . ", 
                          '" . $ref . "', 
                              CONCAT('$date_reg',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);



                $query = "UPDATE t_approvisionnement SET som_verse_dette=som_verse_dette + $mnt_avance WHERE id_appro =$id_appro";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $query = "UPDATE t_fournisseur SET dette_en_cours_frns=dette_en_cours_frns-" . $mnt_avance . " WHERE id_frns=(select frns_appro from t_approvisionnement where id_appro=$id_appro)";
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $query = " SELECT SUM(mnt_paye_dette_frns) as mnt FROM t_dette_fournisseur WHERE bon_dette_frns=$id_appro";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $result = $r->fetch_assoc();
                $mnt = $result['mnt'];


                if ($mnt >= $mnt_dette) {
                    $query = "UPDATE t_approvisionnement SET bl_dette_regle=1 WHERE id_appro =$id_appro";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => " Versement avance effectue avec success!");

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

    public function paidReglementgFrns() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appReglements = $_POST;
        $id_frns = intval($appReglements['id_frns']);
        $mnt_avance = doubleval($appReglements['mnt_versement']);
        $ref = $this->esc($appReglements['ref']);

        $date_reg = (!empty($appReglements['date_dette_frns'])) ? isoToMysqldate($appReglements['date_dette_frns']) : date("Y-m-d");

        $response = array();

        do {
            try {
                $this->mysqli->autocommit(FALSE);
                $condMag = "";
                if ($_SESSION['userMag'] > 0)
                    $condMag = " AND a.user_appro in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

                $query = "SELECT a.id_appro,a.mnt_revient_appro,
     a.som_verse_dette 
                 FROM t_approvisionnement a
                  INNER JOIN t_fournisseur f ON a.frns_appro=f.id_frns
                 WHERE f.id_frns=$id_frns AND a.mnt_revient_appro>0 AND a.bl_bon_dette=1 AND bl_dette_regle=0 
                   $condMag  ORDER BY a.id_appro ASC LIMIT 1";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($r->num_rows > 0) {
                    $row = $r->fetch_assoc();
                    $_id_appro = $row['id_appro'];
                    $_dette_appro = $row['mnt_revient_appro'];
                    $_som_verse_dette = $row['som_verse_dette'];

                    $_montant_a_completer = $_dette_appro - $_som_verse_dette;

                    if ($mnt_avance <= $_montant_a_completer) {
                        $heure_vnt = date("H:i:s");
                        $query = "INSERT INTO  t_dette_fournisseur (
                     	bon_dette_frns,
                        frns_dette,
                     mnt_paye_dette_frns,
                     ref_dette_frns,
                     date_dette_frns,
                     caissier_dette_frns,
                     caissier_login_frns,
                     code_caissier_frns) 
                     VALUES(" . $_id_appro . ",
                         " . $id_frns . ",
                          " . $mnt_avance . ", 
                          '" . $ref . "', 
                              CONCAT('$date_reg',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";

                        if (!$r = $this->mysqli->query($query))
                            throw new Exception($this->mysqli->error . __LINE__);


                        $query = "UPDATE t_approvisionnement SET som_verse_dette=som_verse_dette + $mnt_avance WHERE id_appro =$_id_appro";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


                        $query = "SELECT a.id_appro,a.mnt_revient_appro,
     a.som_verse_dette 
                 FROM t_approvisionnement a
                  WHERE a.id_appro=$_dette_appro  ORDER BY a.id_appro ASC LIMIT 1";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        $result = $r->fetch_assoc();

                        if (($result['som_verse_dette']) >= $_dette_appro) {

                            $query = "UPDATE t_approvisionnement SET bl_dette_regle=1 WHERE id_appro =$_id_appro";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }
                        $mnt_avance = 0;
                    } else {

                        $heure_vnt = date("H:i:s");
                        $query = "INSERT INTO  t_dette_fournisseur (
                     	bon_dette_frns,
                        frns_dette,
                     mnt_paye_dette_frns,
                     ref_dette_frns,
                     date_dette_frns,
                     caissier_dette_frns,
                     caissier_login_frns,
                     code_caissier_frns) 
                     VALUES(" . $_id_appro . ",
                         " . $id_frns . ",
                          " . $_montant_a_completer . ", 
                          '" . $ref . "', 
                               CONCAT('$date_reg',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";

                        if (!$r = $this->mysqli->query($query))
                            throw new Exception($this->mysqli->error . __LINE__);


                        $query = "UPDATE t_approvisionnement SET som_verse_dette=som_verse_dette + $_montant_a_completer WHERE id_appro =$_id_appro";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                        $query = " SELECT SUM(mnt_paye_dette_frns) as mnt FROM t_dette_fournisseur WHERE  bon_dette_frns=$_id_appro";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        $result = $r->fetch_assoc();
                        $mnt = $result['mnt'];

                        if ($mnt >= ($_dette_appro)) {
                            $query = "UPDATE t_approvisionnement SET bl_dette_regle=1 WHERE id_appro =$_id_appro";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }
                        $mnt_avance -= $_montant_a_completer;
                    }
                } else {/* num rows est <0 */
                    $mnt_avance = 0;
                }

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);
            } catch (Exception $exc) {
                $this->mysqli->rollback();
                $this->mysqli->autocommit(TRUE);
                $response = array("status" => 1,
                    "datas" => "",
                    "message" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } while ($mnt_avance > 0); /* fin du dowhile */


        $response = array("status" => 0,
            "datas" => "",
            "message" => " Reglement avance effectue avec success!");

        $this->response($this->json($response), 200);
    }

    public function getCreancesClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $c_us = " AND 1=1 ";
        if ($_SESSION['mode_clnt_uniq'] == 0)
            $c_us = " AND c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";


        $condMag = "";
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $query = "SELECT f.id_fact,f.code_fact,f.bl_tva,DATE_FORMAT(f.date_fact, '%d/%m/%Y %T') as date_fact,c.code_clt,c.nom_clt
                           FROM 
                           t_facture_vente f
                           INNER JOIN t_client c ON f.clnt_fact=c.id_clt 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.sup_fact=0
                           $c_us
                           AND f.bl_crdt_regle=0 AND f.crdt_fact>0 AND (f.crdt_fact-f.som_verse_crdt)>0 $condMag
                           ORDER BY f.id_fact DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['mnt_reste'] = $this->fctGetCreanceDetails($row['id_fact']);
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

    public function getCreancesGrtClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $c_us = " AND 1=1 ";
        if ($_SESSION['mode_clnt_uniq'] == 0)
            $c_us = " AND c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";

        $condMag = "";
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $query = "SELECT f.id_fact,f.code_fact,f.bl_tva,c.code_clt,c.nom_clt
                           FROM 
                           t_facture_vente f
                           INNER JOIN t_client c ON f.clnt_fact=c.id_clt 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND f.sup_fact=0
                           $c_us
                           AND f.bl_crdt_regle=0 AND f.crdt_fact>0 AND (f.crdt_fact-f.som_verse_crdt)>0 $condMag
                           ORDER BY f.id_fact DESC";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['mnt_reste'] = $this->fctGetCreanceGrtDetails($row['id_fact']);
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

    public function getCreancesgClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $c_us = " AND 1=1 ";
        if ($_SESSION['mode_clnt_uniq'] == 0)
            $c_us = " AND c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";


        $condMag = "";
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $query = "SELECT c.id_clt,c.code_clt,c.nom_clt
                           FROM 
                           t_facture_vente f
                           INNER JOIN t_client c ON f.clnt_fact=c.id_clt 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.sup_fact=0
                          $c_us
                           AND f.bl_crdt_regle=0 AND f.crdt_fact>0 AND (f.crdt_fact-f.som_verse_crdt)>0 $condMag
                           GROUP BY c.id_clt ORDER BY c.nom_clt ASC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['mnt_reste'] = $this->fctGetCreancegDetails($row['id_clt']);
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

    public function getCreancesGrtgClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $c_us = " AND 1=1 ";
        if ($_SESSION['mode_clnt_uniq'] == 0)
            $c_us = " AND c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";

        $condMag = "";
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $query = "SELECT c.id_clt,c.code_clt,c.nom_clt
                           FROM 
                           t_facture_vente f
                           INNER JOIN t_client c ON f.clnt_fact=c.id_clt 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND f.sup_fact=0
                           $c_us
                           AND f.bl_crdt_regle=0 AND f.crdt_fact>0 AND (f.crdt_fact-f.som_verse_crdt)>0 $condMag
                           GROUP BY c.id_clt ORDER BY c.nom_clt ASC";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['mnt_reste'] = $this->fctGetCreancegGrtDetails($row['id_clt']);
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

    public function getCreanceDetails() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_fact'])) {
            $id_fact = intval($this->_request['id_fact']);
            $query = "SELECT f.crdt_fact,f.bl_tva,f.som_verse_crdt,f.remise_vnt_fact,f.date_fact,f.code_caissier_fact,f.login_caissier_fact,
                 m.nom_mag,c.id_clt,c.nom_clt 
                 FROM t_facture_vente f
                 INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
                 INNER JOIN t_client c ON f.clnt_fact=c.id_clt
                 WHERE f.bl_fact_grt=0 AND f.id_fact=$id_fact AND f.bl_fact_crdt=1 AND bl_crdt_regle=0 LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();

            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Valeurs incorrectes de la creance!");
        $this->response($this->json($response), 200);
    }

    public function fctGetCreanceDetails($id_fact) {

        if (!empty($id_fact)) {
            $id_fact = intval($id_fact);
            $query = "SELECT (f.crdt_fact-f.som_verse_crdt) as mnt_reste 
                 FROM t_facture_vente f
                INNER JOIN t_client c ON f.clnt_fact=c.id_clt
                 WHERE f.bl_fact_grt=0 AND f.id_fact=$id_fact AND f.bl_fact_crdt=1 AND bl_crdt_regle=0 LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();

            return $result['mnt_reste'];
        }
    }

    public function getCreanceGrtDetails() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_fact'])) {
            $id_fact = intval($this->_request['id_fact']);
            $query = "SELECT f.crdt_fact,f.som_verse_crdt,f.bl_tva,f.remise_vnt_fact,f.date_fact,f.code_caissier_fact,f.login_caissier_fact,
                 m.nom_mag,c.id_clt,c.nom_clt 
                 FROM t_facture_vente f
                 INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
                 INNER JOIN t_client c ON f.clnt_fact=c.id_clt
                 WHERE f.bl_fact_grt=1 AND f.id_fact=$id_fact AND f.bl_fact_crdt=1 AND bl_crdt_regle=0 LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();

            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Valeurs incorrectes de la creance!");
        $this->response($this->json($response), 200);
    }

    public function fctGetCreanceGrtDetails($id_fact) {


        if (!empty($id_fact)) {
            $id_fact = intval($id_fact);
            $query = "SELECT (f.crdt_fact-f.som_verse_crdt) as mnt_reste
                 FROM t_facture_vente f
                 INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
                  WHERE f.bl_fact_grt=1 AND f.id_fact=$id_fact AND f.bl_fact_crdt=1 AND bl_crdt_regle=0 LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();
            return $result['mnt_reste'];
        }
    }

    public function getCreancegDetails() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_clt'])) {
            $id_clt = intval($this->_request['id_clt']);
            $query = "SELECT c.id_clt,c.code_clt,c.nom_clt,SUM(crdt_fact) as mnt_creditg,SUM(som_verse_crdt) as som_verseg,
    SUM(remise_vnt_fact) as remiseg
                 FROM t_facture_vente f
                  INNER JOIN t_client c ON f.clnt_fact=c.id_clt
                 WHERE f.mag_fact=" . $_SESSION['userMag'] . " 
                     AND f.clnt_fact=$id_clt AND 
                           f.sup_fact=0 AND
                          f.crdt_fact>0 AND
                         f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.bl_crdt_regle=0
                    GROUP BY c.id_clt LIMIT 1";


            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();

            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Valeur incorrecte du creancier!");
        $this->response($this->json($response), 200);
    }

    public function fctGetCreancegDetails($id_clt) {


        if (!empty($id_clt)) {
            $id_clt = intval($id_clt);
            $query = "SELECT (SUM(crdt_fact)-SUM(som_verse_crdt)) as mnt_reste
                 FROM t_facture_vente f
                  INNER JOIN t_client c ON f.clnt_fact=c.id_clt
                 WHERE f.mag_fact=" . $_SESSION['userMag'] . " 
                     AND f.clnt_fact=$id_clt
                         AND f.crdt_fact>0 AND f.sup_fact=0 AND
                         f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.bl_crdt_regle=0
                    GROUP BY c.id_clt LIMIT 1";


            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();
            return $result['mnt_reste'];
        }
    }

    public function getCreancegGrtDetails() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_clt'])) {
            $id_clt = intval($this->_request['id_clt']);
            $query = "SELECT c.id_clt,c.code_clt,c.nom_clt,SUM(crdt_fact) as mnt_creditg,SUM(som_verse_crdt) as som_verseg,
      SUM(remise_vnt_fact) as remiseg
                 FROM t_facture_vente f
                  INNER JOIN t_client c ON f.clnt_fact=c.id_clt
                 WHERE f.mag_fact=" . $_SESSION['userMag'] . " 
                     AND f.clnt_fact=$id_clt AND 
                      f.sup_fact=0 AND
                          f.crdt_fact>0 AND
                         f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND f.bl_crdt_regle=0
                    GROUP BY c.id_clt LIMIT 1";


            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();

            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Valeur incorrecte du creancier!");
        $this->response($this->json($response), 200);
    }

    public function fctGetCreancegGrtDetails($id_clt) {

        if (!empty($id_clt)) {
            $id_clt = intval($id_clt);
            $query = "SELECT (SUM(crdt_fact)- SUM(som_verse_crdt)) as mnt_reste
                 FROM t_facture_vente f
                  INNER JOIN t_client c ON f.clnt_fact=c.id_clt
                 WHERE f.mag_fact=" . $_SESSION['userMag'] . " 
                     AND f.clnt_fact=$id_clt AND 
                         f.crdt_fact>0  AND f.sup_fact=0
                         AND f.bl_fact_crdt=1 AND 
                         f.bl_fact_grt=1 AND 
                         f.bl_crdt_regle=0
                    GROUP BY c.id_clt LIMIT 1";


            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $result = $r->fetch_assoc();

            return $result['mnt_reste'];
        }
    }

    public function paidRemiseClt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appReglements = $_POST;
        $id_fac = intval($appReglements['id_fac']);
        $mnt_remise = doubleval($appReglements['mnt_remise']);
        $mnt_credit = doubleval($appReglements['mnt_credit']);
        $mnt_val_remise = doubleval($appReglements['mnt_rem_act']);

        $nvl_remise = doubleval($mnt_remise + $mnt_val_remise);

        $response = array();

        if (!empty($id_fac) && !empty($mnt_remise)) {

            try {
                $this->mysqli->autocommit(FALSE);

                $query = "UPDATE t_facture_vente SET remise_vnt_fact=remise_vnt_fact + $mnt_remise WHERE id_fact =$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $query = "select f.bl_tva,f.bl_bic,c.exo_tva_clt from t_client c inner join t_facture_vente f ON f.clnt_fact=c.id_clt WHERE f.id_fact =$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $result = $r->fetch_assoc();

                $exoclt = $result['exo_tva_clt'];
                $bltva = $result['bl_tva'];
                $blbic = $result['bl_bic'];

                $query = "UPDATE t_facture_vente SET tva_fact=((mnt_theo_fact - remise_vnt_fact) * $bltva * " . $_SESSION['tva'] . ") WHERE id_fact =$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $query = "UPDATE t_facture_vente SET bic_fact=((((mnt_theo_fact - remise_vnt_fact)+((mnt_theo_fact - remise_vnt_fact) * $bltva * " . $_SESSION['tva'] . "))) * $blbic * " . $_SESSION['bic'] . ")
                           WHERE id_fact =$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($exoclt > 0) {
                    $query = "UPDATE t_facture_vente SET crdt_fact=(mnt_theo_fact-remise_vnt_fact)  WHERE id_fact =$id_fac";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                } else {
                    $query = "UPDATE t_facture_vente SET crdt_fact=(mnt_theo_fact-remise_vnt_fact+tva_fact+bic_fact)  WHERE id_fact =$id_fac";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }



                /* $query = "UPDATE t_client SET credit_en_cours_clt=(credit_en_cours_clt - " . $mnt_remise . ") WHERE id_clt=(select clnt_fact from t_facture_vente where id_fact=$id_fac)";
                  if (!$r = $this->mysqli->query($query))
                  throw new Exception($this->mysqli->error . __LINE__); */

                $query = "select f.crdt_fact from  t_facture_vente f   WHERE f.id_fact =$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $result = $r->fetch_assoc();

                $mnt_credit = $result['crdt_fact'];

                $query = " SELECT SUM(mnt_paye_crce_clnt) as mnt FROM t_creance_client WHERE 	fact_crce_clnt=$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $result = $r->fetch_assoc();
                $mnt = $result['mnt'];


                if ($mnt >= $mnt_credit) {
                    $query = "UPDATE t_facture_vente SET bl_crdt_regle=1 WHERE id_fact =$id_fac";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => " Remise  effectuee avec success!");

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

    public function paidReglementClt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appReglements = $_POST;
        $mnt_val_remise = 0;
        $id_fac = intval($appReglements['id_fac']);
        $id_clt = intval($appReglements['id_clt']);
        $mnt_avance = doubleval($appReglements['mnt_versement']);
        $mnt_credit = doubleval($appReglements['mnt_credit']);
        $ref = $this->esc($appReglements['ref']);

        /* recuperation du montant global de l'avance */
        $mntglobale = $mnt_avance;
        $refavance = time();


        if (isset($appReglements['mnt_rem_act']))
            $mnt_val_remise = doubleval($appReglements['mnt_rem_act']);
        $date_reg = (!empty($appReglements['date_crce_clnt'])) ? isoToMysqldate($appReglements['date_crce_clnt']) : date("Y-m-d");

        $response = array();
        $heure_vnt = date("H:i:s");
        $unikId = getUniqueId();

        if (!empty($id_fac) && !empty($mnt_avance)) {

            try {
                $this->mysqli->autocommit(FALSE);
                $query = "INSERT INTO  t_creance_client (
                     	fact_crce_clnt,
                        clnt_crce,
                        ref_paye_crce_clnt,
                        id_reg,
                        mnt_glob_crce_clnt,
                     mnt_paye_crce_clnt,
                     ref_crce_clnt,
                     date_crce_clnt,
                     caissier_crce_clnt,
                     caissier_login_crce,
                     code_caissier_crce) 
                     VALUES(" . $id_fac . ",
                         " . $id_clt . ",
                         " . $refavance . ",
                         '" . $unikId . "',
                         " . $mntglobale . ",
                          " . $mnt_avance . ",
                              '" . $ref . "',
                             CONCAT('$date_reg',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";

//print_r($query);
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);



                $query = "UPDATE t_facture_vente SET som_verse_crdt=som_verse_crdt + $mnt_avance, date_reg_fact= CONCAT('$date_reg',' ',time(now())) WHERE id_fact =$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $query = " SELECT SUM(mnt_paye_crce_clnt) as mnt FROM t_creance_client WHERE 	fact_crce_clnt=$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $result = $r->fetch_assoc();
                $mnt = $result['mnt'];


                if ($mnt >= ($mnt_credit)) {
                    $query = "UPDATE t_facture_vente SET bl_crdt_regle=1 WHERE id_fact =$id_fac";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => " Versement avance effectue avec success!");

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

    public function paidReglementgClt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appReglements = $_POST;
        $id_clt = intval($appReglements['id_clt']);
        $ref = $this->esc($appReglements['ref']);
        $mnt_avance = doubleval($appReglements['mnt_versement']);

        /* recuperation du montant global de l'avance */
        $mntglobale = $mnt_avance;
        $refavance = time();

        $date_reg = (!empty($appReglements['date_crce_clnt'])) ? isoToMysqldate($appReglements['date_crce_clnt']) : date("Y-m-d");

        $response = array();

        $unikId = getUniqueId();

        do {

            try {
                $this->mysqli->autocommit(FALSE);

                $condMag = "";
                if ($_SESSION['userMag'] > 0)
                    $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

                $queryp = "SELECT f.id_fact,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact
                           FROM 
                           t_facture_vente f
                            WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=0
                           AND f.clnt_fact=$id_clt
                               AND f.crdt_fact>0 AND f.sup_fact=0
                           AND f.bl_crdt_regle=0  $condMag
                           ORDER BY f.id_fact ASC limit 1";


                $rp = $this->mysqli->query($queryp) or die($this->mysqli->error . __LINE__);
                if ($rp->num_rows > 0) {
                    $row = $rp->fetch_assoc();
                    $_id_fact = $row['id_fact'];
                    $_crdt_fact = $row['crdt_fact'];
                    $_som_verse_fact = $row['som_verse_crdt'];
                    $_remise_fact = $row['remise_vnt_fact'];

                    $_montant_a_completer = $_crdt_fact - $_som_verse_fact;


                    if ($mnt_avance <= $_montant_a_completer) {
                        $heure_vnt = date("H:i:s");
                        $query = "INSERT INTO  t_creance_client (
                     	fact_crce_clnt,
                        clnt_crce,
                        ref_paye_crce_clnt,
                        id_reg,
                        mnt_glob_crce_clnt,
                     mnt_paye_crce_clnt,
                     ref_crce_clnt,
                     date_crce_clnt,
                     caissier_crce_clnt,
                     caissier_login_crce,
                     code_caissier_crce) 
                     VALUES(" . $_id_fact . ",
                         " . $id_clt . ",
                         " . $refavance . ",
                         '" . $unikId . "',
                         " . $mntglobale . ",
                          " . $mnt_avance . ", 
                          '" . $ref . "', 
                             CONCAT('$date_reg',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                        if (!$r = $this->mysqli->query($query))
                            throw new Exception($this->mysqli->error . __LINE__);



                        $query = "UPDATE t_facture_vente SET som_verse_crdt=som_verse_crdt + $mnt_avance, date_reg_fact= CONCAT('$date_reg',' ',time(now())) WHERE id_fact =$_id_fact";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                        /* $query = "UPDATE t_client SET credit_en_cours_clt=(credit_en_cours_clt - " . $mnt_avance . ") WHERE id_clt=$id_clt";
                          if (!$r = $this->mysqli->query($query))
                          throw new Exception($this->mysqli->error . __LINE__); */

                        /* $query = " SELECT SUM(mnt_paye_crce_clnt) as mnt FROM t_creance_client WHERE 	fact_crce_clnt=$_id_fact";
                          $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                          $result = $r->fetch_assoc();
                          $mnt = $result['mnt']; */

                        $query = "SELECT f.id_fact,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact
                           FROM 
                           t_facture_vente f
                            WHERE f.id_fact=$_id_fact
                           ORDER BY f.id_fact ASC limit 1";

                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        $result = $r->fetch_assoc();
                        /* $mnt = $result['mnt']; */


                        if (($result['som_verse_crdt']) >= $_crdt_fact) {


                            $query = "UPDATE t_facture_vente SET bl_crdt_regle=1 WHERE id_fact =$_id_fact";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }
                        $mnt_avance = 0;
                    } else {
                        //debut 
                        $query = "INSERT INTO  t_creance_client (
                     	fact_crce_clnt,
                        clnt_crce,
                        ref_paye_crce_clnt,
                        id_reg,
                        mnt_glob_crce_clnt,
                     mnt_paye_crce_clnt,
                     ref_crce_clnt,
                     date_crce_clnt,
                     caissier_crce_clnt,
                     caissier_login_crce,
                     code_caissier_crce) 
                     VALUES(" . $_id_fact . ",
                         " . $id_clt . ",
                             " . $refavance . ",
                             '" . $unikId . "',
                         " . $mntglobale . ",
                          " . $_montant_a_completer . ", 
                               '" . $ref . "', 
                               CONCAT('$date_reg',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                        if (!$r = $this->mysqli->query($query))
                            throw new Exception($this->mysqli->error . __LINE__);



                        $query = "UPDATE t_facture_vente SET som_verse_crdt=som_verse_crdt + $_montant_a_completer WHERE id_fact =$_id_fact";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                        /* $query = "UPDATE t_client SET credit_en_cours_clt=(credit_en_cours_clt - " . $mnt_avance . ") WHERE id_clt=$id_clt";
                          if (!$r = $this->mysqli->query($query))
                          throw new Exception($this->mysqli->error . __LINE__); */

                        $query = " SELECT SUM(mnt_paye_crce_clnt) as mnt FROM t_creance_client WHERE 	fact_crce_clnt=$_id_fact";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        $result = $r->fetch_assoc();
                        $mnt = $result['mnt'];


                        if ($mnt >= ($_crdt_fact - $_remise_fact)) {
                            $query = "UPDATE t_facture_vente SET bl_crdt_regle=1 WHERE id_fact =$_id_fact";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }

                        $mnt_avance -= $_montant_a_completer;
                    }

                    /*                     * *}**** fin du while */
                } else {
                    //print_r("numrows est < 0");
                    $mnt_avance = 0;
                }



                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);
            } catch (Exception $exc) {
                $this->mysqli->rollback();
                $this->mysqli->autocommit(TRUE);
                $response = array("status" => 1,
                    "datas" => "",
                    "message" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } while ($mnt_avance > 0); /* fin du dowhile */


        $response = array("status" => 0,
            "datas" => "",
            "message" => " Reglement avance effectue avec success!");

        $this->response($this->json($response), 200);
    }

    public function paidReglementgGrtClt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appReglements = $_POST;
        $id_clt = intval($appReglements['id_clt']);
        $mnt_avance = doubleval($appReglements['mnt_versement']);

        /* recuperation du montant global de l'avance */
        $mntglobale = $mnt_avance;
        $refavance = time();


        $date_reg = (!empty($appReglements['date_crce_clnt'])) ? isoToMysqldate($appReglements['date_crce_clnt']) : date("Y-m-d");

        $response = array();



        do {

            try {
                $this->mysqli->autocommit(FALSE);

                $condMag = "";
                if ($_SESSION['userMag'] > 0)
                    $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

                $queryp = "SELECT f.id_fact,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact
                           FROM 
                           t_facture_vente f
                            WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=1
                           AND f.clnt_fact=$id_clt
                               AND f.crdt_fact>0
                               AND f.sup_fact=0
                           AND f.bl_crdt_regle=0  $condMag
                           ORDER BY f.id_fact ASC limit 1";


                $rp = $this->mysqli->query($queryp) or die($this->mysqli->error . __LINE__);
                if ($rp->num_rows > 0) {
                    $row = $rp->fetch_assoc();
                    $_id_fact = $row['id_fact'];
                    $_crdt_fact = $row['crdt_fact'];
                    $_som_verse_fact = $row['som_verse_crdt'];
                    $_remise_fact = $row['remise_vnt_fact'];

                    $_montant_a_completer = $_crdt_fact - $_remise_fact - $_som_verse_fact;

                    //print_r("id_fact=".$_id_fact." | credit restant=".$_crdt_fact." | somme verse=".$_som_verse_fact." | remise=".$_remise_fact." | montant_a_completer=".$_montant_a_completer);

                    if ($mnt_avance <= $_montant_a_completer) {
                        //print_r("lavance ne depasse pas le montant_a_completer");
                        //debut 
                        $heure_vnt = date("H:i:s");
                        $query = "INSERT INTO  t_creance_client (
                     	fact_crce_clnt, 
                        clnt_crce,
                        ref_paye_crce_clnt,
                        mnt_glob_crce_clnt,
                     mnt_paye_crce_clnt,
                     date_crce_clnt,
                     caissier_crce_clnt,
                     caissier_login_crce,
                     code_caissier_crce) 
                     VALUES(" . $_id_fact . ",
                         " . $id_clt . ",
                              " . $refavance . ",
                         " . $mntglobale . ",
                          " . $mnt_avance . ", 
                             CONCAT('$date_reg',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                        if (!$r = $this->mysqli->query($query))
                            throw new Exception($this->mysqli->error . __LINE__);



                        $query = "UPDATE t_facture_vente SET som_verse_crdt=som_verse_crdt + $mnt_avance, date_reg_fact= CONCAT('$date_reg',' ',time(now())) WHERE id_fact =$_id_fact";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                        /* $query = "UPDATE t_client SET credit_en_cours_clt=(credit_en_cours_clt - " . $mnt_avance . ") WHERE id_clt=$id_clt";
                          if (!$r = $this->mysqli->query($query))
                          throw new Exception($this->mysqli->error . __LINE__); */

                        /* $query = " SELECT SUM(mnt_paye_crce_clnt) as mnt FROM t_creance_client WHERE 	fact_crce_clnt=$_id_fact";
                          $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                          $result = $r->fetch_assoc();
                          $mnt = $result['mnt']; */

                        $query = "SELECT f.id_fact,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact
                           FROM 
                           t_facture_vente f
                            WHERE f.id_fact=$_id_fact
                           ORDER BY f.id_fact ASC limit 1";

                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        $result = $r->fetch_assoc();
                        /* $mnt = $result['mnt']; */


                        if (($result['som_verse_crdt'] + $_remise_fact) >= $_crdt_fact) {


                            $query = "UPDATE t_facture_vente SET bl_crdt_regle=1 WHERE id_fact =$_id_fact";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }
                        $mnt_avance = 0;
                    } else {
                        //debut 
                        $query = "INSERT INTO  t_creance_client (
                     	fact_crce_clnt,
                        clnt_crce,
                        ref_paye_crce_clnt,
                        mnt_glob_crce_clnt,
                     mnt_paye_crce_clnt,
                     date_crce_clnt,
                     caissier_crce_clnt,
                     caissier_login_crce,
                     code_caissier_crce) 
                     VALUES(" . $_id_fact . ",
                         " . $id_clt . ",
                              " . $refavance . ",
                         " . $mntglobale . ",
                          " . $_montant_a_completer . ", 
                             '" . $date_reg . "',
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                        if (!$r = $this->mysqli->query($query))
                            throw new Exception($this->mysqli->error . __LINE__);



                        $query = "UPDATE t_facture_vente SET som_verse_crdt=som_verse_crdt + $_montant_a_completer WHERE id_fact =$_id_fact";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                        /* $query = "UPDATE t_client SET credit_en_cours_clt=(credit_en_cours_clt - " . $mnt_avance . ") WHERE id_clt=$id_clt";
                          if (!$r = $this->mysqli->query($query))
                          throw new Exception($this->mysqli->error . __LINE__); */

                        $query = " SELECT SUM(mnt_paye_crce_clnt) as mnt FROM t_creance_client WHERE 	fact_crce_clnt=$_id_fact";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        $result = $r->fetch_assoc();
                        $mnt = $result['mnt'];


                        if ($mnt >= ($_crdt_fact - $_remise_fact)) {
                            $query = "UPDATE t_facture_vente SET bl_crdt_regle=1 WHERE id_fact =$_id_fact";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }

                        $mnt_avance -= $_montant_a_completer;
                    }

                    /*                     * *}**** fin du while */
                } else {
                    //print_r("numrows est < 0");
                    $mnt_avance = 0;
                }



                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);
            } catch (Exception $exc) {
                $this->mysqli->rollback();
                $this->mysqli->autocommit(TRUE);
                $response = array("status" => 1,
                    "datas" => "",
                    "message" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } while ($mnt_avance > 0); /* fin du dowhile */


        $response = array("status" => 0,
            "datas" => "",
            "message" => " Versement avance effectue avec success!");

        $this->response($this->json($response), 200);
    }

    public function vucr() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_crce_clnt']);
        try {

            $query = "UPDATE t_creance_client set vu=1 WHERE id_crce_clnt=$id ";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $response = array("status" => 0,
                "datas" => $r,
                "message" => "Reglement Marquer comme vu avec success!!!");
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

    public function tvucr() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        try {

            $query = "UPDATE t_creance_client set vu=1 WHERE vu=0 ";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $response = array("status" => 0,
                "datas" => $r,
                "message" => "Tous les reglements Marquer comme vu avec success!!!");
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
    $app = new reglementController;
    $app->processApp();
}
?>