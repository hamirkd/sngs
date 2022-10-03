<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");
require_once ("api-class/sms.class.php");

class etatController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getEtatCreances() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
       {     
            $condmag = " AND fv.mag_fact=" . $_SESSION['userMag'] . "";
           
            
        }
        $query = "SELECT  
            fv.code_caissier_fact,
            fv.bl_fact_grt,
            fv.login_caissier_fact,
            (fv.crdt_fact-fv.som_verse_crdt) as mnt_crce,
            fv.code_fact,fv.id_fact,
            date(fv.date_fact) as date_fact,
            time(fv.date_fact) as heure_fact,
            cl.id_clt,
            cl.code_clt,
            cl.nom_clt,
            cl.tel_clt,
            datediff(date(now()),date(cl.last_sms)) as duree,
            m.code_mag
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             inner join t_magasin m on fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_crdt=1 AND fv.bl_fact_grt=0 AND fv.bl_crdt_regle=0 AND fv.sup_fact=0
            AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt)>0 $condmag ";

        if (!empty($search['user']))
            $query.=" AND fv.code_caissier_fact='" . $this->esc($search['user']) . "'";

        if (!empty($search['magasin']))
            $query.=" AND fv.mag_fact=" . intval($search['magasin']);


        if (!empty($search['client']))
            $query.=" AND fv.clnt_fact=" . intval($search['client']);


        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(fv.date_fact)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(fv.date_fact) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.= ' ORDER BY fv.date_fact DESC';
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

    public function getEtatGrt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT  
            fv.code_caissier_fact,
            fv.bl_fact_grt,
            fv.login_caissier_fact,
            (fv.crdt_fact-fv.som_verse_crdt) as mnt_crce,
            fv.code_fact,fv.id_fact,
            date(fv.date_fact) as date_fact,
            time(fv.date_fact) as heure_fact,
            cl.id_clt,
            cl.code_clt,
            cl.nom_clt ,
            m.code_mag
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             inner join t_magasin m on fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_crdt=1 AND fv.bl_fact_grt=1 AND fv.bl_crdt_regle=0  AND fv.sup_fact=0
            AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt)>0 $condmag ";

        if (!empty($search['user']))
            $query.=" AND fv.code_caissier_fact='" . $this->esc($search['user']) . "'";

        if (!empty($search['magasin']))
            $query.=" AND fv.mag_fact=" . intval($search['magasin']);


        if (!empty($search['client']))
            $query.=" AND fv.clnt_fact=" . intval($search['client']);


        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(fv.date_fact)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(fv.date_fact) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.="  order by fv.id_fact ASC";


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

    public function getRapCommande() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT  
            fv.code_caissier_fact,
           (fv.crdt_fact) as mnt_crce,
            fv.code_fact,fv.id_fact,
            date(fv.date_encaiss_grt) as date_fact,
            time(fv.date_encaiss_grt) as heure_fact,
            cl.id_clt,
            cl.code_clt,
            cl.nom_clt ,
            m.code_mag
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             inner join t_magasin m on fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_crdt=1 AND fv.bl_fact_grt=1 AND fv.bl_crdt_regle=1 AND fv.crdt_fact>0 $condmag ";

        if (!empty($search['user']))
            $query.=" AND fv.code_caissier_fact='" . $this->esc($search['user']) . "'";

        if (!empty($search['magasin']))
            $query.=" AND fv.mag_fact=" . intval($search['magasin']);


        if (!empty($search['client']))
            $query.=" AND fv.clnt_fact=" . intval($search['client']);


        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(fv.date_encaiss_grt)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(fv.date_encaiss_grt) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.="  order by fv.id_fact ASC";


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

    public function getEtatDettes() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND ap.user_appro in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";


        $query = "SELECT 
            ap.code_user_appro,
            ap.login_appro,
            (ap.mnt_revient_appro-ap.som_verse_dette) as mnt_dette ,
            ap.bon_liv_appro,ap.id_appro,
            date(ap.date_appro) as date_appro,
            time(ap.date_appro) as heure_appro,
            f.id_frns,
            f.code_frns,
            f.nom_frns 
            FROM t_approvisionnement ap  
            INNER JOIN t_fournisseur f ON ap.frns_appro=f.id_frns
            WHERE ap.bl_bon_dette=1 AND ap.bl_dette_regle=0 
			AND ap.mnt_revient_appro>0
			AND (ap.mnt_revient_appro-ap.som_verse_dette)>0$condmag ";

        if (!empty($search['user']))
            $query.=" AND ap.code_user_appro='" . $this->esc($search['user']) . "'";


        if (!empty($search['fournisseur']))
            $query.=" AND f.id_frns=" . intval($search['fournisseur']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(ap.date_appro)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(ap.date_appro) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query.=" ORDER BY ap.date_appro ASC, f.nom_frns ASC";


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

    public function getValStock() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }



        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND vvs.id_mag=" . intval($_SESSION['userMag']);


        $query = "SELECT vvs.nom_mag,vvs.val_max,vvs.val_min 
            FROM v_valeur_stock vvs
             WHERE 1=1 $condmag ";

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
    
    
    
    
     public function getValStockAchat() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }



        $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND vvs.id_mag=" . intval($_SESSION['userMag']);


        $query = "SELECT vvs.nom_mag,vvs.val_max,vvs.val_min 
            FROM v_valeur_stock_achat vvs
             WHERE 1=1 $condmag ";

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
    
    
    

    public function getEtatCaisse() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $result = array();
        $search = $_POST;
        try {
            $ccpt = "";
            $crse = "";
            $crc = "";
            $grt = "";
            $crf = "";
            $cdp = "";
            $cvrs = "";
            $prov = "";

            if ($_SESSION['userMag'] > 0) {
                $ccpt = " AND f.mag_fact=" . intval($_SESSION['userMag']);
                $crse = " AND f.mag_fact=" . intval($_SESSION['userMag']);
                $crc = " AND cr.caissier_crce_clnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $grt = " AND f.caissier_fact IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $crf = " AND df.caissier_dette_frns IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $cdp = " AND d.user_dep IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $prov = " AND cais.user_cais IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $cvrs = " AND vrs.caissier_vrsmnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
            } else {
                $ccpt = "";
                $crse = "";
                $crc = "";
                $grt = "";
                $crf = "";
                $cdp = "";
                $prov = "";
                $cvrs = "";
                if (!empty($search['magasin'])) {
                    $ccpt = " AND f.mag_fact=" . intval($search['magasin']);
                    $crse = " AND f.mag_fact=" . intval($search['magasin']);
                    $crc = " AND cr.caissier_crce_clnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $grt = " AND f.caissier_fact IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $crf = " AND df.caissier_dette_frns IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $cdp = " AND d.user_dep IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $prov = " AND cais.user_cais IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $cvrs = " AND vrs.caissier_vrsmnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                }
            }

            $query = "SELECT sum(f.crdt_fact) as comptant,sum(f.tva_fact+f.bic_fact) as taxe
                from t_facture_vente f
                           WHERE f.bl_fact_crdt=0 $ccpt
                           AND date(f.date_fact)=date(now()) AND f.sup_fact=0";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['comptant'] = doubleval($res['comptant']);
            }

            $query = "SELECT SUM(v.marge_vnt) as margecpt
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_crdt=0 $ccpt
                           AND date(v.date_vnt)=date(now()) AND f.sup_fact=0";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['margecpt'] = doubleval($res['margecpt']);
            }


            $query = "SELECT SUM(v.marge_vnt) as margecrdt
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=0 $ccpt
                           AND date(v.date_vnt)=date(now()) AND f.sup_fact=0";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['margecrdt'] = doubleval($res['margecrdt']);
            }


            $query = "SELECT sum(f.remise_vnt_fact) as remise
                           FROM 
                            t_facture_vente f   
                           WHERE  date(f.date_fact)=date(now()) AND f.sup_fact=0 $crse";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['remise'] = intval($res['remise']);
            }

            $query = "SELECT sum(cr.mnt_paye_crce_clnt) as creance
                           FROM 
                           t_creance_client cr
                           inner join t_facture_vente f ON cr.fact_crce_clnt=f.id_fact
                            WHERE f.bl_fact_grt=0 AND f.bl_fact_crdt=1 
                            AND date(cr.date_crce_clnt)=date(now()) $crc";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['creance'] = intval($res['creance']);
            }


            $query = "SELECT sum(f.crdt_fact) as grtencaiss
                           FROM t_facture_vente f 
                           WHERE f.bl_fact_grt=1 AND f.bl_encaiss_grt=1 
                           AND f.bl_crdt_regle=1 
                           AND date(f.date_encaiss_grt)=date(now()) AND f.sup_fact=0 $grt";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['grtencaiss'] = intval($res['grtencaiss']);
            }

            $query = "SELECT SUM(v.marge_vnt) as margegrtencaiss
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_grt=1 AND f.bl_encaiss_grt=1 
                           AND f.bl_crdt_regle=1 $ccpt
                           AND date(f.date_encaiss_grt)=date(now()) AND f.sup_fact=0";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['margegrtencaiss'] = doubleval($res['margegrtencaiss']);
            }


            $query = "SELECT sum(df.mnt_paye_dette_frns) as dette
                           FROM 
                           t_dette_fournisseur df
                            WHERE date(df.date_dette_frns)=date(now()) $crf";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['dette'] = intval($res['dette']);
            }

            $query = "SELECT IFNULL(sum(d.mnt_dep),0) as depense
                           FROM 
                           t_depense d
                            WHERE date(d.date_dep)=date(now()) $cdp";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['depense'] = intval($res['depense']);
            }

            $query = "SELECT IFNULL(sum(cais.mnt_cais),0) as provision
                           FROM 
                           t_caisse cais
                            WHERE date(cais.date_cais)=date(now()) $prov";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['provision'] = intval($res['provision']);
            }


            $query = "SELECT IFNULL(sum(vrs.mnt_vrsmnt),0) as versement
                           FROM 
                           t_versement vrs
                            WHERE date(vrs.date_vrsmnt)=date(now()) $cvrs";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['versement'] = intval($res['versement']);
            }


            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "msg" => $exc->getMessage());

            $this->response($this->json($response), 200);
        }
    }

    public function getExtEtatCaisse() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $result = array();

        try {
            $search = $_POST;

            $ccpt = "";
            $crse = "";
            $crc = "";
            $grt = "";
            $crf = "";
            $cdp = "";
            $cvrs = "";
            $prov = "";

            if ($_SESSION['userMag'] > 0) {
                $ccpt = " AND f.mag_fact=" . intval($_SESSION['userMag']);
                $crse = " AND f.mag_fact=" . intval($_SESSION['userMag']);
                $crc = " AND cr.caissier_crce_clnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $grt = " AND f.caissier_fact IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $crf = " AND df.caissier_dette_frns IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $cdp = " AND d.user_dep IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $prov = " AND cais.user_cais IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
                $cvrs = " AND vrs.caissier_vrsmnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
            } else {

                $ccpt = "";
                $crse = "";
                $crc = "";
                $grt = "";
                $crf = "";
                $cdp = "";
                $cvrs = "";
                $prov = "";

                if (!empty($search['magasin'])) {
                    $ccpt = " AND f.mag_fact=" . intval($search['magasin']);
                    $crse = " AND f.mag_fact=" . intval($search['magasin']);
                    $crc = " AND cr.caissier_crce_clnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $grt = " AND f.caissier_fact IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $crf = " AND df.caissier_dette_frns IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $cdp = " AND d.user_dep IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $prov = " AND cais.user_cais IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                    $cvrs = " AND vrs.caissier_vrsmnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($search['magasin']) . ")";
                }
            }

            if (!empty($search['date_deb']) && empty($search['date_fin']))
                $dateq = "='" . isoToMysqldate($search['date_deb']) . "'";

            if (!empty($search['date_fin']) && empty($search['date_deb']))
                $dateq = " between '2010-01-01' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";
            
            if (!empty($search['date_fin']) && !empty($search['date_deb']))
                $dateq = " between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

            $query = "SELECT sum(f.crdt_fact) as comptant,sum(f.tva_fact+f.bic_fact) as taxe
                           FROM t_facture_vente f
                           WHERE f.bl_fact_crdt=0 AND f.sup_fact=0 $ccpt
                           AND date(f.date_fact) $dateq";


            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['comptant'] = doubleval($res['comptant']);
            }

            $query = "SELECT SUM(v.marge_vnt) as margecpt
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_crdt=0 AND f.sup_fact=0 $ccpt
                           AND date(v.date_vnt) $dateq";


            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['margecpt'] = doubleval($res['margecpt']);
            }


            $query = "SELECT SUM(v.marge_vnt) as margecrdt
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.sup_fact=0 $ccpt
                           AND date(v.date_vnt) $dateq";


            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['margecrdt'] = doubleval($res['margecrdt']);
            }


            $query = "SELECT sum(f.remise_vnt_fact) as remise
                           FROM 
                            t_facture_vente f   
                           WHERE  date(f.date_fact)  AND f.sup_fact=0$dateq $crse";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['remise'] = intval($res['remise']);
            }

            $query = "SELECT sum(cr.mnt_paye_crce_clnt) as creance
                           FROM 
                           t_creance_client cr
                           inner join t_facture_vente f ON cr.fact_crce_clnt=f.id_fact
                            WHERE f.bl_fact_grt=0 AND f.bl_fact_crdt=1 AND date(cr.date_crce_clnt) $dateq $crc";



            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['creance'] = intval($res['creance']);
            }


            $query = "SELECT sum(f.crdt_fact) as grtencaiss
                           FROM t_facture_vente f 
                           WHERE f.bl_fact_grt=1 AND f.bl_encaiss_grt=1 
                           AND f.bl_crdt_regle=1 
                           AND date(f.date_encaiss_grt) AND f.sup_fact=0 $dateq $grt";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['grtencaiss'] = intval($res['grtencaiss']);
            }

            $query = "SELECT SUM(v.marge_vnt) as margegrtencaiss
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_grt=1 AND f.bl_encaiss_grt=1 
                           AND f.bl_crdt_regle=1 $ccpt
                           AND date(f.date_encaiss_grt) AND f.sup_fact=0 $dateq";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['margegrtencaiss'] = doubleval($res['margegrtencaiss']);
            }


            $query = "SELECT sum(df.mnt_paye_dette_frns) as dette
                           FROM 
                           t_dette_fournisseur df
                            WHERE date(df.date_dette_frns) $dateq $crf";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['dette'] = intval($res['dette']);
            }

            $query = "SELECT IFNULL(sum(d.mnt_dep),0) as depense
                           FROM 
                           t_depense d
                            WHERE date(d.date_dep) $dateq $cdp";


            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['depense'] = intval($res['depense']);
            }

            $query = "SELECT IFNULL(sum(cais.mnt_cais),0) as provision
                           FROM 
                           t_caisse cais
                            WHERE date(cais.date_cais) $dateq $prov";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['provision'] = intval($res['provision']);
            }



            $query = "SELECT IFNULL(sum(vrs.mnt_vrsmnt),0) as versement
                           FROM 
                           t_versement vrs
                            WHERE date(vrs.date_vrsmnt) $dateq $cvrs";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['versement'] = intval($res['versement']);
            }


            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "msg" => $exc->getMessage());

            $this->response($this->json($response), 200);
        }
    }

    public function getStats() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $limit = 10;
        $limita = 50;
        $limitb = 100;

        $search = $_POST;
        $year = $search['year'];
        $echelle = 1;


        $topventes = array();
        $bottomventes = array();
        $trimestreventes = array();
        $semestreventes = array();
        $anneeventes = array();
        $neverventes = array();
        $rentableventes = array();
        $yearcomptants = array();
        $yearventes = array();
        $yeardepenses = array();
        $yearcreances = array();
        $yeardettes = array();
        $yearmarges = array();

        /* les 50 produits les plus vendus */
        $query = " select v.article_vnt,
a.nom_art,
count(v.id_vnt) as nbVente,v.date_vnt
from t_vente v 
inner join t_article a on v.article_vnt=a.id_art
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(v.Date_vnt)='$year'
AND f.sup_fact=0
group by v.article_vnt order by nbVente DESC limit $limita";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "");

        while ($row = $r->fetch_assoc()) {
            $topventes[] = $row;
        }


        /* les 50 produits le plus rentable */
        $query = " select v.article_vnt,
a.nom_art,
SUM(v.qte_vnt * pu_theo_vnt) as mntVente 
from t_vente v 
inner join t_article a on v.article_vnt=a.id_art
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(v.Date_vnt)='$year'
AND f.sup_fact=0
group by v.article_vnt order by mntVente DESC limit $limita";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "");

        while ($row = $r->fetch_assoc()) {
            $rentableventes[] = $row;
        }



        /* les 100 produits les moins vendus */
        $query = " select v.article_vnt,
a.nom_art,
count(v.id_vnt) as nbVente,v.date_vnt
from t_vente v 
inner join t_article a on v.article_vnt=a.id_art
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(v.Date_vnt)='$year'
AND f.sup_fact=0
group by v.article_vnt order by nbVente ASC limit $limitb";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $bottomventes[] = $row;
        }


        /* les produits sorties il ya plus de 03 mois */
        $query = " select v.article_vnt,
a.nom_art,max(v.date_vnt) as max_date,
count(v.id_vnt) as nbVente
from t_vente v 
inner join t_article a on v.article_vnt=a.id_art
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where TIMESTAMPDIFF(MONTH, v.date_vnt,now())>=3
 AND f.sup_fact=0
group by v.article_vnt
order by a.nom_art";



        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "");

        while ($row = $r->fetch_assoc()) {
            $trimestreventes[] = $row;
        }

        /* les produits sorties il ya plus de 06 mois */
        $query = "select v.article_vnt,
a.nom_art,max(v.date_vnt) as max_date,
count(v.id_vnt) as nbVente
from t_vente v 
inner join t_article a on v.article_vnt=a.id_art
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where TIMESTAMPDIFF(MONTH, v.date_vnt,now())>=6
 AND f.sup_fact=0
group by v.article_vnt
order by a.nom_art";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "");

        while ($row = $r->fetch_assoc()) {
            $semestreventes[] = $row;
        }

        /* les produits sorties il ya plus de 01 an */
        $query = " select v.article_vnt,
a.nom_art,max(v.date_vnt) as max_date,
count(v.id_vnt) as nbVente
from t_vente v 
inner join t_article a on v.article_vnt=a.id_art
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where TIMESTAMPDIFF(MONTH, v.date_vnt,now())>=12
 AND f.sup_fact=0
group by v.article_vnt
order by a.nom_art";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "");

        while ($row = $r->fetch_assoc()) {
            $anneeventes[] = $row;
        }


        /* les produits jamais vendus */
        $query = " select a.nom_art FROM t_article a 
            where a.id_art NOT IN 
            (select v.article_vnt from t_vente v)";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "");

        while ($row = $r->fetch_assoc()) {
            $neverventes[] = $row;
        }

        /* les ventes de l'annee par mois */
        $query = " select $year as annee,
 'Janvier' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=1";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "mitte");

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }


        $query = " select $year as annee,
 'Fevrier' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=2";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }


        $query = " select $year as annee,
 'Mars' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=3";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }


        $query = " select $year as annee,
 'Avril' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=4";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }

        $query = " select $year as annee,
 'Mai' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=5";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }

        $query = " select $year as annee,
 'Juin' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=6";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }

        $query = " select $year as annee,
 'Juillet' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=7";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }

        $query = " select $year as annee,
 'Aout' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=8";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }

        $query = " select $year as annee,
 'Septembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=9";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }

        $query = " select $year as annee,
 'Octobre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=10";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }

        $query = " select $year as annee,
 'Novembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=11";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }

        $query = " select $year as annee,
 'Decembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as vente
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=12";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearventes[] = $row;
        }



        /* les comptants de l'annee par mois */
        $query = " select $year as annee,
 'Janvier' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=1";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "");

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }


        $query = " select $year as annee,
 'Fevrier' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=2";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }


        $query = " select $year as annee,
 'Mars' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=3";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }


        $query = " select $year as annee,
 'Avril' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=4";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }

        $query = " select $year as annee,
 'Mai' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=5";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }

        $query = " select $year as annee,
 'Juin' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=6";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }

        $query = " select $year as annee,
 'Juillet' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=7";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }

        $query = " select $year as annee,
 'Aout' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=8";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }

        $query = " select $year as annee,
 'Septembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=9";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }

        $query = " select $year as annee,
 'Octobre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=10";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }

        $query = " select $year as annee,
 'Novembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=11";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }

        $query = " select $year as annee,
 'Decembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as comptant
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0
AND bl_fact_crdt=0
AND MONTH(date_fact)=12";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcomptants[] = $row;
        }


        /* les creances de l'annee par mois */

        $query = " select $year as annee,
 'Janvier' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=1";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }

        $query = " select $year as annee,
 'Fevrier' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=2";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }


        $query = " select $year as annee,
 'Mars' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=3";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }


        $query = " select $year as annee,
 'Avril' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=4";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }


        $query = " select $year as annee,
 'Mai' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=5";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }


        $query = " select $year as annee,
 'Juin' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=6";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }


        $query = " select $year as annee,
 'Juillet' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND  crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=7";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }


        $query = " select $year as annee,
 'Aout' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=8";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }


        $query = " select $year as annee,
 'Septembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=9";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }

        $query = " select $year as annee,
 'Octobre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=10";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }

        $query = " select $year as annee,
 'Septembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=11";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }

        $query = " select $year as annee,
 'Decembre' as mois,
ifnull(sum(crdt_fact)/$echelle,0) as creance
from t_facture_vente
where YEAR(date_fact)='$year'
AND sup_fact=0 AND bl_fact_crdt=1 AND bl_crdt_regle=0
AND crdt_fact>0 AND (crdt_fact-som_verse_crdt)>0
AND MONTH(date_fact)=12";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearcreances[] = $row;
        }

        /* les dettes de l'annee par mois */


        $query = " select $year as annee,
 'Janvier' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=1";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }

        $query = " select $year as annee,
 'Fevrier' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=2";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }


        $query = " select $year as annee,
 'Mars' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=3";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }



        $query = " select $year as annee,
 'Avril' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=4";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }



        $query = " select $year as annee,
 'Mai' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=5";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }


        $query = " select $year as annee,
 'Juin' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=6";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }



        $query = " select $year as annee,
 'Juillet' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=7";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }



        $query = " select $year as annee,
 'Aout' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=8";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }



        $query = " select $year as annee,
 'Septembre' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=9";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }



        $query = " select $year as annee,
 'Octobre' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=10";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }



        $query = " select $year as annee,
 'Novembre' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=11";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }



        $query = " select $year as annee,
 'Decembre' as mois,
ifnull(sum(mnt_revient_appro)/$echelle,0) as dette
from t_approvisionnement
where YEAR(date_appro)='$year'
AND bl_bon_dette=1 AND bl_dette_regle=0
AND mnt_revient_appro>0 AND (mnt_revient_appro-som_verse_dette)>0
AND MONTH(date_appro)=12";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardettes[] = $row;
        }

        /* les depenses de l'annee par mois */

        $query = " select $year as annee,
 'Janvier' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=1";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }

        $query = " select $year as annee,
 'Fevrier' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=2";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }

        $query = " select $year as annee,
 'Mars' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=3";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }


        $query = " select $year as annee,
 'Avril' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=4";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }


        $query = " select $year as annee,
 'Mai' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=5";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }


        $query = " select $year as annee,
 'Juin' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=6";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }


        $query = " select $year as annee,
 'Juillet' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=7";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }


        $query = " select $year as annee,
 'Aout' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=8";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }

        $query = " select $year as annee,
 'Septembre' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=9";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }


        $query = " select $year as annee,
 'Octobre' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=10";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }


        $query = " select $year as annee,
 'Novembre' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=11";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }


        $query = " select $year as annee,
 'Decembre' as mois,
ifnull(sum(mnt_dep)/$echelle,0) as depense
from t_depense
where YEAR(date_dep)='$year'
AND MONTH(date_dep)=12";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yeardepenses[] = $row;
        }



        /* les benefices de l'annee par mois */

        $query = " select $year as annee,
 'Janvier' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=1";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }


        $query = " select $year as annee,
 'Fevrier' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=2";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }


        $query = " select $year as annee,
 'Mars' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=3";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Avril' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=4";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Mai' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=5";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Juin' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=6";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Juillet' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=7";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Aout' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=8";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Septembre' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=9";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Octobre' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=10";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Novembre' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=11";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }

        $query = " select $year as annee,
 'Decembre' as mois,
ifnull(sum(v.marge_vnt)/$echelle,0) as bene
from t_vente v 
inner join t_facture_vente f on v.facture_vnt=f.id_fact
where YEAR(date_fact)='$year'
AND sup_fact=0
AND MONTH(date_fact)=12";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        while ($row = $r->fetch_assoc()) {
            $yearmarges[] = $row;
        }



        for ($i = 0; $i < count($yearventes) - 1; $i++)
            $yearventes[$i]['vente'] = round($yearventes[$i]['vente'], 0);

        for ($i = 0; $i < count($yearcomptants) - 1; $i++)
            $yearcomptants[$i]['comptant'] = round($yearcomptants[$i]['comptant'], 0);

        for ($i = 0; $i < count($yearcreances) - 1; $i++)
            $yearcreances[$i]['creance'] = round($yearcreances[$i]['creance'], 0);

        for ($i = 0; $i < count($yeardettes) - 1; $i++)
            $yeardettes[$i]['dette'] = round($yeardettes[$i]['dette'], 0);

        for ($i = 0; $i < count($yeardepenses) - 1; $i++)
            $yeardepenses[$i]['depense'] = round($yeardepenses[$i]['depense'], 0);

        for ($i = 0; $i < count($yearmarges) - 1; $i++)
            $yearmarges[$i]['bene'] = round($yearmarges[$i]['bene'], 0);


        $result['stvnt'] = $yearventes;
        $result['stcpt'] = $yearcomptants;
        $result['stcrce'] = $yearcreances;
        $result['stdet'] = $yeardettes;
        $result['stdep'] = $yeardepenses;
        $result['stben'] = $yearmarges;
        $result['tpvnt'] = $topventes;
        $result['btvnt'] = $bottomventes;
        $result['trivnt'] = $trimestreventes;
        $result['semvnt'] = $semestreventes;
        $result['annvnt'] = $anneeventes;
        $result['nevvnt'] = $neverventes;
        $result['rentvnt'] = $rentableventes;

        $response = array("status" => 0,
            "datas" => $result,
            "msg" => "");
        $this->response($this->json($response), 200);
    }

    public function recover() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $customerid = intval($this->_request['clt']);
        $customerNumber = $this->_request['cn'];
       
         

        //user sms
        $user = $_SESSION['usersms'];
        $password = $_SESSION['passwordsms'];
                $message = "Bonjour!  Votre compte No ".$this->proprietaire."/". $customerid ." Possede " . $this->getCreancesOfCustomer($customerid);
        $message .= " F XOF comme dette chez nous. vous etes prie de bien vouloir respecter votre engagement a solder votre dette le plus tot possible. Nous vous remercions par avance de votre franche collaboration..";

        $smsObject = new Sender($user, $password, $message, $customerNumber);
        $sok = $smsObject->Submit();

        $response = array();

        if (!empty($customerid)) {
            try {
 
                $rek_update = "UPDATE t_client SET last_sms=now() WHERE id_clt=" . intval($customerid);
                $r = $this->mysqli->query($rek_update) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $message . " -- " . $sok,
                    "msg" => "Message envoye avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "msg" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204);
    }

    public function getCreancesOfCustomer($customerId) {

        $query = "SELECT  
           SUM(fv.crdt_fact-fv.som_verse_crdt) as mnt_crce 
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             inner join t_magasin m on fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_crdt=1 AND fv.bl_fact_grt=0 AND fv.bl_crdt_regle=0 AND fv.sup_fact=0
            AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt)>0 AND fv.clnt_fact=$customerId limit 1";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $row = $r->fetch_assoc();
        return doubleval($row['mnt_crce']);
    }

    public function getCustomerInfos($customerId) {

        $query = "SELECT  
            * from t_client 
            WHERE id_clt=$customerId limit 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $row = $r->fetch_assoc();
        return doubleval($row);
    }

    public function getEtatFicheArt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $result = array();
        $qtestk = 0;
        $search = $_POST;

        $blselbtk = 0;


        try {

            if ($_SESSION['userMag'] > 0) {/* un gars donn� */

                if (!empty($search['date_deb']) && empty($search['date_fin'])) {
                    $dated = isoToMysqldate($search['date_deb']);

                    $qteappro = intval($this->getApproOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                    $qtetransfget = intval($this->getTransGetfOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                    $qtetransfset = intval($this->getTransSetfOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                    $qtevente = intval($this->getVenteOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                    $qtesortie = intval($this->getSortieOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                    $qtedef = intval($this->getDeffOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));

                    $result[] = array(
                        'periode' => "Date du " . date('d/m/Y', strtotime($dated)),
                        'qteappro' => intval($this->getApproOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                        'qteapprob' => intval($this->getApproOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                        'qtetransfget' => intval($this->getTransGetfOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                        'qtetransfgetb' => intval($this->getTransGetfOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                        'qtetransfset' => intval($this->getTransSetfOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                        'qtetransfsetb' => intval($this->getTransSetfOfArticleFromTo($search['article'], $dated, '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                        'qtevente' => intval($this->getVenteOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                        'qteventeb' => intval($this->getVenteOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                        'qtesortie' => intval($this->getSortieOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                        'qtesortieb' => intval($this->getSortieOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                        'qtedef' => intval($this->getDeffOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                        'qtedefb' => intval($this->getDeffOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag']))
                    );


                    $qtestk = intval($this->getQuantiteOfAritlceOfMagasin($search['article'], $_SESSION['userMag']));
                }
                if (!empty($search['date_deb']) && !empty($search['date_fin'])) {
                    $dated = isoToMysqldate($search['date_deb']);
                    $datef = isoToMysqldate($search['date_fin']);

                    while ($dated <= $datef) {

                        $qteappro = intval($this->getApproOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                        $qtetransfget = intval($this->getTransGetfOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                        $qtetransfset = intval($this->getTransSetfOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                        $qtevente = intval($this->getVenteOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                        $qtesortie = intval($this->getSortieOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));
                        $qtedef = intval($this->getDeffOfArticleFrom($search['article'], $dated, $_SESSION['userMag']));

                        if ($qteappro > 0 || $qtetransfget > 0 || $qtetransfset > 0 || $qtevente > 0 || $qtesortie > 0 || $qtedef > 0)
                            $result[] = array(
                                'periode' => "Date du " . date('d/m/Y', strtotime($dated)),
                                'qteappro' => intval($this->getApproOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                                'qteapprob' => intval($this->getApproOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                                'qtetransfget' => intval($this->getTransGetfOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                                'qtetransfgetb' => intval($this->getTransGetfOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                                'qtetransfset' => intval($this->getTransSetfOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                                'qtetransfsetb' => intval($this->getTransSetfOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                                'qtevente' => intval($this->getVenteOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                                'qteventeb' => intval($this->getVenteOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                                'qtesortie' => intval($this->getSortieOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                                'qtesortieb' => intval($this->getSortieOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag'])),
                                'qtedef' => intval($this->getDeffOfArticleFrom($search['article'], $dated, $_SESSION['userMag'])),
                                'qtedefb' => intval($this->getDeffOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $_SESSION['userMag']))
                            );

                        $dated = date('Y-m-d', strtotime($dated . ' + 1 days'));
                    }
                    $qtestk = intval($this->getQuantiteOfAritlceOfMagasin($search['article'], $_SESSION['userMag']));
                }
            } else {/* gerant ayant choisi une boutique */
                if (!empty($search['magasin'])) {

                    if (!empty($search['date_deb']) && empty($search['date_fin'])) {

                        $dated = isoToMysqldate($search['date_deb']);

                        $qteappro = intval($this->getApproOfArticleFrom($search['article'], $dated, $search['magasin']));
                        $qtetransfget = intval($this->getTransGetfOfArticleFrom($search['article'], $dated, $search['magasin']));
                        $qtetransfset = intval($this->getTransSetfOfArticleFrom($search['article'], $dated, $search['magasin']));
                        $qtevente = intval($this->getVenteOfArticleFrom($search['article'], $dated, $search['magasin']));
                        $qtesortie = intval($this->getSortieOfArticleFrom($search['article'], $dated, $search['magasin']));
                        $qtedef = intval($this->getDeffOfArticleFrom($search['article'], $dated, $search['magasin']));

                        $result[] = array(
                            'periode' => "Date du " . date('d/m/Y', strtotime($dated)),
                            'qteappro' => intval($this->getApproOfArticleFrom($search['article'], $dated, $search['magasin'])),
                            'qteapprob' => intval($this->getApproOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                            'qtetransfget' => intval($this->getTransGetfOfArticleFrom($search['article'], $dated, $search['magasin'])),
                            'qtetransfgetb' => intval($this->getTransGetfOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                            'qtetransfset' => intval($this->getTransSetfOfArticleFrom($search['article'], $dated, $search['magasin'])),
                            'qtetransfsetb' => intval($this->getTransSetfOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                            'qtevente' => intval($this->getVenteOfArticleFrom($search['article'], $dated, $search['magasin'])),
                            'qteventeb' => intval($this->getVenteOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                            'qtesortie' => intval($this->getSortieOfArticleFrom($search['article'], $dated, $search['magasin'])),
                            'qtesortieb' => intval($this->getSortieOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                            'qtedef' => intval($this->getDeffOfArticleFrom($search['article'], $dated, $search['magasin'])),
                            'qtedefb' => intval($this->getDeffOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin']))
                        );


                        $qtestk = intval($this->getQuantiteOfAritlceOfMagasin($search['article'], $search['magasin']));
                    }
                    if (!empty($search['date_deb']) && !empty($search['date_fin'])) {

                        $dated = isoToMysqldate($search['date_deb']);
                        $datef = isoToMysqldate($search['date_fin']);

                        while ($dated <= $datef) {

                            $qteappro = intval($this->getApproOfArticleFrom($search['article'], $dated, $search['magasin']));
                            $qtetransfget = intval($this->getTransGetfOfArticleFrom($search['article'], $dated, $search['magasin']));
                            $qtetransfset = intval($this->getTransSetfOfArticleFrom($search['article'], $dated, $search['magasin']));
                            $qtevente = intval($this->getVenteOfArticleFrom($search['article'], $dated, $search['magasin']));
                            $qtesortie = intval($this->getSortieOfArticleFrom($search['article'], $dated, $search['magasin']));
                            $qtedef = intval($this->getDeffOfArticleFrom($search['article'], $dated, $search['magasin']));

                            if ($qteappro > 0 || $qtetransfget > 0 || $qtetransfset > 0 || $qtevente > 0 || $qtesortie > 0 || $qtedef > 0)
                                $result[] = array(
                                    'periode' => "Date du " . date('d/m/Y', strtotime($dated)),
                                    'qteappro' => intval($this->getApproOfArticleFrom($search['article'], $dated, $search['magasin'])),
                                    'qteapprob' => intval($this->getApproOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                                    'qtetransfget' => intval($this->getTransGetfOfArticleFrom($search['article'], $dated, $search['magasin'])),
                                    'qtetransfgetb' => intval($this->getTransGetfOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                                    'qtetransfset' => intval($this->getTransSetfOfArticleFrom($search['article'], $dated, $search['magasin'])),
                                    'qtetransfsetb' => intval($this->getTransSetfOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                                    'qtevente' => intval($this->getVenteOfArticleFrom($search['article'], $dated, $search['magasin'])),
                                    'qteventeb' => intval($this->getVenteOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                                    'qtesortie' => intval($this->getSortieOfArticleFrom($search['article'], $dated, $search['magasin'])),
                                    'qtesortieb' => intval($this->getSortieOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin'])),
                                    'qtedef' => intval($this->getDeffOfArticleFrom($search['article'], $dated, $search['magasin'])),
                                    'qtedefb' => intval($this->getDeffOfArticleFromTo($search['article'], '2000-01-01', date('Y-m-d', strtotime($dated . ' - 1 days')), $search['magasin']))
                                );

                            $dated = date('Y-m-d', strtotime($dated . ' + 1 days'));
                        }
                        $qtestk = intval($this->getQuantiteOfAritlceOfMagasin($search['article'], $search['magasin']));
                    } else {/* gerant sur tous les points de vente */
                        $blselbtk = 1;
                    }
                }
            }

            $response = array("status" => 0,
                "datas" => array("details" => $result, "qtestk" => $qtestk, "r" => $blselbtk),
                "msg" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
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
    $app = new etatController;
    $app->processApp();
}
?>