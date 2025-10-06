<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");
require_once ("api-class/authentification.php");

class productionController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getsrtnv() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        $query = "SELECT COUNT(*) as srtnv 
            FROM t_production WHERE vu=0 limit 1";

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

    public function getsrtnba() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT COUNT(*) as srtnba 
            FROM t_production WHERE mag_prod_dst=" . $_SESSION['userMag'] . " AND bon_vu=0 and rejeter=0   limit 1";

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
    // Bon de stock rejeté
    public function getsrtnbarj() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        $query = "SELECT COUNT(*) as srtnbarj 
            FROM t_production WHERE mag_prod_src=" . $_SESSION['userMag'] . " AND rejeter=1   limit 1";

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

    public function getProdie() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT prod.id_prod,prod.bon_prod,prod.mag_prod_dst as id_mag,prod.mag_prod_dst,prod.date_prod,prod.login_prod,m.nom_mag,prod.rejeter,prod.actif  FROM t_production prod inner join (select id_mag,nom_mag from t_magasin) m on prod.mag_prod_dst=m.id_mag WHERE prod.id_prod =$id LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $result = $r->fetch_assoc();

                $response = array("status" => 0,
                    "datas" => $result,
                    "message" => "");
                $this->response($this->json($response), 200);
            }
            $response = array("status" => 1,
                "datas" => "",
                "message" => "Mauvais identifiant de la prodie");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Veuillez fournir un identifiant de la prodie !");
        $this->response($this->json($response), 200);
    }

    public function getProdByCode() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $code = $this->esc($_GET['vr']);

        $cond = "";


        $dt = "";

        if ($_SESSION['userMag'] > 0)
            $cond = " AND prod.mag_prod_src=" . $_SESSION['userMag'];

        if (isDate($code))
            $dt = " OR prod.date_prod='" . isoToMysqldate($code) . "'";

        $query = "SELECT prod.id_prod,prod.bon_prod,prod.date_prod,prod.login_prod,m.nom_mag  FROM t_production prod inner join (select id_mag,nom_mag from t_magasin) m on prod.mag_prod_dst=m.id_mag WHERE (prod.bon_prod  like '%$code%' $cond) $dt";
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

    public function undoProd() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_prod']);


        $query = "DELETE FROM t_dette_fournisseur WHERE bon_dette_frns=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $query = "DELETE FROM t_production_article WHERE prod_prod_art=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);


        $query = "DELETE FROM t_production WHERE id_prod=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $response = array("status" => 0,
            "datas" => $r,
            "message" => "");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }

    public function undoProdArt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_prod_art']);


        $query = "DELETE FROM t_production_article WHERE id_prod_art=$id ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $response = array("status" => 0,
            "datas" => $r,
            "message" => "");
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
                $query = "UPDATE t_production set actif=$status WHERE id_prod=$id AND DATEDIFF(date(now()),date(date_prod))>=" . $_SESSION['delai_bons'];
            else
                $query = "UPDATE t_production set actif=$status WHERE id_prod=$id";
        }
        else
            $query = "UPDATE t_production set actif=$status WHERE id_prod=$id";

        if ($_SESSION['userProfil'] <= 1) {
            $query = "UPDATE t_production set actif=$status WHERE id_prod=$id";
        }


        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);
            $response = array("status" => 0,
                "datas" => "",
                "message" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "message" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    public function getProductions() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        
        $offset = "";
        $condValid = "";
        if (isset($this->_request['offset']) && doubleval($this->_request['offset'])) {
            $offset = doubleval($this->_request['offset']);
            $offset = " offset $offset";
        }
        if (isset($this->_request['date_confirm']) && boolval($this->_request['date_confirm'])) {
            $condValid = " AND date_confirm IS NULL";
        }
        $cond = $_SESSION['userMag'] > 0 ? " AND prod.prod_mag_src =  ".$_SESSION["userMag"] : '';
        $query = "SELECT prod.*,m.nom_mag, m.code_mag FROM t_production prod 
        inner join t_magasin m on m.id_mag=prod_mag_src  
        WHERE 1=1 $cond $condValid order by prod.id_prod DESC LIMIT 50 $offset";

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


    public function showProdDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval(isset($fact['prod_prod_art']) ? $fact['prod_prod_art'] : $fact['id_prod']);

        $query = "SELECT a.code_art,a.nom_art,art_prod_art,prod_prod_art,pa.prod_mag_src,
                         pa.id_prod_art,pa.qte_prod_art,pa.cout_prod,pa.date_enr,m.nom_mag,m.code_mag,ap.user_prod,
                         pa.code_user_confirm,pa.date_confirm
                          FROM t_production_article pa 
                         INNER JOIN t_article a ON pa.art_prod_art=a.id_art
                         INNER JOIN t_magasin m ON pa.prod_mag_src=m.id_mag
                         INNER JOIN t_production ap ON pa.prod_prod_art=ap.id_prod
                          WHERE ap.id_prod=$id ORDER BY m.nom_mag,a.nom_art ASC";

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

    
    public function insertStockProdForConfirmation() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appstock = $_POST;

        $column_names = array('prod_prod_art', 'art_prod_art', 'qte_prod_art', 'prod_mag_src', 'cout_prod');

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
        $query = "INSERT INTO  t_production_article (" . trim($columns, ',') . ",user_prod_art,code_user_prod_art) VALUES(" . trim($values, ',') . "," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

        if (!empty($appstock)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $appstock,
                    "message" => "article production en confirmation avec success!");

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

    
    
    public function insertStockProdConfirmation() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $appstock = $_POST;
        // Recuperation des lignes qui n'ont pas ete valider
        $query = "SELECT id_prod_art FROM t_production_article WHERE date_confirm IS NOT NULL AND id_prod_art =".$appstock['id_prod_art'];
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Cette ligne a déjà été validée");
            $this->response($this->json($response), 200);
        }
        if ($_SESSION['droitConfirmationProduction'] != 1) {
            $response = array(
                "status" => 0,
                "datas" => "-1",
                "message" => "Vous n'avez pas les droits pour confirmer une production, veuillez contacter le controlleur de gestion");
            $this->response($this->json($response), 200);
        } 

        $response = array();

        if (!empty($appstock)) {

            try {
                $idmag = $appstock['prod_mag_src'];
                $idart = intval($appstock['art_prod_art']);
                $qte = intval($appstock['qte_prod_art']);
                $coutProd = intval($appstock['cout_prod']);

                $query = "SELECT * FROM t_stock  WHERE art_stk =$idart AND mag_stk=$idmag LIMIT 1";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $article = null;
                if ($r->num_rows > 0) {
                    $article = $r->fetch_assoc();
                }
                if ($article == null) {
                        $response = array("status" => -1,
                        "datas" => "",
                        "message" => "L'article n'existe pas");
                    $this->response($this->json($response), 200); 
                }
                else if ($article['qte_stk'] >= $qte) {
                    // Mise à jour de la quantité
                    $query = "UPDATE t_stock SET qte_stk=qte_stk - $qte WHERE art_stk =$idart AND mag_stk=$idmag";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                    // Confirmation des articles
                    $query = "UPDATE  t_production_article SET code_user_confirm='" . $_SESSION['userCode'] . "', date_confirm=now(), cout_prod=$coutProd WHERE id_prod_art=".$appstock['id_prod_art'];
                    if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                    $id = (int) $appstock['art_prod_art'];
                    
                    $appstock['code_user_confirm'] = $_SESSION['userCode'];
                    $response = array("status" => 0,
                        "datas" => $appstock,
                        "message" => "article approvisionne avec success!");

                    $this->response($this->json($response), 200);

                } else {
                    $response = array("status" => 1,
                                "datas" => "",
                                "message" => "Pas de quantité disponible, quantité disponible : " . $article['qte_stk']);
                    $this->response($this->json($response), 200); 
                }
                
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
    

    public function setConfirmationProduction() {

        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $prod = $_POST;
        $id = (int) $prod['id_prod'];
        $chargeProd = $prod['charge_prod'];

        $query = "";
        if (!isset($_SESSION['droitConfirmationProduction']) || $_SESSION['droitConfirmationProduction'] != 1) {
            $response = array(
                "status" => 0,
                "datas" => "-1",
                "message" => "Vous n'avez pas les droits pour confirmer une production, veuillez contacter le controlleur de gestion");
            $this->response($this->json($response), 200);
        }
        // Vérification si la production n'a pas été validé
        $query = "SELECT * FROM t_production WHERE id_prod = $id AND date_confirm IS NOT NULL";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array(
                "status" => 0,
                "datas" => "-1",
                "message" => "Cette production est déjà validée");
            $this->response($this->json($response), 200);
        }

        // Verification si il existe des articles qui n'ont pas été confirmé
        $query = "SELECT id_prod_art FROM t_production_article WHERE date_confirm IS NULL AND prod_prod_art =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Il existe des lignes qui n'ont pas été validées");
            $this->response($this->json($response), 200);
        }
        // vérification si il existe des articles matières premiere sur le bon
        $query = "SELECT sum(cout_prod * qte_prod_art) as coutAchat, count(*) as qte FROM t_production_article WHERE prod_prod_art =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        // Récupération du coût d'achat
        $row = $r->fetch_assoc();
        if ($row['qte']<=0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce bon ne contient pas de matière première. Veuillez l'ajouter avant de continuer");
            $this->response($this->json($response), 200);
        }
        $coutAchat = intval($row['coutAchat']);
        $coutProdTotal = intval($coutAchat) + intval($chargeProd);

        $appstock = $this->getProductionOf($id);

        $idmag = $appstock['prod_mag_src'];
        $idart = intval($appstock['prod_art']);
        $qte = intval($appstock['qte']);

        $query = "SELECT id_stk FROM t_stock  WHERE art_stk =$idart AND mag_stk=$idmag LIMIT 1";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        if ($r->num_rows > 0) {
            // Mise à jour de la quantité
            $query = "UPDATE t_stock SET qte_stk=qte_stk + $qte WHERE art_stk =$idart AND mag_stk=$idmag";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        } else {
            $query = "INSERT INTO t_stock (art_stk,mag_stk,qte_stk,date_stk) VALUES($idart,$idmag,$qte,now())";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        }

        // $query = "UPDATE  t_production set actif=0, code_user_confirm='" . $_SESSION['userCode'] . "', date_confirm=now() WHERE id_prod=$id ";
        $query = "UPDATE  t_production set actif=0, code_user_confirm='" . $_SESSION['userCode'] . "', date_confirm=now(), cout_achat=$coutAchat, charge_prod=$chargeProd, cout_prod_total=$coutProdTotal WHERE id_prod=$id ";

        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);
            $response = array("status" => 0,
                "datas" => $this->getProductionOf($id),
                "message" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "message" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    public function getProductionOf($id) {

        $query = "SELECT * FROM t_production WHERE id_prod = $id";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $row = $r->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }


    public function getInventaire() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT `code_inventaire`, `date_enr`, `mag`, `login_user`,(SELECT nom_mag FROM t_magasin WHERE id_mag=mag) as nom_mag FROM `t_inventaire` WHERE 1=1 
            WHERE mag=".$_SESSION['userMag'];
        else
            $query = "SELECT `code_inventaire`, `date_enr`, `mag`, `login_user` FROM `t_inventaire` WHERE 1=1";

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
 

    public function getaProductions() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] != 0)
            $query = 'SELECT prod.id_prod,prod.vu,prod.bon_prod,prod.actif,
           prod.date_prod,prod.mag_prod_dst as id_mag,prod.login_prod,m.nom_mag,
           mm.nom_mag as nom_mag_source,mm.code_mag as code_mag_source,
           prod.rejeter,prod.motif
            FROM t_production prod 
            inner join t_magasin m on m.id_mag=mag_prod_dst
            inner join t_magasin mm on mm.id_mag=mag_prod_src
            WHERE prod.mag_prod_src =  '.$_SESSION["userMag"].'
            order by prod.id_prod DESC limit 100';
        else
            $query = "SELECT prod.id_prod,prod.vu,prod.bon_prod,prod.actif,
           prod.date_prod,prod.mag_prod_dst as id_mag,prod.login_prod,m.nom_mag,prod.motif,
           mm.nom_mag as nom_mag_source,mm.code_mag as code_mag_source  
            FROM t_production prod 
            inner join t_magasin m on m.id_mag=mag_prod_dst
            inner join t_magasin mm on mm.id_mag=mag_prod_src  
            WHERE 1=1 order by prod.id_prod DESC limit 100";

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

    public function getaProductionsRejeter() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] != 0)
            $query = "SELECT prod.id_prod,prod.vu,prod.bon_prod,prod.bon_vu,prod.actif,
           prod.date_prod,prod.mag_prod_dst as id_mag,prod.login_prod,m.nom_mag,
           prod.rejeter,prod.motif  
            FROM t_production prod 
            inner join t_magasin m on m.id_mag=mag_prod_dst  
            WHERE prod.user_prod in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
            AND prod.rejeter=1 
            order by prod.id_prod DESC limit 100";
        else
            $query = "SELECT prod.id_prod,prod.vu,prod.bon_vu,prod.bon_prod,prod.actif,
           prod.date_prod,prod.mag_prod_dst as id_mag,prod.login_prod,m.nom_mag,
           prod.rejeter,prod.motif   
            FROM t_production prod 
            inner join t_magasin m on m.id_mag=mag_prod_dst  
            WHERE 1=1 AND prod.rejeter=1  order by prod.id_prod DESC limit 100";
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

    public function getProductionsAttentes() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] != 0)
            $query = "SELECT prod.id_prod,prod.bon_vu,prod.bon_prod,
           prod.date_prod,prod.mag_prod_src as id_mag,prod.rejeter,prod.motif,prod.login_prod,m.nom_mag,s.nom_mag as nom_mag_prod_dst  
            FROM t_production prod 
            inner join t_magasin m on m.id_mag=mag_prod_src 
            inner join t_magasin s on s.id_mag=mag_prod_dst  
            WHERE prod.mag_prod_dst =" . $_SESSION['userMag'] . "
                AND prod.bon_vu=0 order by prod.id_prod DESC";
        else
            $query = "SELECT prod.id_prod,prod.bon_vu,prod.bon_prod,
           prod.date_prod,prod.mag_prod_src as id_mag,prod.rejeter,prod.motif,prod.login_prod,m.nom_mag,s.nom_mag as nom_mag_prod_dst  
            FROM t_production prod 
            inner join t_magasin m on m.id_mag=mag_prod_src
            inner join t_magasin s on s.id_mag=mag_prod_dst    
            WHERE prod.bon_vu=0 order by prod.id_prod DESC";
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

    public function getArticleProductions() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $_GET['id'];
        $query = "SELECT aprod.id_prod_art,aprod.qte_prod_art,aprod.code_user_prod_art,
           a.nom_art,a.id_art  
            FROM t_production_article aprod 
            inner join t_production a on a.id_art=aprod.art_prod_art
            inner join t_production sor on sor.id_prod=aprod.prod_prod_art
            WHERE sor.id_prod=$id
                order by aprod.id_prod_art DESC";

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
    

    public function searchBon() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $numerobon = $_POST['numerobon'];

        if ($_SESSION['userMag'] != 0)
            $query = "SELECT prod.id_prod,prod.bon_vu,prod.bon_prod,
            prod.date_prod,prod.mag_prod_src as id_mag,prod.rejeter,prod.motif,prod.login_prod,m.nom_mag,s.nom_mag as nom_mag_prod_dst  
            FROM t_production prod 
            inner join t_magasin m on m.id_mag=mag_prod_src 
            inner join t_magasin s on s.id_mag=mag_prod_dst  
            WHERE prod.mag_prod_dst =" . $_SESSION['userMag'] . "
                AND  prod.bon_prod like '%$numerobon%'  order by prod.id_prod DESC limit 20";
        else
            $query = "SELECT prod.id_prod,prod.bon_vu,prod.bon_prod,
            prod.date_prod,prod.mag_prod_src as id_mag,prod.rejeter,prod.motif,prod.login_prod,m.nom_mag,s.nom_mag as nom_mag_prod_dst  
            FROM t_production prod 
            inner join t_magasin m on m.id_mag=mag_prod_src
            inner join t_magasin s on s.id_mag=mag_prod_dst    
            WHERE prod.bon_prod like '%$numerobon%' order by prod.id_prod DESC limit 20";
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

    public function getEtaProd() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        
        $cond = $_SESSION['userMag'] > 0 ? " AND prod.prod_mag_src =  ".$_SESSION["userMag"] : '';
        


        $query = "SELECT prod.bon_prod,prod.date_prod,
            a.nom_art,m.nom_mag,c.nom_cat,mr.nom_mag as nom_mag_src,
            apa.qte_prod_art
            FROM t_production prod
            INNER JOIN t_magasin m on m.id_mag=prod.mag_prod_dst
            INNER JOIN t_article a ON prod.prod_art=a.id_art
            INNER JOIN t_magasin mr on mr.id_mag=prod.mag_prod_src 
            INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
            WHERE 1=1 $cond ";
        

        if (!empty($search['magasin']))
            $query.=" AND prod.mag_prod_src=" . intval($search['magasin']);

        if (!empty($search['article']))
            $query.=" AND prod.prod_art=" . intval($search['article']);

        if (!empty($search['categorie']))
            $query.=" AND id_cat=" . intval($search['categorie']);

        if (!empty($search['date_deb']) && empty($search['date_fin']))
            $query.=" AND date(prod.date_prod)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(prod.date_prod) between '" . isoToMysqldate($search['date_deb']) . "' 
                AND '" . isoToMysqldate($search['date_fin']) . "'";

        $query .= " Order by prod.date_prod DESC,m.nom_mag,c.nom_cat,a.nom_art";

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

    public function insertProd() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $prod = $_POST;
        /* $this->isExistProd($prod['bon_prod']); */




        $keys = array_keys($prod);
        $columns = '';
        $values = '';
       

        $date_s = $this->esc($prod['date_prod']);
        $date_prod = $this->esc($prod['date_prod']); // suppose format YYYY-MM-DD

        // Date du jour (YYYY-MM-DD)
        $today = date('Y-m-d');

        if (isoToMysqldate($date_prod) === $today) {
            // Même jour -> on prend l'heure et minutes courantes
            $date_s = "CONCAT('" . isoToMysqldate($date_prod) . " ', DATE_FORMAT(NOW(), '%H:%i'))";
            $numerobon = "CONCAT('PROD','" . date('Ymd') . "', DATE_FORMAT(NOW(), '%H%i'))";
        } else {
            // Autre jour -> on met 00 00
            $numerobon = date('Ymd', isoToMysqldate($date_prod)) . '0000';

            $date_s = "CONCAT('PROD','" . isoToMysqldate($date_prod) . "', ' 00:00')";
        }
        $response = array();
        $this->isExistProd2($numerobon);
        $query = "INSERT INTO t_production (bon_prod,prod_mag_src,date_prod,login_prod,user_prod,code_user_prod,prod_art,qte,nom_art)
         VALUES($numerobon," . $_SESSION['userMag'] . ",$date_s,'" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "',
         ".$prod['prod_art'].",".$prod['qte'].",'".$prod['nom_art']."')";
        if (!empty($prod)) {
            try {
                if (!$r = $this->mysqli->query($query)) {
                    throw new Exception($this->mysqli->error . __LINE__);
                    $this->mysqli->rollback();
                    $this->mysqli->autocommit(TRUE);
                }

                $response = array("status" => 0,
                    "datas" => $prod,
                    "message" => "production  créée avec success!");

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
        else
            $this->response('', 204);
    }

    
    public function deleteProd() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        $this->isExistProdArticle($id);
        if ($id > 0) {
            $query = "DELETE FROM t_production WHERE id_prod = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Bon de Prodie [BS-" . $id . "] supprime avec success!");
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

    public function insertStockProd() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $prodstock = $_POST;


        $column_names = array('prod_prod_art', 'art_prod_art', 'qte_prod_art');

        $keys = array_keys($sprodstock);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = intval($sprodstock[$desired_key]);
            }
            $columns = $columns . $desired_key . ',';
            $values = $values . "" . $$desired_key . ",";
        }



        $response = array();
        $query = "INSERT INTO  t_production_article (" . trim($columns, ',') . ",user_prod_art,code_user_prod_art) VALUES(" . trim($values, ',') . ",'" . $_SESSION['userLogin'] . "'," . $_SESSION['userId'] . ",'" . $_SESSION['userCode'] . "')";

        if (!empty($prodstock)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $idmag = intval($_SESSION['userMag']);
                $idart = intval($prodstock['art_prod_art']);
                $qte = intval($prodstock['qte_prod_art']);

                $query = "UPDATE t_stock SET qte_stk=qte_stk - $qte WHERE art_stk =$idart AND mag_stk=$idmag";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $prodstock,
                    "message" => "article enregistre dans le bon de production avec success!");

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

    private function isExistProd($bl) {

        $query = "SELECT id_prod FROM t_production WHERE bon_prod ='$bl'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce Bon de prodie existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }
    

    private function isExistProd2($bl) {

        
        $query = "SELECT id_prod FROM t_production WHERE bon_prod =$bl";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce Bon de prodie existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    
    }

    private function isExistProdUpdt($bl, $id) {
        
        $query = "SELECT id_prod FROM t_production WHERE bon_prod ='$bl' AND id_prod !=$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        
        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce bon de prodie existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistProdArticle($id) {

        $query = "SELECT id_prod_art FROM t_production_article WHERE prod_prod_art =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
                $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ces articles existent dans le stock");
                $this->response($this->json($response), 200);
        }
        
    }

    public function bonvusrt() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $id = intval($fact['id_prod']);
        try {

            $query = "UPDATE t_production set bon_vu=1 WHERE id_prod=$id ";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $response = array("status" => 0,
                "datas" => $r,
                "message" => "Bon en attente d'entree de Stock Marquer comme recu avec success!!!");
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

    public function tvusrt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        try {

            $query = "UPDATE t_production set vu=1 WHERE vu=0 ";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $response = array("status" => 0,
                "datas" => $r,
                "message" => "Toutes les prodies de stock Marquer comme vu avec success!!!");
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

    public function tbonvusrt() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        try {

            $query = "UPDATE t_production set bon_vu=1 WHERE bon_vu=0 ";

            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $response = array("status" => 0,
                "datas" => $r,
                "message" => "Tous les Bons en attente d'entree de stock Marquer comme vu avec success!!!");
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
authentication();
if (isset($_SESSION['userId'])) {
    $prod = new productionController;
    $prod->processApp();
}


?>