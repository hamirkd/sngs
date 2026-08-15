<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");
require_once ("api-class/authentification.php");

class demandeController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getEtatFacturePrix() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;
        

        $condition =  $_SESSION['userMag'] > 0 ? " AND tf.mag_fact=".$_SESSION['userMag']:" ";
        $query =  'SELECT tf.*,'."COALESCE(c.nom_clt,'-')".' as nom_clt,m.nom_mag,sum((tfp.prix_propose - pu_theo_vnt) * tv.qte_vnt) as reduction  FROM t_facture_prix tfp join t_vente tv on tv.id_vnt=tfp.id_vntp join
         t_facture_vente tf on tf.id_fact=tv.facture_vnt join t_client c on tf.clnt_fact=c.id_clt join t_magasin m on m.id_mag=tf.mag_fact WHERE 1=1 ';

        if (!empty($search['date_deb']) && empty($search['date_fin'])) 
            $query.=" AND date(tfp.date_enr)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
            $query.=" AND date(tfp.date_enr) between '" . isoToMysqldate($search['date_deb']) . "' 
            AND '" . isoToMysqldate($search['date_fin']) . "'";

        if (!empty($search['mag_paiement']))
            $query.="tf.mag_fact=".$search['mag_paiement'];

        $query.= $condition." GROUP BY tf.id_fact ORDER BY tfp.date_enr DESC LIMIT 30";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                

                $query =  'SELECT DISTINCT tf.*, tfp.action  FROM t_facture_prix tfp join t_vente tv on tv.id_vnt=tfp.id_vntp join
                    t_facture_vente tf on tf.id_fact=tv.facture_vnt WHERE tf.id_fact='.$row['id_fact'];
                $r2 = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                
                if ($r2->num_rows > 1) {
                    $row['action'] = 'VALIDATION PARTIELLE';
                } else if ($r2->num_rows == 1) {
                    $prix = $r2->fetch_assoc();
                    if (intval($prix['action']) == 0) {
                        $row['action'] = 'EN ATTENTE DE VALIDATION';
                    } else {
                        $row['action'] = 'DEJA CONFIRME';
                    }
                }
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

    public function getDemande() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $query =  'SELECT DISTINCT tfp.*,tf.*,tv.pu_theo_vnt  FROM t_facture_prix tfp join t_vente tv on tv.id_vnt=tfp.id_vntp join
         t_facture_vente tf on tf.id_fact=tv.facture_vnt WHERE tf.id_fact= '.$_POST['id_fact'];

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "message" => "recuperer avec success");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "La liste est vide");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }
    

    
    public function saveFacturePrix()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $factPrix = $_POST;

        $user_err = $_SESSION['nom_prenom_user'];
        foreach($factPrix as $data) {
            $id_vntp = $data['id_vntp'];
            $prix_ancien = $data['prix_ancien'];
            $prix_propose = $data['prix_propose'];
            $query = "INSERT INTO t_facture_prix (id_vntp, prix_ancien, prix_propose, date_enr, user_enr, last_updated)
             VALUES ($id_vntp, $prix_ancien,$prix_propose, now(), '$user_err', now())";
            $r = $this->mysqli->query($query);
        }

        if (!$r) {
            $response = [
                "status" => 1,
                "message" => $this->mysqli->error
            ];
        } else {
            $response = [
                "status" => 0,
                "datas" => "",
                "message" => "Demande initiée avec succès !"
            ];
        }

        $this->response($this->json($response), 200);
    }
    
       
    public function validationFacturePrix() {
        if ($this->get_request_method() != "POST") {
        $this->response('', 406);
        }
        if ($_SESSION['droitValidationPrixFacture'] != 1) {
            $response = array("status" => 1,
            "datas" => "",
            "message" => "Vous n'êtes pas autorisé à valider les soumissions de facture");
            $this->response($this->json($response), 200);
            return;
        }
        $this->mysqli->autocommit(FALSE);
        $fact = $_POST;

        $id = intval($fact['id_fact_prix']);
        $action = intval($fact['action']);

        try {
            if ($action == 1) {    
                $query = "SELECT * FROM t_facture_prix tfp join t_vente tv on tv.id_vnt=tfp.id_vntp WHERE id_facp=$id";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $factp = $r->fetch_assoc();
                // Modification du prix au niveau de la vente
                $query = 'UPDATE t_vente SET pu_theo_vnt='.$factp['prix_propose'].',mnt_theo_vnt=Qte_vnt*'.$factp['prix_propose']
                .',marge_vnt=('.$factp['prix_propose'].'-pu_theo_achat)*Qte_vnt WHERE id_vnt='.$factp['id_vntp'];
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $this->verificationErreur();
                $query = 'UPDATE t_facture_vente SET mnt_theo_fact=(select SUM(mnt_theo_vnt) FROM t_vente WHERE facture_vnt=id_fact)
                WHERE id_fact='.$factp['facture_vnt'];
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $this->verificationErreur();
                $query = 'UPDATE t_facture_vente SET crdt_fact=(select SUM(mnt_theo_vnt) FROM t_vente WHERE facture_vnt=id_fact)
                WHERE id_fact='.$factp['facture_vnt'];
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                $this->verificationErreur();
            }
            $query = "UPDATE t_facture_prix SET action=$action,last_updated=now(),user_valid='".$_SESSION['nom_prenom_user']."' WHERE id_facp=$id";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            $this->verificationErreur();
            $this->mysqli->autocommit(TRUE);
            $response = array("status" => 0,
                "datas" => $r,
                "message" => $action==1?"Autoriser avec success!!!":"Rejeter avec success!!!");
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

    public function showFactureDetails() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fact = $_POST;

        $code_fact = $fact['code_fact'];

        $query = "SELECT a.code_art,a.nom_art,v.pu_theo_achat,
                        v.id_vnt,f.id_fact,f.bl_fact_grt,f.bl_bic,f.bl_tva,v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,f.caissier_fact
                        FROM t_vente v 
                        INNER JOIN t_article a ON v.article_vnt=a.id_art
                        INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                        WHERE f.code_fact='$code_fact' ORDER BY a.nom_art ASC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();

            while ($row = $r->fetch_assoc()) {
                $id_vntp = $row['id_vnt'];
                $query = "SELECT * FROM t_facture_prix WHERE id_vntp = $id_vntp order by id_facp desc limit 1";
                
                $r2 = $this->mysqli->query($query);
                if ($r2->num_rows > 0) {
                    $rowp = $r2->fetch_assoc();
                    $row['prix_propose'] = intval($rowp['prix_propose']);
                    $row['id_facp'] = intval($rowp['id_facp']);
                    $row['action'] = intval($rowp['action']);
                    $row['user_valid'] = $rowp['user_valid'];
                    $row['user_enr'] = $rowp['user_enr'];
                } else {
                    $row['prix_propose'] = intval($row['pu_theo_vnt']);
                }
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
    public function verificationErreur() {
        if ($this->mysqli->affected_rows !== 1) {
        throw new Exception("Mise à jour de nombre de lignes inattendu " 
            . $this->mysqli->affected_rows);
    }
}





}

session_name('SessSngS');
session_start();
authentication();
if (isset($_SESSION['userId'])) {
    $app = new demandeController;
    $app->processApp();
}
?>