<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class retourArticleController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getEtatRetourArticles() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condition =  $_SESSION['userMag'] > 0 ? " AND mag_retour=".$_SESSION['userMag']:" ";
        $query =  "SELECT *,td.nom_art as article from t_retour_article retour
                        INNER JOIN t_article td ON retour.art_retour_art=td.id_art
                        WHERE 1=1 ";

        if (!empty($search['article'])) $query.=" AND retour.art_retour_art=" . intval($search['article']);
        if (!empty($search['magasin'])) $query.=" AND retour.mag_retour=" . intval($search['magasin']);
        if (!empty($search['retourneur'])) $query.=" AND retour.user_retour_id='" .$search['retourneur']."'";

        if (!empty($search['date_deb']) && empty($search['date_fin'])) 
        $query.=" AND date(retour.date_retour)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
        $query.=" AND date(retour.date_retour) between '" . isoToMysqldate($search['date_deb']) . "' 
            AND '" . isoToMysqldate($search['date_fin']) . "'";
        $query.= $condition." ORDER BY retour.date_retour DESC";
        // echo $query;

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

    
    public function saveRetourArticle() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $retourArticle = $_POST;
        $quantite = intval($retourArticle['quantite']);
        $montant = intval($retourArticle['montant']);
        $article = intval($retourArticle['article']);
        $motif = $this->esc($retourArticle['motif']);
        $magasin = !empty($retourArticle['magasin']) ? intval($retourArticle['magasin']):$_SESSION['userMag'];
        $date_retour = (!empty($retourArticle['date_retour'])) ? isoToMysqldate($retourArticle['date_retour']) : date("Y-m-d");
        $response = array();
        if (!empty($article) && $magasin>0 &&!empty($quantite) &&$quantite>0 &&   !empty($montant) && $montant >= 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_retour_article (
                     	art_retour_art,
                        quantite,
                        montant,
                        date_retour,
                        user_retour_id,
                        login_retour,
                        mag_retour,
                        motif) 
                     VALUES(" . $article . ",
                          " . $quantite . ", 
                          " . $montant . ", 
                              '$date_retour $heure_vnt',
                              
                          " . $_SESSION['userId'] . ",
                         '" . $_SESSION['userLogin'] . "',
                         " . $magasin . ",
                             '" . $motif . "')";
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Article retourné avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $this->mysqli->rollback();
                $this->mysqli->autocommit(TRUE);
                $response = array("status" => 1,
                    "datas" => "$query",
                    "message" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } else {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Attention donnees incorrectes!");

            $this->response($this->json($response), 200);
        }
    }





}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new retourArticleController;
    $app->processApp();
}
?>