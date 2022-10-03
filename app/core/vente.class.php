<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class venteController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function etatVente() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $query = "SELECT date(Date_vnt) as Date_vnt,time(Date_vnt) as heure_vnt,code_fact,bl_fact_grt,bl_fact_crdt,sup_fact,date_fact,remise_vnt_fact,crdt_fact,som_verse_crdt,code_caissier_fact,code_clt,code_art,nom_art,nom_mag,code_mag,nom_clt,Qte_vnt,pu_theo_vnt,mnt_theo_vnt,marge_vnt FROM v_etat_ventes  WHERE 1=1";
        if ($_SESSION['userMag'] > 0)
            $query = "SELECT date(Date_vnt) as Date_vnt,time(Date_vnt) as heure_vnt,code_fact,bl_fact_grt,bl_fact_crdt,sup_fact,date_fact,remise_vnt_fact,crdt_fact,som_verse_crdt,code_caissier_fact,code_clt,code_art,nom_art,nom_mag,code_mag,nom_clt,Qte_vnt,pu_theo_vnt,mnt_theo_vnt FROM v_etat_ventes WHERE id_mag=" . intval($_SESSION['userMag']);

        if (!empty($search['magasin']))
            $query.=" AND id_mag=" . intval($search['magasin']);

        if (!empty($search['user']))
            $query.=" AND code_caissier_fact='" . $this->esc($search['user']) . "'";

        if (!empty($search['article']))
            $query.=" AND id_art=" . intval($search['article']);

        if (!empty($search['categorie']))
            $query.=" AND id_cat=" . intval($search['categorie']);

        if (!empty($search['client']))
            $query.=" AND id_clt=" . intval($search['client']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND bl_tva=" . intval($search['tx']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(date_vnt)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(date_vnt) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        if (isset($search['bc']) && $search['bc'] != "" && $search['bc'] != 2)
            $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['bc']);

        if (isset($search['bc']) && $search['bc'] != "" && $search['bc'] == 2)
            $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

        $query.=" ORDER BY Date_vnt DESC,id_vnt DESC  ";

        // $file = fopen("fichier.txt", "a");
        //     fwrite($file,$query);
        //     fclose($file);
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "message" => "dao");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "dao");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function queryEtatVente() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $qc = "";
        $q = $_GET['q'];
        if (strtolower($q) == "annulee")
            $qc = " OR sup_fact=1";

        if (strtolower($q) == "remise")
            $qc = " OR remise_vnt_fact>0";

        $query = "SELECT date(Date_vnt) as Date_vnt,
            time(Date_vnt) as heure_vnt,
            code_fact,bl_fact_grt,
            bl_fact_crdt,
            crdt_fact,remise_vnt_fact,date_fact,sup_fact,
            code_caissier_fact,
            code_clt,code_art,
            nom_art,nom_mag,
            code_mag,nom_clt,
            Qte_vnt,
            pu_theo_vnt,
            mnt_theo_vnt,
            marge_vnt 
            FROM v_etat_ventes  WHERE nom_cat LIKE '%$q%' $qc OR nom_art LIKE '%$q%' OR code_fact LIKE '%$q%' ";

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT date(Date_vnt) as Date_vnt,
                time(Date_vnt) as heure_vnt,
                code_fact,
                bl_fact_grt,
                bl_fact_crdt,
                crdt_fact,remise_vnt_fact,date_fact,sup_fact, 
                code_caissier_fact,
                code_clt,
                code_art,
                nom_art,
                nom_mag,
                code_mag,
                nom_clt,
                Qte_vnt,
                pu_theo_vnt,
                mnt_theo_vnt 
                FROM v_etat_ventes WHERE (nom_cat LIKE '%$q%' $qc OR nom_art LIKE '%$q%' OR code_fact LIKE '%$q%' ) AND id_mag=" . intval($_SESSION['userMag']);;

        $query.=" ORDER BY id_vnt DESC  ";


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

    public function getVentes() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $condMag = "";
        $sliceT = "";
        $pst = $_POST;
        $slice = $pst['sl'];
        $sliceg = $pst['slg'];

        if ($slice > 0)
            $sliceT = " AND v.id_vnt > $slice";

        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $query = "SELECT a.nom_art,f.code_fact,
                         m.nom_mag,m.code_mag,
                         v.id_vnt,v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,v.marge_vnt,
                         COALESCE(c.nom_clt,'-') as clt,
                         time(f.date_fact) as heure_fact,
                         f.login_caissier_fact,
                         f.sup_fact,
                         f.date_sup_fact,
                         f.code_caissier_fact,
                         f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
                         f.bl_fact_crdt,f.bl_fact_grt,f.bl_encaiss_grt
                         FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                         INNER JOIN t_magasin m on f.mag_fact=m.id_mag
                         LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                         WHERE DATE(v.date_vnt)='" . date('Y-m-d') . "' $condMag $sliceT ORDER BY v.id_vnt DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['TYPE'] = "VNT";
                $result[] = $row;
            }

            $ej = $this->getComptantJour();

            $gj = $this->getGarantiesEncaiss($sliceg);

            $response = array("status" => 0,
                "datas" => array("vj" => $result, "gj" => $gj, "ej" => $ej),
                "msg" => "");
            $this->response($this->json($response), 200);
        } else {
            $gj = $this->getGarantiesEncaiss($sliceg);
            $ej = $this->getComptantJour();

            $response = array("status" => 0,
                "datas" => array("vj" => null, "gj" => $gj, "ej" => $ej),
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getGarantiesEncaiss($sliceg) {

        $condMag = "";
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $query = "SELECT a.nom_art,f.code_fact,
                         m.nom_mag,m.code_mag,
                         v.id_vnt,v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,v.marge_vnt,
                         COALESCE(c.nom_clt,'-') as clt,
                         time(f.date_fact) as heure_fact,
                         f.login_caissier_fact,
                         f.code_caissier_fact,
                         f.id_fact,
                         f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
                         f.bl_fact_crdt,f.bl_fact_grt,f.bl_encaiss_grt
                         FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                         INNER JOIN t_magasin m on f.mag_fact=m.id_mag
                         LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                         WHERE f.id_fact NOT IN ($sliceg) AND DATE(f.date_encaiss_grt)='" . date('Y-m-d') . "' AND f.bl_encaiss_grt=1 $condMag ORDER BY v.id_vnt DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['TYPE'] = "VNT";
                $result[] = $row;
            }

            return $result;
        }
    }

    public function getComptantJour() {

        $condMag = "";
        $result = array();
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $queryt = "SELECT sum(f.tva_fact+f.bic_fact) as taxe,sum(f.remise_vnt_fact) as remise
                         FROM t_facture_vente f 
                         WHERE DATE(f.date_fact)='" . date('Y-m-d') . "'
                          AND f.bl_fact_crdt=0 
                             $condMag LIMIT 1";
        $rt = $this->mysqli->query($queryt) or die($this->mysqli->error . __LINE__);
        $rowt = $rt->fetch_assoc();

        $taxet = $rowt['taxe'];
        $remiset = $rowt['remise'];

        /* $query = "SELECT IFNULL(SUM(v.mnt_theo_vnt),0) as mntcpt 
          FROM t_vente v
          INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
          WHERE DATE(v.date_vnt)='" . date('Y-m-d') . "'
          AND f.bl_fact_crdt=0
          $condMag LIMIT 1"; */
        $query = "SELECT IFNULL(SUM(f.crdt_fact),0) as mntcpt 
                         FROM t_facture_vente f   WHERE DATE(f.date_fact)='" . date('Y-m-d') . "'
                          AND f.bl_fact_crdt=0 AND f.sup_fact=0
                             $condMag LIMIT 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $row = $r->fetch_assoc();

        $result["mntcpt"] = doubleval($row['mntcpt'] /* + $taxet - $remiset */);


        $queryt = "SELECT sum(f.remise_vnt_fact) as remise
                         FROM t_facture_vente f
                          WHERE DATE(f.date_fact)='" . date('Y-m-d') . "'
                          AND f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.sup_fact=0
                             $condMag LIMIT 1";
        $rt = $this->mysqli->query($queryt) or die($this->mysqli->error . __LINE__);
        $rowt = $rt->fetch_assoc();

        $remiset = $rowt['remise'];


        /* $query = "SELECT IFNULL(SUM(v.mnt_theo_vnt),0) as mntcrdt
          FROM t_vente v
          INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
          WHERE DATE(v.date_vnt)='" . date('Y-m-d') . "'
          AND f.bl_fact_crdt=1 AND f.bl_fact_grt=0
          $condMag LIMIT 1"; */
        $query = "SELECT IFNULL(SUM(f.crdt_fact),0) as mntcrdt
                         FROM t_facture_vente f 
                           WHERE DATE(f.date_fact)='" . date('Y-m-d') . "'
                          AND f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.sup_fact=0
                             $condMag LIMIT 1";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $row = $r->fetch_assoc();

        $result["mntcrdt"] = $row['mntcrdt'] /* - $remiset */;


        $query = "SELECT IFNULL(SUM(v.mnt_theo_vnt),0) as mntgrt,sum(f.remise_vnt_fact) as remise
                         FROM t_vente v 
                          INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE DATE(v.date_vnt)='" . date('Y-m-d') . "'
                          AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND f.bl_encaiss_grt=0 AND f.sup_fact=0
                             $condMag LIMIT 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $row = $r->fetch_assoc();
        $result["mntgrt"] = $row['mntgrt'] - $row['remise'];


        $query = "SELECT IFNULL(SUM(f.som_verse_crdt),0) as mntp
                         FROM t_facture_vente f 
                           WHERE DATE(f.date_fact)='" . date('Y-m-d') . "'
                          AND f.bl_fact_crdt=1 AND f.sup_fact=0
                             $condMag LIMIT 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $row = $r->fetch_assoc();
        $result["mntp"] = $row['mntp'];

        $query = "SELECT IFNULL(SUM(f.crdt_fact),0) as mntencaiss
                         FROM t_facture_vente f 
                           WHERE DATE(f.date_encaiss_grt)='" . date('Y-m-d') . "'
                          AND f.bl_fact_crdt=1 AND f.bl_encaiss_grt=1 AND f.bl_fact_grt=1 AND f.bl_crdt_regle=1 AND f.sup_fact=0
                             $condMag LIMIT 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $row = $r->fetch_assoc();
        $result["mntencaiss"] = $row['mntencaiss'];

        return $result;
    }

    public function venteCrdt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appVentecpts = $_POST;
        $items = $appVentecpts['items'];
        $id_mag = intval($_SESSION['userMag']);
        $id_clt = intval($appVentecpts['id_clt']);
        $exo_tva_clt = intval($appVentecpts['exo_tva_clt']);
        $mnt_crdt = doubleval($appVentecpts['mnt_total']);
        $ref_fac = (!empty($appVentecpts['num_ref_fac'])) ? $this->esc($appVentecpts['num_ref_fac']) : "REF" . date("YmdHis");
        $date_vnt = (!empty($appVentecpts['date'])) ? isoToMysqldate($appVentecpts['date']) : date("Y-m-d");

        $num_fac = $_SESSION['userMag'] . $this->esc($appVentecpts['num_fact']);
        $remise = doubleval($appVentecpts['remise']);
        $avance = doubleval($appVentecpts['avance']);

        $bltva = 0;
        $blbic = 0;


        if ($appVentecpts['bic'] == true || $appVentecpts['bic'] == 1) {
            $bltva = 1;
            $blbic = 1;
        } elseif ($appVentecpts['tva'] == true || $appVentecpts['tva'] == 1) {
            $bltva = 1;
        } else {
            
        }


        $dat = explode("-", $date_vnt);
        $an = $dat[0];

        $response = array();

        if (!empty($items) && !empty($id_mag)) {

            try {
                $this->mysqli->autocommit(FALSE);

                if ($_SESSION['mode_fact_uniq'] == 1)
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where bl_tva=0 AND YEAR(date_fact)='$an' order by id_fact DESC LIMIT 1";
                else
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where mag_fact=" . $_SESSION['userMag'] . " AND bl_tva=0 AND YEAR(date_fact)='$an'  order by id_fact DESC LIMIT 1";


                if ($bltva == 1)
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where bl_tva=1 AND YEAR(date_fact)='$an' order by id_fact DESC LIMIT 1";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


                if ($r->num_rows > 0) {

                    $result = $r->fetch_assoc();
                    $lastcode = $result['code_fact'];
                    $lastdate = $result['date_fact'];
                    $ldt = explode('-', $lastcode);
                    $dtfact = explode('-', $lastdate);
                    $ld = $ldt[1];

                    $lan = $dtfact[0];

                    if ($bltva == 1) {
                        if ($an == $lan)
                            $num_fac = "F" . $_SESSION['userMag'] . $an . "-" . str_pad(($ld + 1), 4, "0", STR_PAD_LEFT);
                        else
                            $num_fac = "F" . $_SESSION['userMag'] . $an . "-0001";
                    }
                    else {

                        if ($an == $lan)
                            $num_fac = $_SESSION['userMag'] . $lan . "-" . str_pad(($ld + 1), 4, "0", STR_PAD_LEFT);
                        else
                            $num_fac = $_SESSION['userMag'] . $an . "-0001";
                    }
                } else {
                    if ($bltva == 1)
                        $num_fac = "F" . $_SESSION['userMag'] . $an . "-0001";
                    else
                        $num_fac = $_SESSION['userMag'] . $an . "-0001";
                }



                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_facture_vente (
                     code_fact,
                     clnt_fact,
                     mag_fact, 
                     bl_tva,
                     bl_bic, 
                     remise_vnt_fact,
                     bl_fact_crdt,
                     bl_crdt_regle,
                     ref_fact_vnt,
                     date_fact,
                     caissier_fact,
                     login_caissier_fact,
                     code_caissier_fact) 
                     VALUES('" . $num_fac . "',
                          " . $id_clt . ",
                          " . $id_mag . ", 
                           " . $bltva . ",
                           " . $blbic . ", 
                               " . $remise . ",
                          1,0,                          
                          '" . $ref_fac . "',
                        CONCAT('$date_vnt',' ',time(now())),
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $factID = $this->mysqli->insert_id;



                foreach ($items as $item) {
                    $id_art = intval($item['id_art']);
                    $qte_vnt = intval($item['qte']);
                    $pu_vnt = intval($item['prix_mini_art']);
                    $mnt_vnt = intval($item['mnt']);
                    $marg_vnt = doubleval($item['mnt']) - doubleval($item['or_mnt']);

                    $query = "SELECT qte_stk FROM t_stock WHERE art_stk =$id_art AND mag_stk=$id_mag";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                    $result = $r->fetch_assoc();

                    if ($result['qte_stk'] >= $qte_vnt) {
                        $query = "INSERT INTO t_vente 
                        (facture_vnt,
                        clnt_vnt,
                        article_vnt,
                        qte_vnt,
                        pu_theo_vnt,
                        marge_vnt,
                        mnt_theo_vnt,
                        date_vnt) VALUES($factID,$id_clt,
                           $id_art,
                            $qte_vnt,
                             $pu_vnt,
                                 $marg_vnt,
                             $mnt_vnt,CONCAT('$date_vnt',' ',time(now())))";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        if ($r) {
                            $query = "UPDATE t_stock SET qte_stk=qte_stk - $qte_vnt WHERE art_stk =$id_art AND mag_stk=$id_mag";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }
                    } else {
                        throw new Exception("Quantite en stock d'un article, insuffisante");
                    }
                }


                $queryrem = "SELECT remise_vnt_fact from t_facture_vente where id_fact=$factID";
                $rem = $this->mysqli->query($queryrem) or die($this->mysqli->error . __LINE__);
                $resultem = $rem->fetch_assoc();

                $remise_existant = $resultem['remise_vnt_fact'];
                $remise +=$remise_existant;

                $this->verserAvance($factID, $id_clt, $avance, $mnt_crdt, $remise, $date_vnt);
                $lastopvnt = $this->getDetailsOfFacture($factID);


                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => $lastopvnt,
                    "msg" => array("message" => "Vente a credit effectuee avec success!", "f" => $factID));

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

    public function venteProf() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appVentecpts = $_POST;
        $items = $appVentecpts['items'];
        $id_mag = intval($_SESSION['userMag']);
        $id_clt = intval($appVentecpts['id_clt']);
        $exo_tva_clt = intval($appVentecpts['exo_tva_clt']);
        $mnt_crdt = doubleval($appVentecpts['mnt_total']);
        $date_items = (!empty($appVentecpts['date'])) ? isoToMysqldate($appVentecpts['date']) : date("Y-m-d");

        $num_fac = $_SESSION['userMag'] . $this->esc($appVentecpts['num_fact']);
        $remise = intval($appVentecpts['remise']);
        $avance = intval($appVentecpts['avance']);


        $bltva = 0;
        $blbic = 0;

        if ($appVentecpts['bic'] == true || $appVentecpts['bic'] == 1) {
            $bltva = 1;
            $blbic = 1;
        } elseif ($appVentecpts['tva'] == true || $appVentecpts['tva'] == 1) {
            $bltva = 1;
        } else {
            
        }




        $response = array();

        if (!empty($items) && !empty($id_mag)) {

            try {
                $this->mysqli->autocommit(FALSE);

                if ($_SESSION['mode_fact_uniq'] == 1)
                    $query = "SELECT code_pro,date(date_pro) as date_pro FROM t_proforma where  bl_tva=0  order by id_pro DESC LIMIT 1";
                else
                    $query = "SELECT code_pro,date(date_pro) as date_pro FROM t_proforma where mag_pro=" . $_SESSION['userMag'] . " AND bl_tva=0  order by id_pro DESC LIMIT 1";


                if ($bltva == 1)
                    $query = "SELECT code_pro,date(date_pro) as date_pro FROM t_proforma where bl_tva=1  order by id_pro DESC LIMIT 1";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


                $dat = explode("-", $date_items);
                $an = $dat[0];

                if ($r->num_rows > 0) {

                    $result = $r->fetch_assoc();
                    $lastcode = $result['code_pro'];
                    $lastdate = $result['date_pro'];
                    $ldt = explode('-', $lastcode);
                    $dtfact = explode('-', $lastdate);
                    $ld = $ldt[1];

                    $lan = $dtfact[0];

                    if ($bltva == 1) {
                        if ($an == $lan)
                            $num_fac = "P" . $_SESSION['userMag'] . $an . "-" . str_pad(($ld + 1), 4, "0", STR_PAD_LEFT);
                        else
                            $num_fac = "P" . $_SESSION['userMag'] . $an . "-0001";
                    }
                    else {

                        if ($an == $lan)
                            $num_fac = $_SESSION['userMag'] . $lan . "-" . str_pad(($ld + 1), 4, "0", STR_PAD_LEFT);
                        else
                            $num_fac = $_SESSION['userMag'] . $an . "-0001";
                    }
                } else {
                    if ($bltva == 1)
                        $num_fac = "P" . $_SESSION['userMag'] . $an . "-0001";
                    else
                        $num_fac = $_SESSION['userMag'] . $an . "-0001";
                }

                $heure_items = date("H:i:s");
                $query = "INSERT INTO  t_proforma (
                     code_pro,
                     clnt_pro,
                     mag_pro,
                     crdt_pro,
                     bl_tva,
                     bl_bic,
                      remise_items_pro,
                     bl_pro_crdt,
                     bl_crdt_regle,
                     date_pro,
                     caissier_pro,
                     login_caissier_pro,
                     code_caissier_pro) 
                     VALUES('" . $num_fac . "',
                          " . $id_clt . ",
                          " . $id_mag . ",
                           " . $mnt_crdt . ",
                           " . $bltva . ",
                           " . $blbic . ",
                                " . $remise . ",
                          1,0,                          
                              CONCAT('$date_items',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $factID = $this->mysqli->insert_id;



                foreach ($items as $item) {
                    $id_art = intval($item['id_art']);
                    $qte_items = intval($item['qte']);
                    $pu_items = intval($item['prix_mini_art']);
                    $mnt_items = intval($item['mnt']);
                    $marg_items = doubleval($item['mnt']) - doubleval($item['or_mnt']);

                    $query = "SELECT qte_stk FROM t_stock WHERE art_stk =$id_art AND mag_stk=$id_mag";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                    $result = $r->fetch_assoc();

                    $query = "INSERT INTO t_proforma_items 
                        (facture_items,
                        clnt_items,
                        article_items,
                        qte_items,
                        pu_theo_items,
                        marge_items,
                        mnt_theo_items,
                        date_items) VALUES($factID,$id_clt,
                           $id_art,
                            $qte_items,
                             $pu_items,
                                 $marg_items,
                             $mnt_items,CONCAT('$date_items',' ',time(now())))";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }


                $queryrem = "SELECT remise_items_pro from t_proforma where id_pro=$factID";
                $rem = $this->mysqli->query($queryrem) or die($this->mysqli->error . __LINE__);
                $resultem = $rem->fetch_assoc();

                $remise_existant = $resultem['remise_items_pro'];
                $remise +=$remise_existant;



                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => array("message" => "Facture Pro Forma creee avec success!", "f" => $factID));

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

    public function venteGrt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appVentecpts = $_POST;
        $items = $appVentecpts['items'];
        $id_mag = intval($_SESSION['userMag']);
        $id_clt = intval($appVentecpts['id_clt']);
        $exo_tva_clt = intval($appVentecpts['exo_tva_clt']);
        $mnt_crdt = doubleval($appVentecpts['mnt_total']);
        $ref_fac = (!empty($appVentecpts['num_ref_fac'])) ? $this->esc($appVentecpts['num_ref_fac']) : "REF" . date("YmdHis");
        $date_vnt = (!empty($appVentecpts['date'])) ? isoToMysqldate($appVentecpts['date']) : date("Y-m-d");

        $num_fac = $_SESSION['userMag'] . $this->esc($appVentecpts['num_fact']);
        $remise = intval($appVentecpts['remise']);
        $avance = intval($appVentecpts['avance']);

        $bltva = 0;
        $blbic = 0;
        $mnttva = 0;
        $mntbic = 0;

        if ($appVentecpts['bic'] == true || $appVentecpts['bic'] == 1) {
            $bltva = 1;
            $blbic = 1;
        } elseif ($appVentecpts['tva'] == true || $appVentecpts['tva'] == 1) {
            $bltva = 1;
        } else {
            
        }

        $dat = explode("-", $date_vnt);
        $an = $dat[0];
        $response = array();

        if (!empty($items) && !empty($id_mag)) {

            try {
                $this->mysqli->autocommit(FALSE);

                if ($_SESSION['mode_fact_uniq'] == 1)
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where bl_tva=0 AND YEAR(date_fact)='$an' order by id_fact DESC LIMIT 1";
                else
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where mag_fact=" . $_SESSION['userMag'] . " AND YEAR(date_fact)='$an'   order by id_fact DESC LIMIT 1";


                if ($bltva == 1)
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where bl_tva=1 AND YEAR(date_fact)='$an' order by id_fact DESC LIMIT 1";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);



                if ($r->num_rows > 0) {

                    $result = $r->fetch_assoc();
                    $lastcode = $result['code_fact'];
                    $lastdate = $result['date_fact'];
                    $ldt = explode('-', $lastcode);
                    $dtfact = explode('-', $lastdate);
                    $ld = $ldt[1];



                    $lan = $dtfact[0];

                    if ($bltva == 1) {

                        if ($an == $lan)
                            $num_fac = "F" . $_SESSION['userMag'] . $an . "-" . str_pad(($ld + 1), 4, "0", STR_PAD_LEFT);
                        else
                            $num_fac = "F" . $_SESSION['userMag'] . $an . "-0001";
                    }
                    else {

                        if ($an == $lan)
                            $num_fac = $_SESSION['userMag'] . $lan . "-" . str_pad(($ld + 1), 4, "0", STR_PAD_LEFT);
                        else
                            $num_fac = $_SESSION['userMag'] . $an . "-0001";
                    }
                } else {
                    if ($bltva == 1)
                        $num_fac = "F" . $_SESSION['userMag'] . $an . "-0001";
                    else
                        $num_fac = $_SESSION['userMag'] . $an . "-0001";
                }

                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_facture_vente (
                     code_fact,
                     clnt_fact,
                     mag_fact,
                      bl_tva,
                     bl_bic,
                      remise_vnt_fact,
                     bl_fact_crdt,
                     bl_fact_grt,
                     bl_crdt_regle,
                     ref_fact_vnt,
                     date_fact,
                     caissier_fact,
                     login_caissier_fact,
                     code_caissier_fact) 
                     VALUES('" . $num_fac . "',
                          " . $id_clt . ",
                          " . $id_mag . ", 
                           " . $bltva . ",
                           " . $blbic . ", 
                               " . $remise . ",
                          1,1,0,                          
                          '" . $ref_fac . "',
                              CONCAT('$date_vnt',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $factID = $this->mysqli->insert_id;




                foreach ($items as $item) {
                    $id_art = intval($item['id_art']);
                    $qte_vnt = intval($item['qte']);
                    $pu_vnt = intval($item['prix_mini_art']);
                    $mnt_vnt = intval($item['mnt']);
                    $marg_vnt = doubleval($item['mnt']) - doubleval($item['or_mnt']);

                    $query = "SELECT qte_stk FROM t_stock WHERE art_stk =$id_art AND mag_stk=$id_mag";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                    $result = $r->fetch_assoc();

                    if ($result['qte_stk'] >= $qte_vnt) {
                        $query = "INSERT INTO t_vente 
                        (facture_vnt,
                        clnt_vnt,
                        article_vnt,
                        qte_vnt,
                        pu_theo_vnt,
                        marge_vnt,
                        mnt_theo_vnt,
                        date_vnt) VALUES($factID,$id_clt,
                           $id_art,
                            $qte_vnt,
                             $pu_vnt,
                                 $marg_vnt,
                             $mnt_vnt,CONCAT('$date_vnt',' ',time(now())))";
                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        if ($r) {
                            $query = "UPDATE t_stock SET qte_stk=qte_stk - $qte_vnt WHERE art_stk =$id_art AND mag_stk=$id_mag";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }
                    } else {
                        throw new Exception("Quantite en stock d'un article, insuffisante");
                    }
                }

                $queryrem = "SELECT remise_vnt_fact from t_facture_vente where id_fact=$factID";
                $rem = $this->mysqli->query($queryrem) or die($this->mysqli->error . __LINE__);
                $resultem = $rem->fetch_assoc();

                $remise_existant = $resultem['remise_vnt_fact'];
                $remise +=$remise_existant;

                $this->verserAvance($factID, $id_clt, $avance, $mnt_crdt, $remise, $date_vnt);
                $lastopvnt = $this->getDetailsOfFacture($factID);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => $lastopvnt,
                    "msg" => array("message" => "Garantie effectuee avec success!", "f" => $factID));

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

    public function venteCpt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }


        $appVentecpts = $_POST;
        $items = $appVentecpts['items'];
        $id_mag = intval($_SESSION['userMag']);
        $ref_fac = (!empty($appVentecpts['num_ref_fac'])) ? $this->esc($appVentecpts['num_ref_fac']) : "REF" . date("YmdHis");
        $date_vnt = (!empty($appVentecpts['date'])) ? isoToMysqldate($appVentecpts['date']) : date("Y-m-d");
        $num_fac = $_SESSION['userMag'] . $this->esc($appVentecpts['num_fact']);
        $client = intval($appVentecpts['vnt_clt']);
        $exo_tva_clt = intval($appVentecpts['exo_tva_clt']);
        $remise = intval($appVentecpts['remise']);
        $mnt_crdt = doubleval($appVentecpts['mnt_total']);

        /* remise */
        $mnt_crdt -=$remise;

        $bltva = 0;
        $blbic = 0;
        $mnttva = 0;
        $mntbic = 0;

        if ($appVentecpts['bic'] == true || $appVentecpts['bic'] == 1) {
            $bltva = 1;
            $blbic = 1;
        } elseif ($appVentecpts['tva'] == true || $appVentecpts['tva'] == 1) {
            $bltva = 1;
        } else {
            
        }

        $dat = explode("-", $date_vnt);
        $an = $dat[0];

        $response = array();

        if (!empty($items) && !empty($id_mag)) {

            try {
                $this->mysqli->autocommit(FALSE);
                if ($_SESSION['mode_fact_uniq'] == 1)
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where bl_tva=0 AND YEAR(date_fact)='$an'  order by id_fact DESC LIMIT 1";
                else
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where mag_fact=" . $_SESSION['userMag'] . " AND bl_tva=0  AND YEAR(date_fact)='$an' order by id_fact DESC LIMIT 1";

                if ($bltva == 1)
                    $query = "SELECT code_fact,date(date_fact) as date_fact FROM t_facture_vente where bl_tva=1 AND YEAR(date_fact)='$an'  order by id_fact DESC LIMIT 1";

                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                if ($r->num_rows > 0) {

                    $result = $r->fetch_assoc();
                    $lastcode = $result['code_fact'];
                    $lastdate = $result['date_fact'];
                    $ldt = explode('-', $lastcode);
                    $dtfact = explode('-', $lastdate);
                    $ld = $ldt[1];


                    $file = fopen("fichier.txt", "a");

                    $lan = $dtfact[0];

                    if ($bltva == 1) {


                        if ($an == $lan)
                            $num_fac = "F" . $_SESSION['userMag'] . $an . "-" . str_pad(($ld + 1), 4, "0", STR_PAD_LEFT);
                        else
                            $num_fac = "F" . $_SESSION['userMag'] . $an . "-0001";
                    }
                    else {

                        if ($an == $lan)
                            {
                                $num_fac = $_SESSION['userMag'] . $lan . "-" . str_pad(($ld + 1), 4, "0", STR_PAD_LEFT);
                                fwrite($file,"1num_fac**=".$num_fac);
                                fwrite($file,"query**=".$query);
                            }
                        else{
                            fwrite($file,"2num_fac**=".$num_fac);
                            fwrite($file,"2num_fac**=".$num_fac);
                            $num_fac = $_SESSION['userMag'] . $an . "-0001";
                        }
                    }
                    
                fclose($file);
                } else {
                    

                    if ($bltva == 1)
                        $num_fac = "F" . $_SESSION['userMag'] . $an . "-0001";
                    else
                        $num_fac = $_SESSION['userMag'] . $an . "-0001";
                }

                $heure_vnt = date("H:i:s");

                $query = "INSERT INTO  t_facture_vente (
                     code_fact,
                     clnt_fact,
                     mag_fact, 
                     bl_tva,
                     bl_bic, 
                     remise_vnt_fact,
                     bl_fact_crdt,
                     bl_crdt_regle,
                     ref_fact_vnt,
                     date_fact,
                     caissier_fact,
                     login_caissier_fact,
                     code_caissier_fact) 
                     VALUES('" . $num_fac . "',
                          $client,
                          " . $id_mag . ",  
                          " . $bltva . ",
                          " . $blbic . ", 
                        " . $remise . ",
                          0,1,                          
                          '" . $ref_fac . "',
                             CONCAT('$date_vnt',' ',time(now())),
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $factID = $this->mysqli->insert_id;

                foreach ($items as $item) {
                    $id_art = intval($item['id_art']);
                    $qte_vnt = intval($item['qte']);
                    $pu_vnt = doubleval($item['prix_mini_art']);
                    $mnt_vnt = doubleval($item['mnt']);
                    $marg_vnt = doubleval($item['mnt']) - doubleval($item['or_mnt']);

                    $query = "SELECT qte_stk FROM t_stock WHERE art_stk =$id_art AND mag_stk=$id_mag";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                    $result = $r->fetch_assoc();

                    if ($result['qte_stk'] >= $qte_vnt) {
                        $query = "INSERT INTO t_vente 
                        (facture_vnt,
                        clnt_vnt,
                        article_vnt,
                        qte_vnt,
                        pu_theo_vnt,
                        marge_vnt,
                        mnt_theo_vnt,
                        date_vnt) VALUES($factID,$client,
                           $id_art,
                            $qte_vnt,
                             $pu_vnt,
                                $marg_vnt,
                             $mnt_vnt,CONCAT('$date_vnt',' ',time(now())))";

                        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        if ($r) {
                            $query = "UPDATE t_stock SET qte_stk=qte_stk - $qte_vnt WHERE art_stk =$id_art AND mag_stk=$id_mag";
                            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                        }
                    } else {
                        throw new Exception("Quantite en stock d'un article, insuffisante");
                    }
                }
                $lastopvnt = $this->getDetailsOfFacture($factID);
                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);


                $response = array("status" => 0,
                    "datas" => $lastopvnt,
                    "msg" => array("message" => "Vente au comptant effectuee avec success!!", "f" => $factID));


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

    public function rdfP() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }


        $appVentecpts = $_POST;
        $items = $appVentecpts['items'];


        $response = array();

        try {
            $this->mysqli->autocommit(FALSE);

            foreach ($items as $item) {
                $id_art = intval($item['id_art']);
                $pu_vnt = intval($item['prix_mini_art']);

                $query = "select max(id_prix_art) as 'key' from t_prix_article where art_prix_art =$id_art Limit 1";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $result = $r->fetch_assoc();
                $key = $result['key'];

                $query = "Update t_prix_article set prix_mini_art=$pu_vnt WHERE id_prix_art=$key";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            }

            $this->mysqli->commit();
            $this->mysqli->autocommit(TRUE);


            $response = array("status" => 0,
                "datas" => "",
                "msg" => "Prix modifies effectuee avec success!");

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

    public function addItemFct() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        try {
            $this->mysqli->autocommit(FALSE);

            $appVentecpts = $_POST;
            $id_mag = intval($appVentecpts['id_mag']); // intval($_SESSION['userMag']); 
            $factID = doubleval($appVentecpts['id_fact']);


            $id_art = intval($appVentecpts['id_art']);
            $qte_vnt = intval($appVentecpts['qte']);
            $pu_vnt = intval($appVentecpts['prix_mini_art']);
            $mnt_vnt = intval($appVentecpts['mnt']);
            $marg_vnt = doubleval($appVentecpts['mnt']) - doubleval($appVentecpts['or_mnt']);

            $qk = "SELECT clnt_fact,date_fact   FROM t_facture_vente  WHERE id_fact =$factID LIMIT 1";
            $rs = $this->mysqli->query($qk) or die($this->mysqli->error . __LINE__);
            $res = $rs->fetch_assoc();
            $id_clt = intval($res['clnt_fact']);
            $date_vnt = $res['date_fact'];

            $query = "SELECT qte_stk FROM t_stock WHERE art_stk =$id_art AND mag_stk=$id_mag";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            $result = $r->fetch_assoc();

            if ($result['qte_stk'] >= $qte_vnt) {
                $query = "INSERT INTO t_vente 
                        (facture_vnt,
                        clnt_vnt,
                        article_vnt,
                        qte_vnt,
                        pu_theo_vnt,
                        mnt_theo_vnt,
                        marge_vnt,
                        date_vnt) VALUES($factID,$id_clt,
                           $id_art,
                            $qte_vnt,
                             $pu_vnt, 
                             $mnt_vnt,
                            $marg_vnt,'$date_vnt')";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                if ($r) {
                    $query = "UPDATE t_stock SET qte_stk=qte_stk - $qte_vnt WHERE art_stk =$id_art AND mag_stk=$id_mag";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }
            }

            $this->mysqli->commit();
            $this->mysqli->autocommit(TRUE);

            $response = array("status" => 0,
                "datas" => "",
                "msg" => "Article ajoute a la facture avec success!");

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

    public function addItemProFct() {
        /*  if ($this->get_request_method() != "POST") {
          $this->response('', 406);
          }

          try {
          $this->mysqli->autocommit(FALSE);

          $appVentecpts = $_POST;
          $id_mag = intval($appVentecpts['id_mag']); // intval($_SESSION['userMag']);
          $factID = doubleval($appVentecpts['id_pro']);


          $id_art = intval($appVentecpts['id_art']);
          $qte_items = intval($appVentecpts['qte']);
          $pu_items = intval($appVentecpts['prix_mini_art']);
          $mnt_items = intval($appVentecpts['mnt']);
          $marg_items = doubleval($appVentecpts['mnt']) - doubleval($appVentecpts['or_mnt']);

          $qk = "SELECT clnt_pro,date_pro   FROM t_proforma  WHERE id_pro =$factID LIMIT 1";
          $rs = $this->mysqli->query($qk) or die($this->mysqli->error . __LINE__);
          $res = $rs->fetch_assoc();
          $id_clt = intval($res['clnt_pro']);
          $date_items = $res['date_pro'];




          $query = "INSERT INTO t_proforma_items
          (facture_items,
          clnt_items,
          article_items,
          qte_items,
          pu_theo_items,
          mnt_theo_items,
          marge_items,
          date_items) VALUES($factID,$id_clt,
          $id_art,
          $qte_items,
          $pu_items,
          $mnt_items,
          $marg_items,'$date_items')";
          $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


          $query2 = "select bl_pro_crdt,bl_pro_grt,bl_crdt_regle,crdt_pro,tva_pro,bic_pro,bl_tva,bl_bic,exo_tva_clt FROM t_proforma
          inner join t_client on t_proforma.clnt_pro=t_client.id_clt
          WHERE t_proforma.id_pro=$factID";

          $req = $this->mysqli->query($query2) or die($this->mysqli->error . __LINE__);

          if ($req->num_rows > 0) {
          $row = $req->fetch_assoc();
          $exo_tva = $row['exo_tva_clt'];

          if ($row['bl_tva'] == 1 && $row['bl_bic'] == 0) {

          if ($exo_tva == 0) {
          $crdt = doubleval($row['crdt_pro'] - $row['tva_pro']);
          $ncrdt = doubleval($crdt + $mnt_items);
          $ntva = $ncrdt * 0.18;

          $ncrdt = doubleval($ncrdt + $ntva);

          $qu = "Update t_proforma
          set crdt_pro=$ncrdt, tva_pro=$ntva
          WHERE id_pro=$factID";
          $req = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
          } else {
          $crdt = doubleval($row['crdt_pro']);
          $ncrdt = doubleval($crdt + $mnt_items);
          $ntva = $ncrdt * 0.18;

          $qu = "Update t_proforma
          set crdt_pro=$ncrdt, tva_pro=$ntva
          WHERE id_pro=$factID";
          $req = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
          }
          } elseif ($row['bl_bic'] == 1) {

          if ($exo_tva == 0) {
          $crdt = doubleval($row['crdt_pro'] - $row['tva_pro'] - $row['bic_pro']);
          $ncrdt = doubleval($crdt + $mnt_items);
          $ntva = $ncrdt * 0.18;
          $nbic = $ncrdt * 0.0236;

          $ncrdt = doubleval($ncrdt + $ntva + $nbic);

          $qu = "Update t_proforma
          set crdt_pro=$ncrdt, tva_pro=$ntva, bic_pro=$nbic
          WHERE id_pro=$factID";
          $req = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
          } else {
          $crdt = doubleval($row['crdt_pro']);
          $ncrdt = doubleval($crdt + $mnt_items);
          $ntva = $ncrdt * 0.18;
          $nbic = $ncrdt * 0.0236;

          $qu = "Update t_proforma
          set crdt_pro=$ncrdt, tva_pro=$ntva,bic_pro=$nbic
          WHERE id_pro=$factID";
          $req = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
          }
          } else {
          $qu = "Update t_proforma
          set crdt_pro=crdt_pro+$mnt_items
          WHERE id_pro=$factID";
          $req = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
          }
          } else {// client ambulant

          $row = $req->fetch_assoc();

          if ($row['bl_tva'] == 1 && $row['bl_bic'] == 0) {

          $anctva = doubleval($row['tva_pro']);
          $ntva = $mnt_items * 0.18;

          $ntva = doubleval($anctva + $ntva);

          $qu = "Update t_proforma
          set tva_pro=$ntva
          WHERE id_pro=$factID";
          $req = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
          } elseif ($row['bl_bic'] == 1) {

          $anctva = doubleval($row['tva_pro']);
          $ancbic = doubleval($row['bic_pro']);
          $ntva = $mnt_items * 0.18;
          $nbic = $mnt_items * 0.0236;

          $ntva = doubleval($anctva + $ntva);
          $nbic = doubleval($ancbic + $nbic);

          $qu = "Update t_proforma
          set tva_pro=$ntva,bic_pro=$nbic
          WHERE id_pro=$factID";
          $req = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
          } else {
          $qu = "Update t_proforma
          set crdt_pro=crdt_pro+$mnt_items
          WHERE id_pro=$factID";
          $req = $this->mysqli->query($qu) or die($this->mysqli->error . __LINE__);
          }
          }

          $this->mysqli->commit();
          $this->mysqli->autocommit(TRUE);

          $response = array("status" => 0,
          "datas" => "",
          "msg" => "Article ajoute a la pro forma avec success!");

          $this->response($this->json($response), 200);
          } catch (Exception $exc) {
          $this->mysqli->rollback();
          $this->mysqli->autocommit(TRUE);
          $response = array("status" => 1,
          "datas" => "",
          "msg" => $exc->getMessage());

          $this->response($this->json($response), 200);
          } */
    }

    public function getMagArticlePrices($id_art, $id_mag) {

        $query = "SELECT ta.prix_mini_art_mag as prix_mini_art ,ta.prix_gros_art_mag as prix_gros_art  FROM 
             t_prix_article_magasin ta GROUP BY ta.art_prix_art_mag DESC 
             WHERE ta.art_prix_art_mag=$id_art AND ta.mag_prix_art_mag=$id_mag";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $result = $r->fetch_assoc();
        return $response = $result;
    }

    public function getExtCategories() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "select DISTINCT cat.id_cat,cat.nom_cat from t_categorie_article cat
		inner join t_article a on a.cat_art=cat.id_cat
                inner join t_stock s on a.id_art=s.art_stk AND s.qte_stk > 0 GROUP BY s.art_stk";

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

    public function getExtCategoriesOfMag() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (isset($_SESSION['userMag']) && $_SESSION['userMag'] != "") {
            $id_mag = intval($_SESSION['userMag']);

            $condmag = "";
            if ($id_mag > 0)
                $condmag = " AND s.mag_stk=$id_mag";

            $query = "select DISTINCT cat.id_cat,cat.nom_cat from t_categorie_article cat
		inner join t_article a on a.cat_art=cat.id_cat
                inner join t_stock s on a.id_art=s.art_stk AND s.qte_stk > 0 $condmag GROUP BY s.art_stk ORDER BY cat.nom_cat";



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
        }
        $this->response('', 204);
    }

    public function getExtProCategories() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (isset($_SESSION['userMag']) && $_SESSION['userMag'] != "") {
            $id_mag = intval($_SESSION['userMag']);

            $condmag = "";
            if ($id_mag > 0)
                $condmag = " AND s.mag_stk=$id_mag";

            $query = "select DISTINCT cat.id_cat,cat.nom_cat from t_categorie_article cat
		inner join t_article a on a.cat_art=cat.id_cat
                inner join t_stock s on a.id_art=s.art_stk AND s.qte_stk >= 0 $condmag GROUP BY s.art_stk ORDER BY cat.nom_cat";



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
        }
        $this->response('', 204);
    }

    public function getExtMagasins() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "select * from t_magasin m
inner join (select * from t_stock where qte_stk > 0 GROUP BY mag_stk) sm ON m.id_mag=sm.mag_stk order by m.nom_mag";
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

    public function getExtArticlesOfCategorie() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_cat'])) {
            $id_mag = intval($_SESSION['userMag']);
            $id_cat = intval($this->_request['id_cat']);
            $query = "select ar.*,
COALESCE( mag.prix_mini_art_mag, tpa.prix_mini_art) as prix_mini_art,
				COALESCE( mag.prix_gros_art_mag, tpa.prix_gros_art) as prix_gros_art 
				from (select a.id_art,s.qte_stk,c.nom_cat,a.code_art,a.nom_art
		from t_stock s 
		inner join t_article a on s.art_stk=a.id_art
		inner join t_categorie_article c ON a.cat_art=c.id_cat
		where s.qte_stk>0 
		and c.id_cat=$id_cat
		and s.mag_stk=$id_mag) ar
		inner join (select * from t_prix_article GROUP BY art_prix_art DESC) tpa
		on ar.id_art=tpa.art_prix_art
		LEFT JOIN ( SELECT  art_prix_art_mag,mag_prix_art_mag,prix_mini_art_mag
              , prix_gros_art_mag
           FROM t_prix_article_magasin group by mag_prix_art_mag DESC
	            ) mag ON  mag.art_prix_art_mag = ar.id_art AND  mag.mag_prix_art_mag = $id_mag";
$file = fopen("fichier.txt", "a");
            fwrite($file,$query);
            fclose($file);
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        }
    }

    public function getSupExtArticlesOfCategorie() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_cat'])) {
            $id_mag = intval($this->_request['id_mag']);
            $id_cat = intval($this->_request['id_cat']);
            $query = "select ar.*,
COALESCE( mag.prix_mini_art_mag, tpa.prix_mini_art) as prix_mini_art,
				COALESCE( mag.prix_gros_art_mag, tpa.prix_gros_art) as prix_gros_art 
				from (select a.id_art,s.qte_stk,c.nom_cat,a.code_art,a.nom_art
		from t_stock s 
		inner join t_article a on s.art_stk=a.id_art
		inner join t_categorie_article c ON a.cat_art=c.id_cat
		where s.qte_stk>0 
		and c.id_cat=$id_cat
		and s.mag_stk=$id_mag) ar
		inner join (select * from t_prix_article GROUP BY art_prix_art DESC) tpa
		on ar.id_art=tpa.art_prix_art
		LEFT JOIN ( SELECT  art_prix_art_mag,mag_prix_art_mag,prix_mini_art_mag
              , prix_gros_art_mag
           FROM t_prix_article_magasin group by mag_prix_art_mag DESC
	            ) mag ON  mag.art_prix_art_mag = ar.id_art AND  mag.mag_prix_art_mag = $id_mag";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        }
    }

    public function getExtProArticlesOfCategorie() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_cat'])) {
            $id_mag = intval($_SESSION['userMag']);
            $id_cat = intval($this->_request['id_cat']);
            $query = "select ar.*,
COALESCE( mag.prix_mini_art_mag, tpa.prix_mini_art) as prix_mini_art,
				COALESCE( mag.prix_gros_art_mag, tpa.prix_gros_art) as prix_gros_art 
				from (select a.id_art,s.qte_stk,c.nom_cat,a.code_art,a.nom_art
		from t_stock s 
		inner join t_article a on s.art_stk=a.id_art
		inner join t_categorie_article c ON a.cat_art=c.id_cat
		where s.qte_stk>=0 
		and c.id_cat=$id_cat
		and s.mag_stk=$id_mag) ar
		inner join (select * from t_prix_article GROUP BY art_prix_art DESC) tpa
		on ar.id_art=tpa.art_prix_art
		LEFT JOIN ( SELECT  art_prix_art_mag,mag_prix_art_mag,prix_mini_art_mag
              , prix_gros_art_mag
           FROM t_prix_article_magasin group by mag_prix_art_mag DESC
	            ) mag ON  mag.art_prix_art_mag = ar.id_art AND  mag.mag_prix_art_mag = $id_mag";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        }
    }

    public function getSupExtProArticlesOfCategorie() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id_cat'])) {
            $id_mag = intval($this->_request['id_mag']);
            $id_cat = intval($this->_request['id_cat']);
            $query = "select ar.*,
COALESCE( mag.prix_mini_art_mag, tpa.prix_mini_art) as prix_mini_art,
				COALESCE( mag.prix_gros_art_mag, tpa.prix_gros_art) as prix_gros_art 
				from (select a.id_art,s.qte_stk,c.nom_cat,a.code_art,a.nom_art
		from t_stock s 
		inner join t_article a on s.art_stk=a.id_art
		inner join t_categorie_article c ON a.cat_art=c.id_cat
		where s.qte_stk>=0 
		and c.id_cat=$id_cat
		and s.mag_stk=$id_mag) ar
		inner join (select * from t_prix_article GROUP BY art_prix_art DESC) tpa
		on ar.id_art=tpa.art_prix_art
		LEFT JOIN ( SELECT  art_prix_art_mag,mag_prix_art_mag,prix_mini_art_mag
              , prix_gros_art_mag
           FROM t_prix_article_magasin group by mag_prix_art_mag DESC
	            ) mag ON  mag.art_prix_art_mag = ar.id_art AND  mag.mag_prix_art_mag = $id_mag";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);
        }
    }

    public function loadArticlesForSell() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        // $id_mag = intval($_SESSION['userMag']); 
        $id_mag = !empty($_GET['id_mag']) ? intval($_GET['id_mag']) : intval($_SESSION['userMag']);

        $query = "select ar.*,
COALESCE( mag.prix_mini_art_mag, tpa.prix_mini_art) as prix_mini_art,
				COALESCE( mag.prix_gros_art_mag, tpa.prix_gros_art) as prix_gros_art 
				from (select a.id_art,s.qte_stk,c.nom_cat,a.code_art,a.nom_art
		from t_stock s 
		inner join t_article a on s.art_stk=a.id_art
		inner join t_categorie_article c ON a.cat_art=c.id_cat
		where s.qte_stk>0 
		 and s.mag_stk=$id_mag) ar
		inner join (select * from t_prix_article GROUP BY art_prix_art DESC) tpa
		on ar.id_art=tpa.art_prix_art
		LEFT JOIN ( SELECT  art_prix_art_mag,mag_prix_art_mag,prix_mini_art_mag
              , prix_gros_art_mag
           FROM t_prix_article_magasin group by mag_prix_art_mag DESC
	            ) mag ON  mag.art_prix_art_mag = ar.id_art AND  mag.mag_prix_art_mag = $id_mag";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response = array("status" => 0,
            "datas" => $result,
            "msg" => "");
        $this->response($this->json($response), 200);
    }

    public function loadArticlesForPro() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }



        $query = "select ar.*,
COALESCE( mag.prix_mini_art_mag, tpa.prix_mini_art) as prix_mini_art,
				COALESCE( mag.prix_gros_art_mag, tpa.prix_gros_art) as prix_gros_art 
				from (select a.id_art,s.qte_stk,c.nom_cat,a.code_art,a.nom_art
		from t_stock s 
		inner join t_article a on s.art_stk=a.id_art
		inner join t_categorie_article c ON a.cat_art=c.id_cat
		where s.qte_stk>=0 ) ar
		inner join (select * from t_prix_article GROUP BY art_prix_art DESC) tpa
		on ar.id_art=tpa.art_prix_art";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response = array("status" => 0,
            "datas" => $result,
            "msg" => "");
        $this->response($this->json($response), 200);
    }

    /* /////// IONIC ////////////// */

    public function getVentesMobile() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        $query = "SELECT a.nom_art,f.code_fact,
                         m.nom_mag,
                         v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,
                         COALESCE(c.nom_clt,'-') as clt,
                         f.login_caissier_fact,
                         f.code_caissier_fact,
                         f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
                         f.bl_fact_crdt
                         FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                         INNER JOIN t_magasin m on f.mag_fact=m.id_mag
                         LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                         WHERE DATE(v.date_vnt)='" . date('Y-m-d') . "'
                           ORDER BY v.id_vnt DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }

            $ej = $this->getComptantJourMobile();


            $response = array("status" => 0,
                "datas" => array("vj" => $result, "ej" => $ej),
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

    public function getComptantJourMobile() {

        $condMag = "";
        $result = array();

        $query = "SELECT IFNULL(SUM(v.mnt_theo_vnt),0) as mntcpt
                         FROM t_vente v 
                          INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE DATE(v.date_vnt)='" . date('Y-m-d') . "'
                          AND v.clnt_vnt=0 
                               LIMIT 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $row = $r->fetch_assoc();
        $result["mntcpt"] = $row['mntcpt'];

        $query = "SELECT IFNULL(SUM(v.mnt_theo_vnt),0) as mntcrdt
                         FROM t_vente v 
                          INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE DATE(v.date_vnt)='" . date('Y-m-d') . "'
                          AND v.clnt_vnt>0 
                               LIMIT 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $row = $r->fetch_assoc();
        $result["mntcrdt"] = $row['mntcrdt'];


        $query = "SELECT IFNULL(SUM(f.som_verse_crdt),0) as mntp
                         FROM t_facture_vente f 
                           WHERE DATE(f.date_fact)='" . date('Y-m-d') . "'
                          AND f.clnt_fact>0 
                             $condMag LIMIT 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $row = $r->fetch_assoc();
        $result["mntp"] = $row['mntp'];

        return $result;
    }

    public function verserAvance($fac, $clt, $avance, $credit, $remise, $date) {

        $mnt_val_remise = $remise;
        $id_fac = intval($fac);
        $id_clt = intval($clt);
        $mnt_avance = doubleval($avance);
        $mnt_credit = doubleval($credit);


        if (!empty($id_fac) && !empty($mnt_avance)) {

            $query = "INSERT INTO  t_creance_client (
                     	fact_crce_clnt,
                        clnt_crce,
                     mnt_paye_crce_clnt,
                     date_crce_clnt,
                     caissier_crce_clnt,
                     caissier_login_crce,
                     code_caissier_crce) 
                     VALUES(" . $id_fac . ",
                         " . $id_clt . ",
                          " . $mnt_avance . ", 
                             '" . $date . "',
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         '" . $_SESSION['userCode'] . "')";


            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);



            $query = "UPDATE t_facture_vente SET som_verse_crdt=som_verse_crdt + $mnt_avance WHERE id_fact =$id_fac";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $query = " SELECT SUM(mnt_paye_crce_clnt) as mnt FROM t_creance_client WHERE  fact_crce_clnt=$id_fac";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            $result = $r->fetch_assoc();
            $mnt = $result['mnt'];


            if ($mnt >= ($mnt_credit)) {
                $query = "UPDATE t_facture_vente SET bl_crdt_regle=1 WHERE id_fact =$id_fac";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            }

            return true;
        }
    }

    public function getDs() {

        $response = array();

        try {
            $this->mysqli->autocommit(FALSE);
            $query = "SELECT DATE(now()) as date_f from dual limit 1 ";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $dtj = "";
            if ($r->num_rows > 0) {
                $result = $r->fetch_assoc();
                $dt = $result['date_f'];
                $dt = explode('-', $dt);
                $dtj = $dt[2] . "/" . $dt[1] . "/" . $dt[0];
            }

            $this->mysqli->commit();
            $this->mysqli->autocommit(TRUE);


            $response = array("status" => 0,
                "datas" => array("datej" => $dtj),
                "msg" => array("message" => "success"));


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
    $app = new venteController;
    $app->processApp();
}
?>