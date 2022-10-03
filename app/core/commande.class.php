<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class commandeController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getCommande() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT app.id_cmd,app.bon_cmd,app.frns_cmd,app.date_cmd,app.date_cmd_recu,app.login_cmd,f.nom_frns  FROM t_commande app inner join (select id_frns,nom_frns from t_fournisseur) f on app.frns_cmd=f.id_frns WHERE app.id_cmd =$id LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $result = $r->fetch_assoc();

                $response = array("status" => 0,
                    "datas" => $result,
                    "msg" => "");
                $this->response($this->json($response), 200);
            }
            $response = array("status" => 1,
                "datas" => "",
                "msg" => "Mauvais identifiant de la commande");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant de la commande !");
        $this->response($this->json($response), 200);
    }

    public function getCmdByCode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $code = $this->esc($_GET['vr']);
        $dt = "";
        if (isDate($code))
            $dt = " OR DATE(app.date_cmd)='" . isoToMysqldate($code) . "'";

        /* if ($_SESSION['userMag'] > 0)
          $cond = "AND app.user_cmd in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";
          else */
        $cond = "";

        $query = "SELECT app.id_cmd,app.bon_cmd,app.frns_cmd,app.bl_cmd_recu,
                 app.date_cmd,app.date_cmd_recu,app.login_cmd,app.user_cmd,f.nom_frns
                FROM t_commande app 
                inner join (select id_frns,nom_frns from t_fournisseur) f on app.frns_cmd=f.id_frns 
                WHERE (app.bon_cmd like '%$code%' OR f.nom_frns LIKE '%$code%' $dt) $cond
                        ORDER BY app.id_cmd DESC";
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

    public function undoCmd() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_cmd']);

        $query = "DELETE FROM t_commande_article WHERE cmd_cmd_art=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


        $query = "DELETE FROM t_commande WHERE id_cmd=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $response = array("status" => 0,
            "datas" => $r,
            "msg" => "");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }

    public function undoCmdArt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_cmd_art']);


        $query = "DELETE FROM t_commande_article WHERE id_cmd_art=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $response = array("status" => 0,
            "datas" => $r,
            "msg" => "");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }

    public function setStat() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $client = $_GET;
        $id = (int) $client['id'];
        $status = (int) $client['s'];
        $query = "";

        if ($status == 0 && $_SESSION['userMag'] > 0) {
            if ($_SESSION['delai_bons'] > 0)
                $query = "UPDATE  t_commande set actif=$status WHERE id_cmd=$id AND DATEDIFF(date(now()),date(date_cmd))>=" . $_SESSION['delai_bons'];
            else
                $query = "UPDATE  t_commande set actif=$status WHERE id_cmd=$id";
        }
        else
            $query = "UPDATE  t_commande set actif=$status WHERE id_cmd=$id ";

        if ($_SESSION['userProfil'] <= 1) {
            $query = "UPDATE  t_commande set actif=$status WHERE id_cmd=$id";
        }

        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);
            $response = array("status" => 0,
                "datas" => $this->showCmdOf($id),
                "msg" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "msg" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    public function setStatca() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $client = $_GET;
        $id = (int) $client['id'];
        $status = (int) $client['s'];
        $daterecu = $client['dr'];
        $query = "";

        if ($daterecu == "") {
            $query = "UPDATE  t_commande_article set bl_art_recu=$status,date_recu_art=null WHERE id_cmd_art=$id ";
        } else {
            $daterecu = isoToMysqldate($daterecu);
            $query = "UPDATE  t_commande_article set bl_art_recu=$status,date_recu_art='$daterecu' WHERE id_cmd_art=$id ";
        }



        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);

            $qk = "SELECT cmd_cmd_art from t_commande_article WHERE id_cmd_art =$id LIMIT 1";
            $rs = $this->mysqli->query($qk) or die($this->mysqli->error . __LINE__);
            $res = $rs->fetch_assoc();
            $id_cmd = intval($res['cmd_cmd_art']);


            $qk = "SELECT id_cmd_art from t_commande_article WHERE cmd_cmd_art =$id_cmd AND bl_art_recu=0";
            $rs = $this->mysqli->query($qk) or die($this->mysqli->error . __LINE__);
            if ($rs->num_rows == 0) {
                $qk = "UPDATE t_commande SET bl_cmd_recu=1, date_cmd_recu='now()'
                    WHERE id_cmd =$id_cmd";
                $rs = $this->mysqli->query($qk) or die($this->mysqli->error . __LINE__);
            } else {
                $qk = "UPDATE t_commande SET bl_cmd_recu=0, date_cmd_recu='now()'
                    WHERE id_cmd =$id_cmd";
                $rs = $this->mysqli->query($qk) or die($this->mysqli->error . __LINE__);
            }

            $resp = array("detailcmd" => $this->showCmdDetailOf($id),
                "cmd" => $this->showCmdOf($id_cmd));

            $response = array("status" => 0,
                "datas" => $resp,
                "msg" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "msg" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    public function setStatc() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $client = $_GET;
        $id = (int) $client['id'];
        $status = (int) $client['s'];
        $query = "";

        $daterecu = $client['dr'];
        $query = "";

        if ($daterecu == "") {
            $query = "UPDATE  t_commande set bl_cmd_recu=$status,date_cmd_recu=null WHERE id_cmd=$id ";
        } else {
            $daterecu = isoToMysqldate($daterecu);
            $query = "UPDATE  t_commande set bl_cmd_recu=$status,date_cmd_recu='$daterecu' WHERE id_cmd=$id ";
        }



        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);

            if ($daterecu == "") {
                $queryu = "UPDATE  t_commande_article set bl_art_recu=$status,date_recu_art=null WHERE cmd_cmd_art=$id";
            } else {
                $queryu = "UPDATE  t_commande_article set bl_art_recu=$status,date_recu_art='$daterecu' WHERE cmd_cmd_art=$id";
            }
            $rs = $this->mysqli->query($queryu) or die($this->mysqli->error . __LINE__);

            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "msg" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    public function getCommandes() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        /*   if ($_SESSION['userMag'] > 0)
          $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_bon_dette,app.bl_dette_regle, app.dette_cmd,app.actif,
          mnt_revient_cmd,app.date_cmd,app.login_cmd,app.user_cmd,f.nom_frns
          FROM t_commande app
          inner join (select id_frns,nom_frns from t_fournisseur) f
          on app.frns_cmd=f.id_frns WHERE app.actif=1
          AND  app.user_cmd in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
          order by app.date_cmd DESC,app.bon_cmd DESC";
          else */
        $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_cmd_recu,app.actif,
            app.date_cmd,app.date_cmd_recu,app.login_cmd,app.user_cmd,f.nom_frns  
            FROM t_commande app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_cmd=f.id_frns WHERE app.actif=1 OR app.id_cmd=1
                order by app.date_cmd DESC,app.bon_cmd DESC";

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

    public function getaCommandes() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_cmd_recu,app.date_cmd_recu,app.actif,
            app.date_cmd,app.date_cmd_recu,app.login_cmd,app.user_cmd,f.nom_frns  
            FROM t_commande app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_cmd=f.id_frns WHERE 1=1
                order by app.date_cmd DESC,app.bon_cmd DESC";

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

    public function getLmCommandes() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        /* if ($_SESSION['userMag'] > 0)
          $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_bon_dette,app.bl_dette_regle,app.dette_cmd,app.actif,
          mnt_revient_cmd,app.date_cmd,app.login_cmd,app.user_cmd,f.nom_frns
          FROM t_commande app
          inner join (select id_frns,nom_frns from t_fournisseur) f
          on app.frns_cmd=f.id_frns
          WHERE app.user_cmd in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
          order by app.date_cmd DESC,app.bon_cmd DESC limit 10";
          else */
        $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_cmd_recu,app.actif,
            app.date_cmd,app.date_cmd_recu,app.login_cmd,app.user_cmd,f.nom_frns   
            FROM t_commande app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_cmd=f.id_frns 
            WHERE 1=1  order by app.date_cmd DESC,app.bon_cmd DESC limit 10";

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

    public function showCmdOf($idcmd) {

        $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_cmd_recu,app.actif,
            app.date_cmd,app.date_cmd_recu,app.login_cmd,app.user_cmd,f.nom_frns   
            FROM t_commande app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_cmd=f.id_frns 
            WHERE app.id_cmd=$idcmd";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $row = $r->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }

    public function loadMore() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $offset = doubleval($this->_request['offset']);

        /*  if ($_SESSION['userMag'] > 0)
          $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_bon_dette,app.bl_dette_regle,app.dette_cmd,app.actif,
          mnt_revient_cmd,app.date_cmd,app.login_cmd,app.user_cmd,f.nom_frns
          FROM t_commande app
          inner join (select id_frns,nom_frns from t_fournisseur) f
          on app.frns_cmd=f.id_frns
          WHERE app.user_cmd in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
          order by app.date_cmd DESC,app.bon_cmd DESC limit 10 offset $offset";
          else */
        $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_cmd_recu,app.actif,
            app.date_cmd,app.date_cmd_recu,app.login_cmd,app.user_cmd,f.nom_frns  
            FROM t_commande app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_cmd=f.id_frns 
            INNER JOIN t_commande_article apa ON app.id_cmd=apa.cmd_cmd_art
                INNER JOIN t_article a ON apa.art_cmd_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art 
            WHERE  1=1 order by app.date_cmd DESC,app.bon_cmd DESC limit 10 offset $offset";

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

    public function queryCommandes() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $qry = $this->esc($this->_request['q']);

        $dt = "";
        if (isDate($qry))
            $dt = " OR date(app.date_cmd)='" . isoToMysqldate($qry) . "'";

        /* if ($_SESSION['userMag'] > 0)
          $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_bon_dette,app.bl_dette_regle,app.dette_cmd,app.actif,
          mnt_revient_cmd,app.date_cmd,app.login_cmd,app.user_cmd,f.nom_frns
          FROM t_commande app
          inner join (select id_frns,nom_frns from t_fournisseur) f
          on app.frns_cmd=f.id_frns
          WHERE (app.bon_cmd LIKE '%$qry%' OR app.code_user_cmd LIKE '%$qry%' OR app.login_cmd LIKE '%$qry%' $dt) AND app.user_cmd in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
          order by app.date_cmd DESC,app.bon_cmd DESC";
          else */
        $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_cmd_recu,app.actif,
            app.date_cmd,app.date_cmd_recu,app.login_cmd,app.user_cmd,f.nom_frns  
            FROM t_commande app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_cmd=f.id_frns 
            WHERE (app.bon_cmd LIKE '%$qry%' OR app.code_user_cmd LIKE '%$qry%' OR app.login_cmd LIKE '%$qry%' $dt) 
             order by app.date_cmd DESC,app.bon_cmd DESC";

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

    public function showCmdDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_cmd']);

        $query = "SELECT a.code_art,a.nom_art,
                        ta.bl_art_recu,ta.date_recu_art, ta.id_cmd_art,ta.qte_cmd_art,ta.prix_cmd_art,ta.date_cmd_art,ap.user_cmd
                          FROM t_commande_article ta 
                         INNER JOIN t_article a ON ta.art_cmd_art=a.id_art
                         INNER JOIN t_commande ap ON ta.cmd_cmd_art=ap.id_cmd
                          WHERE ap.id_cmd=$id ORDER BY a.nom_art ASC";

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

    public function showCmdDetailOf($idCmdDetail) {

        $id = intval($idCmdDetail);

        $query = "SELECT a.code_art,a.nom_art,
                        ta.bl_art_recu,ta.date_recu_art, ta.id_cmd_art,ta.qte_cmd_art,ta.prix_cmd_art,ta.date_cmd_art,ap.user_cmd
                          FROM t_commande_article ta 
                         INNER JOIN t_article a ON ta.art_cmd_art=a.id_art
                         INNER JOIN t_commande ap ON ta.cmd_cmd_art=ap.id_cmd
                          WHERE ta.id_cmd_art=$id ORDER BY a.nom_art ASC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $row = $r->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }

    /* public function showCmdgDetails() {
      if ($this->get_request_method() != "POST") {
      $this->response('', 406);
      }

      $fact = $_POST;

      $id = intval($fact['id_frns']);

      $query = "SELECT a.code_art,a.nom_art,
      ta.id_cmd_art,ta.qte_cmd_art,m.nom_mag,m.code_mag,ap.user_cmd
      FROM t_commande_article ta
      INNER JOIN t_article a ON ta.art_cmd_art=a.id_art
      INNER JOIN t_magasin m ON ta.mag_cmd_art=m.id_mag
      INNER JOIN t_commande ap ON ta.cmd_cmd_art=ap.id_cmd
      WHERE ap.frns_cmd=$id AND ap.mnt_revient_cmd>0 AND ap.bl_bon_dette=1 AND ap.bl_dette_regle=0
      ORDER BY m.nom_mag,a.nom_art ASC";

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
      } */

    public function getEtaCmd() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;


        /* if ($_SESSION['userMag'] != 0)
          $query = "SELECT app.bon_cmd,app.date_cmd,app.mnt_revient_cmd,
          a.nom_art,m.nom_mag,c.nom_cat,f.nom_frns,
          apa.qte_cmd_art,apa.prix_cmd_art
          FROM t_commande app
          INNER JOIN t_commande_article apa ON app.id_cmd=apa.cmd_cmd_art
          INNER JOIN t_magasin m on m.id_mag=apa.mag_cmd_art
          INNER JOIN t_article a ON apa.art_cmd_art=a.id_art
          INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
          INNER JOIN t_fournisseur f ON app.frns_cmd=f.id_frns
          WHERE apa.mag_cmd_art=" . intval($_SESSION['userMag']);
          else */
        $query = "SELECT app.bon_cmd,app.date_cmd,app.date_cmd_recu,
                a.nom_art,c.nom_cat,f.nom_frns,
                apa.qte_cmd_art,apa.prix_cmd_art,apa.bl_art_recu
                FROM t_commande app
                INNER JOIN t_commande_article apa ON app.id_cmd=apa.cmd_cmd_art
                INNER JOIN t_article a ON apa.art_cmd_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                INNER JOIN t_fournisseur f ON app.frns_cmd=f.id_frns
                WHERE 1=1 ";

        if (!empty($search['fournisseur']))
            $query.=" AND f.id_frns=" . intval($search['fournisseur']);

        if (!empty($search['article']))
            $query.=" AND apa.art_cmd_art=" . intval($search['article']);

        if (!empty($search['categorie']))
            $query.=" AND id_cat=" . intval($search['categorie']);

        if (isset($search['bc']) && $search['bc'] != "")
            $query.=" AND app.bl_cmd_recu=" . intval($search['bc']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(app.date_cmd)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(app.date_cmd) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query .= " Order by app.date_cmd DESC,app.bon_cmd DESC,c.nom_cat,a.nom_art";
        //print_r($query);

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

    public function getEtaBc() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;


        /* if ($_SESSION['userMag'] > 0)
          $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_bon_dette,app.bl_dette_regle,app.dette_cmd,app.actif,
          mnt_revient_cmd,app.date_cmd,app.login_cmd,app.user_cmd,f.nom_frns
          FROM t_commande app
          inner join (select id_frns,nom_frns from t_fournisseur) f
          on app.frns_cmd=f.id_frns
          INNER JOIN t_commande_article apa ON app.id_cmd=apa.cmd_cmd_art
          INNER JOIN t_magasin m on m.id_mag=apa.mag_cmd_art
          INNER JOIN t_article a ON apa.art_cmd_art=a.id_art
          INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
          WHERE app.user_cmd in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
          AND 1=1 ";
          else */
        $query = "SELECT app.id_cmd,app.bon_cmd,app.bl_cmd_recu,app.actif,
            app.date_cmd,app.date_cmd_recu,app.login_cmd,app.user_cmd,f.nom_frns  
            FROM t_commande app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_cmd=f.id_frns 
            INNER JOIN t_commande_article apa ON app.id_cmd=apa.cmd_cmd_art
                INNER JOIN t_article a ON apa.art_cmd_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art 
            WHERE  1=1 ";


        if (!empty($search['fournisseur']))
            $query.=" AND f.id_frns=" . intval($search['fournisseur']);

        if (!empty($search['article']))
            $query.=" AND apa.art_cmd_art=" . intval($search['article']);

        if (!empty($search['categorie']))
            $query.=" AND id_cat=" . intval($search['categorie']);

        if (isset($search['bc']) && $search['bc'] != "")
            $query.=" AND app.bl_cmd_recu=" . intval($search['bc']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(app.date_cmd)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(app.date_cmd) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query .= " Order by app.date_cmd DESC,app.bon_cmd DESC";

// $file = fopen("fichier.txt", "a");
//             fwrite($file,$query);
//             fclose($file);

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

    public function insertCommande() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $commande = $_POST;
        $this->isExistBl($commande['bon_cmd']);
        $this->isExistBl($commande['bon_cmd']);


        $column_names = array('bon_cmd', 'frns_cmd');


        $keys = array_keys($commande);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                if ($desired_key == "frns_cmd")
                    $$desired_key = intval($commande[$desired_key]);
                else {
                    $$desired_key = $this->esc($commande[$desired_key]);
                    if ($desired_key == "bon_cmd")
                        $$desired_key = $this->esc($commande[$desired_key]);
                }
            }
            $columns = $columns . $desired_key . ',';
            if ($desired_key == "frns_cmd")
                $values = $values . "" . $$desired_key . ",";
            else
                $values = $values . "'" . $$desired_key . "',";
        }

        $date = $this->esc($commande['date_cmd']);
        $h_ap = date("H:i:s");


        $response = array();
        $query = "INSERT INTO  t_commande (" . trim($columns, ',') . ",date_cmd,login_cmd,user_cmd,code_user_cmd) VALUES(" . trim($values, ',') . ",CONCAT('" . isoToMysqldate($date) . "',' ',time(now())),'" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

        if (!empty($commande)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $commande,
                    "msg" => "commande  cree avec success!");

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

    /* public function apprvae() {
      if ($this->get_request_method() != "POST") {
      $this->response('', 406);
      }

      $fact = $_POST;

      $id = intval($fact['id_sort']);
      try {

      $query = "SELECT mag_sort_src,bon_sort,date_sort FROM t_sortie WHERE id_sort=$id";
      $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "sfts");
      $result = $r->fetch_assoc();

      $mag_source = intval($result['mag_sort_src']);
      $bon_sort_cmd = $result['bon_sort'];
      $date_sort_cmd = $result['date_sort'];
      $magdest = $_SESSION['userMag'];

      $this->isExistBl($bon_sort_cmd);

      $query = "INSERT INTO  t_commande
      (bon_cmd,frns_cmd,tva_cmd,bl_bon_dette,bl_dette_regle,date_cmd,login_cmd,user_cmd,code_user_cmd)
      VALUES('$bon_sort_cmd',1,0,0,1,'" . $date_sort_cmd . "','" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

      if (!$r = $this->mysqli->query($query))
      throw new Exception($this->mysqli->error . __LINE__ . "iita");

      $lastInsertID = $this->mysqli->insert_id;


      $query = "SELECT id_sort_art,qte_sort_art,art_sort_art
      FROM t_sortie_article
      WHERE sort_sort_art=$id order by id_sort_art ASC";
      $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "sftsa");

      if ($r->num_rows > 0) {
      while ($row = $r->fetch_assoc()) {
      $art = $row['art_sort_art'];
      $qt = $row['qte_sort_art'];
      $queri = "INSERT INTO  t_commande_article (cmd_cmd_art, mag_cmd_art, art_cmd_art, qte_cmd_art,date_cmd_art,login_cmd_art,user_cmd_art,code_user_cmd_art)
      VALUES(" . $lastInsertID . "," . $magdest . "," . $art . "," . $qt . ",'" . $date_sort_cmd . "','" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

      if (!$ri = $this->mysqli->query($queri))
      throw new Exception($this->mysqli->error . __LINE__ . "iitaa");

      $queri = "SELECT id_stk FROM t_stock  WHERE art_stk =$art AND mag_stk=$magdest LIMIT 1";
      $ri = $this->mysqli->query($queri) or die($this->mysqli->error . __LINE__ . "sfs");

      if ($ri->num_rows > 0) {
      $queri = "UPDATE t_stock SET qte_stk=qte_stk + $qt WHERE art_stk =$art AND mag_stk=$magdest";
      $ri = $this->mysqli->query($queri) or die($this->mysqli->error . __LINE__ . "uts");
      } else {
      $queri = "INSERT INTO t_stock (art_stk,mag_stk,qte_stk,date_stk) VALUES($art,$magdest,$qt,now())";
      $ri = $this->mysqli->query($queri) or die($this->mysqli->error . __LINE__ . "its");
      }
      }
      }


      $query = "UPDATE t_sortie set bon_vu=1 WHERE id_sort=$id ";
      $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "ubv");

      $response = array("status" => 0,
      "datas" => $r,
      "msg" => "Bon en attente Directement entree en Stock avec success!!!");
      $this->response($this->json($response), 200);
      } catch (Exception $exc) {
      $this->mysqli->rollback();
      $this->mysqli->autocommit(TRUE);
      $response = array("status" => 1,
      "datas" => "",
      "msg" => $exc->getMessage());

      $this->response($this->json($response), 200);
      }
      } */

    public function updateCommande() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $commande = $_POST;
        $id = (int) $commande['id'];

        $this->isExistBlUpdt($commande['approvisionnement']['bon_cmd'], $id);

        $column_names = array('bon_cmd');

        $bon = $this->esc($commande['approvisionnement']['bon_cmd']);
        $dat = isoToMysqldate($this->esc($commande['approvisionnement']['date_cmd']));
        $h_ap = date("H:i:s");

        $query = "UPDATE t_commande SET bon_cmd='$bon',date_cmd='$dat $h_ap' WHERE id_cmd=$id";
        $response = array();

        if (!empty($commande)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $commande,
                    "msg" => "Commande [CMD" . $id . "] modifie avec success!");
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

    public function getArticleCommandees() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $_GET['id'];
        $query = "SELECT asort.bl_art_recu,asort.id_cmd_art,asort.qte_cmd_art,asort.prix_cmd_art,asort.user_cmd_art,asort.date_cmd_art,
           a.nom_art,a.id_art  
            FROM t_commande_article asort 
            inner join t_article a on a.id_art=asort.art_cmd_art
            inner join t_commande sor on sor.id_cmd=asort.cmd_cmd_art
            WHERE sor.id_cmd=$id
                order by asort.id_cmd_art DESC";

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

    public function insertStockCmd() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appstock = $_POST;


        $column_names = array('cmd_cmd_art', 'art_cmd_art', 'qte_cmd_art', 'prix_cmd_art');

        $keys = array_keys($appstock);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = intval($appstock[$desired_key]);
            }
            $columns = $columns . $desired_key . ',';
            $values = $values . "" . $$desired_key . ",";
        }



        $response = array();
        $query = "INSERT INTO  t_commande_article (" . trim($columns, ',') . ",date_cmd_art,login_cmd_art,user_cmd_art,code_user_cmd_art) VALUES(" . trim($values, ',') . ",now(),'" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

        if (!empty($appstock)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $appstock,
                    "msg" => "article enregistre dans la ommande avec success!");

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

    public function deleteCommande() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        $this->isExistBlArticle($id);
        if ($id > 0) {
            $query = "DELETE FROM t_commande WHERE id_cmd = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Commande supprime avec success!");
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

    /* public function insertStockCmd() {
      if ($this->get_request_method() != "POST") {
      $this->response('', 406);
      }

      $appstock = $_POST;


      $column_names = array('cmd_cmd_art', 'mag_cmd_art', 'art_cmd_art', 'qte_cmd_art', 'prix_cmd_art');

      $keys = array_keys($appstock);
      $columns = '';
      $values = '';
      foreach ($column_names as $desired_key) {
      if (!in_array($desired_key, $keys)) {
      $$desired_key = '';
      } else {
      $$desired_key = intval($appstock[$desired_key]);
      }
      $columns = $columns . $desired_key . ',';
      $values = $values . "" . $$desired_key . ",";
      }



      $response = array();
      $query = "INSERT INTO  t_commande_article (" . trim($columns, ',') . ",date_cmd_art,login_cmd_art,user_cmd_art,code_user_cmd_art) VALUES(" . trim($values, ',') . ",now(),'" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

      if (!empty($appstock)) {
      try {
      if (!$r = $this->mysqli->query($query))
      throw new Exception($this->mysqli->error . __LINE__);

      $idmag = intval($appstock['mag_cmd_art']);
      $idart = intval($appstock['art_cmd_art']);
      $qte = intval($appstock['qte_cmd_art']);

      $query = "SELECT id_stk FROM t_stock  WHERE art_stk =$idart AND mag_stk=$idmag LIMIT 1";
      $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

      if ($r->num_rows > 0) {
      $query = "UPDATE t_stock SET qte_stk=qte_stk + $qte WHERE art_stk =$idart AND mag_stk=$idmag";
      $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
      } else {
      $query = "INSERT INTO t_stock (art_stk,mag_stk,qte_stk,date_stk) VALUES($idart,$idmag,$qte,now())";
      $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
      }

      if (!empty($appstock['prix_mini_art_mag']) && !empty($appstock['prix_gros_art_mag'])) {
      $prix_mini = intval($appstock['prix_mini_art_mag']);
      $prix_gros = intval($appstock['prix_gros_art_mag']);

      $query = "INSERT INTO t_prix_article_magasin (art_prix_art_mag,mag_prix_art_mag,prix_mini_art_mag,prix_gros_art_mag,date_prix) VALUES($idart,$idmag,$prix_mini,$prix_gros,now())";
      $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
      }

      $response = array("status" => 0,
      "datas" => $appstock,
      "msg" => "article cmdvisionne avec success!");

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
      } */

    private function isExistBl($bl) {

        $query = "SELECT id_cmd FROM t_commande WHERE bon_cmd ='$bl'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "cette commande existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistBlUpdt($bl, $id) {

        $query = "SELECT id_cmd FROM t_commande WHERE bon_cmd ='$bl' AND id_cmd !=$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "cette commande existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistBlArticle($id) {

        $query = "SELECT id_cmd_art FROM t_commande_article WHERE cmd_cmd_art =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Des articles ont deja ete enregistres sous cette commande ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new commandeController;
    $app->processApp();
}
?>