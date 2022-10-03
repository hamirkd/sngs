<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class approvisionnementController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getApprovisionnement() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.frns_appro,app.bl_bon_dette,app.dette_appro,mnt_revient_appro,app.date_appro,app.login_appro,f.nom_frns  FROM t_approvisionnement app inner join (select id_frns,nom_frns from t_fournisseur) f on app.frns_appro=f.id_frns WHERE app.id_appro =$id LIMIT 1";
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
                "msg" => "Mauvais identifiant de l'approvisionnement");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant de l' approvisionnement !");
        $this->response($this->json($response), 200);
    }

    public function getApproByCode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $code = $this->esc($_GET['vr']);
        $dt = "";
        if (isDate($code))
            $dt = " OR app.date_appro='" . isoToMysqldate($code) . "'";

        if ($_SESSION['userMag'] > 0)
            $cond = "AND app.user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";
        else
            $cond = "";

        $query = "SELECT app.id_appro,app.bon_liv_appro,app.frns_appro,app.bl_bon_dette,
                app.dette_appro,mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns,app.actif
                FROM t_approvisionnement app 
                inner join (select id_frns,nom_frns from t_fournisseur) f on app.frns_appro=f.id_frns 
                WHERE (app.bon_liv_appro like '%$code%' OR f.nom_frns LIKE '%$code%' $dt) $cond
                        ORDER BY app.id_appro DESC";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['openclose'] = $row['actif'];
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

    public function undoAppro() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_appro']);


        $query = "DELETE FROM t_dette_fournisseur WHERE bon_dette_frns=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $query = "DELETE FROM t_approvisionnement_article WHERE appro_appro_art=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


        $query = "DELETE FROM t_approvisionnement WHERE id_appro=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $response = array("status" => 0,
            "datas" => $r,
            "msg" => "");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }

    public function undoApproArt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_appro_art']);


        $query = "DELETE FROM t_approvisionnement_article WHERE id_appro_art=$id ";

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
                $query = "UPDATE  t_approvisionnement set actif=$status WHERE id_appro=$id AND DATEDIFF(date(now()),date(date_appro))>=" . $_SESSION['delai_bons'];
            else
                $query = "UPDATE  t_approvisionnement set actif=$status WHERE id_appro=$id";
        }
        else
            $query = "UPDATE  t_approvisionnement set actif=$status WHERE id_appro=$id ";

        if ($_SESSION['userProfil'] <= 1) {
            $query = "UPDATE  t_approvisionnement set actif=$status WHERE id_appro=$id";
        }

        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);
            $response = array("status" => 0,
                "datas" => $this->geApprovisionnementOf($id),
                "msg" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "msg" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    public function geApprovisionnementOf($idappro) {

        $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            WHERE id_appro=$idappro";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $row = $r->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }

    public function getApprovisionnements() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle, app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns WHERE app.actif=1 
             AND  app.user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
                 order by app.date_appro DESC,app.bon_liv_appro DESC";
        else
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns WHERE app.actif=1 OR app.id_appro=1
                order by app.date_appro DESC,app.bon_liv_appro DESC";

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

    public function getaApprovisionnements() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            WHERE app.user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
             order by app.date_appro DESC,app.bon_liv_appro DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['openclose'] = $row['actif'];
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

    public function getLmApprovisionnements() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            WHERE app.user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
             order by app.date_appro DESC,app.bon_liv_appro DESC limit 50";
        else
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            WHERE 1=1  order by app.date_appro DESC,app.bon_liv_appro DESC limit 50";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['openclose'] = $row['actif'];
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

    public function loadMore() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $offset = doubleval($this->_request['offset']);

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            WHERE app.user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
             order by app.date_appro DESC,app.bon_liv_appro DESC limit 50 offset $offset";
        else
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            WHERE 1=1 order by app.date_appro DESC,app.bon_liv_appro DESC limit 50 offset $offset";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['openclose'] = $row['actif'];
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

    public function queryApprovisionnements() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $qry = $this->esc($this->_request['q']);

        $dt = "";
        if (isDate($qry))
            $dt = " OR date(app.date_appro)='" . isoToMysqldate($qry) . "'";

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            WHERE (app.bon_liv_appro LIKE '%$qry%' OR app.code_user_appro LIKE '%$qry%' OR app.login_appro LIKE '%$qry%' $dt) AND app.user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
             order by app.date_appro DESC,app.bon_liv_appro DESC";
        else
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            WHERE (app.bon_liv_appro LIKE '%$qry%' OR app.code_user_appro LIKE '%$qry%' OR app.login_appro LIKE '%$qry%' $dt) 
             order by app.date_appro DESC,app.bon_liv_appro DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['openclose'] = $row['actif'];
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

    public function showApproDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_appro']);

        $query = "SELECT a.code_art,a.nom_art,
                         ta.id_appro_art,ta.qte_appro_art,ta.prix_appro_art,ta.date_appro_art,m.nom_mag,m.code_mag,ap.user_appro
                          FROM t_approvisionnement_article ta 
                         INNER JOIN t_article a ON ta.art_appro_art=a.id_art
                         INNER JOIN t_magasin m ON ta.mag_appro_art=m.id_mag
                         INNER JOIN t_approvisionnement ap ON ta.appro_appro_art=ap.id_appro
                          WHERE ap.id_appro=$id ORDER BY m.nom_mag,a.nom_art ASC";

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

    public function showApprogDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_frns']);

        $query = "SELECT a.code_art,a.nom_art,
                         ta.id_appro_art,ta.qte_appro_art,m.nom_mag,m.code_mag,ap.user_appro
                          FROM t_approvisionnement_article ta 
                         INNER JOIN t_article a ON ta.art_appro_art=a.id_art
                         INNER JOIN t_magasin m ON ta.mag_appro_art=m.id_mag
                         INNER JOIN t_approvisionnement ap ON ta.appro_appro_art=ap.id_appro
                          WHERE ap.frns_appro=$id AND ap.mnt_revient_appro>0 AND ap.bl_bon_dette=1 AND ap.bl_dette_regle=0 
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
    }

    public function getEtaAppro() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;


        if ($_SESSION['userMag'] != 0)
            $query = "SELECT app.bon_liv_appro,app.date_appro,app.mnt_revient_appro,
                a.nom_art,m.nom_mag,c.nom_cat,f.nom_frns,
                apa.qte_appro_art,apa.prix_appro_art
                FROM t_approvisionnement app
                INNER JOIN t_approvisionnement_article apa ON app.id_appro=apa.appro_appro_art
                INNER JOIN t_magasin m on m.id_mag=apa.mag_appro_art
                INNER JOIN t_article a ON apa.art_appro_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                INNER JOIN t_fournisseur f ON app.frns_appro=f.id_frns
                WHERE apa.mag_appro_art=" . intval($_SESSION['userMag']);
        else
            $query = "SELECT app.bon_liv_appro,app.date_appro,app.mnt_revient_appro,
                a.nom_art,m.nom_mag,c.nom_cat,f.nom_frns,
                apa.qte_appro_art,apa.prix_appro_art
                FROM t_approvisionnement app
                INNER JOIN t_approvisionnement_article apa ON app.id_appro=apa.appro_appro_art
                INNER JOIN t_magasin m on m.id_mag=apa.mag_appro_art
                INNER JOIN t_article a ON apa.art_appro_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                INNER JOIN t_fournisseur f ON app.frns_appro=f.id_frns
                WHERE 1=1 ";

        if (!empty($search['magasin']))
            $query.=" AND apa.mag_appro_art=" . intval($search['magasin']);

        if (!empty($search['fournisseur']))
            $query.=" AND f.id_frns=" . intval($search['fournisseur']);

        if (!empty($search['article']))
            $query.=" AND apa.art_appro_art=" . intval($search['article']);

        if (!empty($search['categorie']))
            $query.=" AND id_cat=" . intval($search['categorie']);

        if (isset($search['bc']) && $search['bc'] != "")
            $query.=" AND app.bl_bon_dette=" . intval($search['bc']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(app.date_appro)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(app.date_appro) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query .= " Order by app.date_appro DESC,app.bon_liv_appro DESC,m.nom_mag,c.nom_cat,a.nom_art";


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

    public function getEtaBl() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;


        if ($_SESSION['userMag'] > 0)
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            INNER JOIN t_approvisionnement_article apa ON app.id_appro=apa.appro_appro_art
                INNER JOIN t_magasin m on m.id_mag=apa.mag_appro_art
                INNER JOIN t_article a ON apa.art_appro_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art 
            WHERE app.user_appro in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
            AND 1=1 ";
        else
            $query = "SELECT app.id_appro,app.bon_liv_appro,app.bl_bon_dette,app.bl_dette_regle,app.dette_appro,app.actif,
            mnt_revient_appro,app.date_appro,app.login_appro,app.user_appro,f.nom_frns  
            FROM t_approvisionnement app 
            inner join (select id_frns,nom_frns from t_fournisseur) f 
            on app.frns_appro=f.id_frns 
            INNER JOIN t_approvisionnement_article apa ON app.id_appro=apa.appro_appro_art
                INNER JOIN t_magasin m on m.id_mag=apa.mag_appro_art
                INNER JOIN t_article a ON apa.art_appro_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art 
            WHERE  1=1 ";

        if (!empty($search['magasin']))
            $query.=" AND apa.mag_appro_art=" . intval($search['magasin']);

        if (!empty($search['fournisseur']))
            $query.=" AND f.id_frns=" . intval($search['fournisseur']);

        if (!empty($search['article']))
            $query.=" AND apa.art_appro_art=" . intval($search['article']);

        if (!empty($search['categorie']))
            $query.=" AND id_cat=" . intval($search['categorie']);

        if (isset($search['bc']) && $search['bc'] != "")
            $query.=" AND app.bl_bon_dette=" . intval($search['bc']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(app.date_appro)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(app.date_appro) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query .= " Order by app.date_appro DESC,app.bon_liv_appro DESC";



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

    public function insertApprovisionnement() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $approvisionnement = $_POST;
        $this->isExistBl($approvisionnement['bon_liv_appro']);
//$this->isExistBl("(K) " . $approvisionnement['bon_liv_appro']);
        $this->isExistBl($approvisionnement['bon_liv_appro']);


        $column_names = array('bon_liv_appro', 'mnt_revient_appro', 'bl_bon_dette', 'frns_appro');


        $keys = array_keys($approvisionnement);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                if ($desired_key == "mnt_revient_appro" || $desired_key == "dette_appro" || $desired_key == "frns_appro")
                    $$desired_key = intval($approvisionnement[$desired_key]);
                else {
                    $$desired_key = $this->esc($approvisionnement[$desired_key]);
                    if ($desired_key == "bon_liv_appro")
//$$desired_key = "(K) " . $this->esc($approvisionnement[$desired_key]);
                        $$desired_key = $this->esc($approvisionnement[$desired_key]);
                }
            }
            $columns = $columns . $desired_key . ',';
            if ($desired_key == "mnt_revient_appro" || $desired_key == "frns_appro")
                $values = $values . "" . $$desired_key . ",";
            else
                $values = $values . "'" . $$desired_key . "',";
        }

        $date = $this->esc($approvisionnement['date_appro']);
        $h_ap = date("H:i:s");


        $response = array();
        $query = "INSERT INTO  t_approvisionnement (" . trim($columns, ',') . ",date_appro,login_appro,user_appro,code_user_appro) VALUES(" . trim($values, ',') . ",CONCAT('" . isoToMysqldate($date) . "',' ',time(now())),'" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

        if (!empty($approvisionnement)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                if (!empty($approvisionnement['bl_bon_dette']) && $approvisionnement['bl_bon_dette'] == 1 && intval($approvisionnement['dette_appro']) != 0) {
                    $lastInsertID = $this->mysqli->insert_id;
                    $avance = intval($approvisionnement['dette_appro']);
                    $frns = intval($approvisionnement["frns_appro"]);
                    $query = "INSERT INTO  t_dette_fournisseur (bon_dette_frns,frns_dette,mnt_paye_dette_frns,date_dette_frns,caissier_login_frns,caissier_dette_frns,code_caissier_frns) 
                         VALUES($lastInsertID,$frns,$avance,now(),'" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";
                    if (!$r = $this->mysqli->query($query))
                        throw new Exception($this->mysqli->error . __LINE__);

                    $query = "UPDATE t_approvisionnement SET som_verse_dette=$avance  WHERE id_appro=$lastInsertID";
                    if (!$r = $this->mysqli->query($query))
                        throw new Exception($this->mysqli->error . __LINE__);

                    $query = "SELECT a.som_verse_dette,a.mnt_revient_appro FROM t_approvisionnement a  WHERE a.id_appro =$lastInsertID LIMIT 1";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                    $result = $r->fetch_assoc();

                    if (intval($result['som_verse_dette']) == intval($result['mnt_revient_appro'])) {
                        $query = "UPDATE t_approvisionnement SET bl_dette_regle=1 WHERE id_appro=$lastInsertID";
                        if (!$r = $this->mysqli->query($query))
                            throw new Exception($this->mysqli->error . __LINE__);
                    }
                }

                if (!empty($approvisionnement['bl_bon_dette']) && $approvisionnement['bl_bon_dette'] == 1) {
                    if (intval($approvisionnement['dette_appro']) != 0)
                        $som_verse = intval($approvisionnement["mnt_revient_appro"]) - $avance;
                    else
                        $som_verse = intval($approvisionnement["mnt_revient_appro"]);

                    $query = "UPDATE t_fournisseur SET dette_en_cours_frns=dette_en_cours_frns+" . $som_verse . " WHERE id_frns=" . intval($approvisionnement["frns_appro"]);
                    if (!$r = $this->mysqli->query($query))
                        throw new Exception($this->mysqli->error . __LINE__);
                }
                $response = array("status" => 0,
                    "datas" => $approvisionnement,
                    "msg" => "approvisionnement  cree avec success!");

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

    public function apprvae() {
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
            $bon_sort_appro = $result['bon_sort'];
            $date_sort_appro = $result['date_sort'];
            $magdest = $_SESSION['userMag'];

            $this->isExistBl($bon_sort_appro);

            $query = "INSERT INTO  t_approvisionnement 
                (bon_liv_appro,frns_appro,tva_appro,bl_bon_dette,bl_dette_regle,date_appro,login_appro,user_appro,code_user_appro,actif) 
                VALUES('$bon_sort_appro',1,0,0,1,'" . $date_sort_appro . "','" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "',0)";

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
                    $queri = "INSERT INTO  t_approvisionnement_article (appro_appro_art, mag_appro_art, art_appro_art, qte_appro_art,date_appro_art,login_appro_art,user_appro_art,code_user_appro_art) 
    VALUES(" . $lastInsertID . "," . $magdest . "," . $art . "," . $qt . ",'" . $date_sort_appro . "','" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

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


            $query = "UPDATE t_sortie set bon_vu=1,actif=0 WHERE id_sort=$id ";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__ . "ubv");
            
            // $file = fopen("fichier.txt", "a");
            // fwrite($file,$query);
            // fclose($file);
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
    }

    public function updateApprovisionnement() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $approvisionnement = $_POST;
        $id = (int) $approvisionnement['id'];

        $this->isExistBlUpdt($approvisionnement['approvisionnement']['bon_liv_appro'], $id);

        $column_names = array('bon_liv_appro');

        $bon = $this->esc($approvisionnement['approvisionnement']['bon_liv_appro']);
        $dat = isoToMysqldate($this->esc($approvisionnement['approvisionnement']['date_appro']));
        $h_ap = date("H:i:s");

        $query = "UPDATE t_approvisionnement SET bon_liv_appro='$bon',date_appro='$dat $h_ap' WHERE id_appro=$id";
        $response = array();

        if (!empty($approvisionnement)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $approvisionnement,
                    "msg" => "Approvisionnement [APRO" . $id . "] modifie avec success!");
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

    public function deleteApprovisionnement() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        $this->isExistBlArticle($id);
        if ($id > 0) {
            $query = "DELETE FROM t_approvisionnement WHERE id_appro = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Approvisionnement supprime avec success!");
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

    public function insertStockAppro() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appstock = $_POST;


        $column_names = array('appro_appro_art', 'art_appro_art', 'qte_appro_art', 'prix_appro_art');

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

$file = fopen("fichier.txt", "a");
            
            

        $response = array();
        $query = "INSERT INTO  t_approvisionnement_article (" . trim($columns, ',') . ",date_appro_art,mag_appro_art,login_appro_art,user_appro_art,code_user_appro_art) VALUES(" . trim($values, ',') . ",now(),'" . $_SESSION['userMag'] . "','" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";
        fwrite($file,$query);
        if (!empty($appstock)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $idmag = $_SESSION['userMag'];
                $idart = intval($appstock['art_appro_art']);
                $qte = intval($appstock['qte_appro_art']);

                $query = "SELECT id_stk FROM t_stock  WHERE art_stk =$idart AND mag_stk=$idmag LIMIT 1";
                fwrite($file,$query);
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                fwrite($file,$query);
                if ($r->num_rows > 0) {
                    $query = "UPDATE t_stock SET qte_stk=qte_stk + $qte WHERE art_stk =$idart AND mag_stk=$idmag";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                } else {
                    $query = "INSERT INTO t_stock (art_stk,mag_stk,qte_stk,date_stk) VALUES($idart,$idmag,$qte,now())";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }
                fwrite($file,$query);
                if (!empty($appstock['prix_mini_art_mag']) && !empty($appstock['prix_gros_art_mag'])) {
                    $prix_mini = intval($appstock['prix_mini_art_mag']);
                    $prix_gros = intval($appstock['prix_gros_art_mag']);

                    $query = "INSERT INTO t_prix_article_magasin (art_prix_art_mag,mag_prix_art_mag,prix_mini_art_mag,prix_gros_art_mag,date_prix) VALUES($idart,$idmag,$prix_mini,$prix_gros,now())";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }

                $response = array("status" => 0,
                    "datas" => $appstock,
                    "msg" => "article approvisionne avec success!");

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
            fclose($file);
    }

    private function isExistBl($bl) {

        $query = "SELECT id_appro FROM t_approvisionnement WHERE bon_liv_appro ='$bl'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Cet Borderau de livraison existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistBlUpdt($bl, $id) {

        $query = "SELECT id_appro FROM t_approvisionnement WHERE bon_liv_appro ='$bl' AND id_appro !=$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Cet Borderau de livraison existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistBlArticle($id) {

        $query = "SELECT id_appro_art FROM t_approvisionnement_article WHERE appro_appro_art =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Des articles ont deja ete enregistres sous ce bordereau ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new approvisionnementController;
    $app->processApp();
}
?>