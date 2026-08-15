<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");
// Include classes
require_once dirname(__FILE__) . '/../../libs/tbs/tbs_class.php';// Load the TinyButStrong template engine
require_once dirname(__FILE__) . '/../../libs/tbs/tbs_plugin_opentbs.php';// Load the OpenTBS plugin

class dechargeController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getEtatDecharges() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $search = $_POST;

        $condition =  $_SESSION['userMag'] > 0 ? " AND mag_decharge=".$_SESSION['userMag']:" ";
        $query =  "SELECT * from t_decharge decharge WHERE 1=1 ";

        if (!empty($search['magasin'])) $query.=" AND decharge.mag_decharge=" . intval($search['magasin']);
        if (!empty($search['dechargeur'])) $query.=" AND decharge.user_decharge_id='" .$search['dechargeur']."'";
        if (!empty($search['client'])) $query.=" AND decharge.client_id='" .$search['client']."'";

        if (!empty($search['date_deb']) && empty($search['date_fin'])) 
        $query.=" AND date(decharge.date_decharge)='" . isoToMysqldate($search['date_deb']) . "'";

        if (!empty($search['date_fin']))
        $query.=" AND date(decharge.date_decharge) between '" . isoToMysqldate($search['date_deb']) . "' 
            AND '" . isoToMysqldate($search['date_fin']) . "'";
        $query.= $condition." ORDER BY decharge.date_decharge DESC";
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

    
    public function saveDecharge() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $decharge = $_POST;
        $montant = intval($decharge['montant']);
        $motif = $this->esc($decharge['motif']);
        $magasin = !empty($decharge['magasin']) ? intval($decharge['magasin']):$_SESSION['userMag'];
        $date_decharge = (!empty($decharge['date_decharge'])) ? isoToMysqldate($decharge['date_decharge']) : date("Y-m-d");
        $delivre_le = isoToMysqldate($decharge['delivre_le']);
        $expire_le = isoToMysqldate($decharge['expire_le']);
        $type_piece = $decharge['type_piece'];
        $reference_piece = $decharge['reference_piece'];


        
        $user_decharge_id = $decharge['user_decharge_id'];
        $type_decharge = $decharge['type_decharge'];
        $client_id = $decharge['client_id'];
        $type_decharge = $decharge['type_decharge'];
        $nom_prenom_dechargeur = $decharge['nom_prenom_dechargeur'];
        $nom_prenom_client = $decharge['nom_prenom_client'];

        $queryClient = "SELECT max(numero_ordre) as numero_ordre FROM t_decharge WHERE client_id=$client_id";
        $r = $this->mysqli->query($queryClient) or die($this->mysqli->error . __LINE__);
        $numero_ordre = 0;
        if ($r->num_rows > 0) {
            while ($row = $r->fetch_assoc()) {
                $numero_ordre = intval($row ['numero_ordre']);
                $numero_ordre = $numero_ordre + 1;
            }
        } else {
            $numero_ordre = $numero_ordre + 1;
        }
        
        $response = array();
        if ($magasin>0 && !empty($montant) && $montant >= 0) {
            try {
                $this->mysqli->autocommit(FALSE);
                $heure_vnt = date("H:i:s");
                $query = "INSERT INTO  t_decharge(mag_decharge, user_decharge_id,
                client_id, date_decharge, montant, login_decharge, motif_decharge,
                type_decharge, nom_prenom_dechargeur, nom_prenom_client, delivre_le,expire_le,reference_piece,type_piece,numero_ordre) 
                    VALUES($magasin, $user_decharge_id, $client_id , '$date_decharge $heure_vnt',
                    $montant,'" . $_SESSION['userLogin'] . "','" . $motif . "','" . $type_decharge . "',
                    '$nom_prenom_dechargeur','$nom_prenom_client','$delivre_le','$expire_le','$reference_piece', '$type_piece',$numero_ordre)";
                    // echo $query;
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $this->mysqli->commit();
                $this->mysqli->autocommit(TRUE);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Déchargé avec success!");

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
    
    public function ficheDeDecharge() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $decharge = $_POST;
        $query = "SELECT d.*,u.*, m.nom_mag, c.tel_clt FROM t_decharge d
        LEFT JOIN t_magasin m ON m.id_mag=d.mag_decharge
        LEFT JOIN t_client c ON c.id_clt=d.client_id
        LEFT JOIN t_user u ON u.id_user=d.user_decharge_id
        WHERE d.id_decharge=" . $decharge['id_decharge'];
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $row = $result[0];
        } else {
            $response = array("status" => 1,
                "datas" => "",
                "message" => "Cette decharge n'existe pas");
            $this->response($this->json($response), 200);
            return;
        }
        // echo json_encode($versement);
        // Initialize the TBS instance
        $TBS = new clsTinyButStrong; // new instance of TBS
        $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load the OpenTBS plugin
        $template = 'documents/FICHE_DE_DECHARGE_DE_FONDS.docx';
        $TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
        $TBS->MergeField('id_decharge', $row['id_decharge']);
        $TBS->MergeField('mag_decharge', $row['nom_mag']);
        $TBS->MergeField('user_decharge_id', $row['user_decharge_id']);
        $TBS->MergeField('client_id', $row['client_id']);
        $TBS->MergeField('date_decharge_formatted', date('d/m/Y', strtotime($row['date_decharge'])));
        $TBS->MergeField('date_imp', date('d/m/Y à H\hi'));
        $TBS->MergeField('montant', number_format($row['montant'], 0, ',', ' '));
        $TBS->MergeField('login_decharge', $row['login_decharge']);
        $TBS->MergeField('motif_decharge', $row['motif_decharge']);
        // Information de l'emetteur
        $TBS->MergeField('nom_prenom_dechargeur', trim($row['nom_prenom_dechargeur']));
        $TBS->MergeField('nom_dechargeur', trim($row['nom_user']));
        $TBS->MergeField('prenom_dechargeur', trim($row['prenom_user']));
        $TBS->MergeField('poste_dechargeur', trim($row['poste_user']));
        $TBS->MergeField('cnib_dechargeur', trim($row['cnib_user']));
        // Information du client
        $TBS->MergeField('nom_prenom_client', trim($row['nom_prenom_client']));
        $TBS->MergeField('tel_clt', $row['tel_clt']);
        $TBS->MergeField('numero_ordre_client', str_pad($row['numero_ordre'], 4, '0', STR_PAD_LEFT));
        $TBS->MergeField('type_piece', $row['type_piece']);
        $TBS->MergeField('reference_piece', $row['reference_piece']);
        $TBS->MergeField('delivre_le', date('d/m/Y', strtotime($row['delivre_le'])));
        $TBS->MergeField('expire_le', date('d/m/Y', strtotime($row['expire_le'])));


        $TBS->MergeField('mode_paiement', 'Espèces');
        if ($row['type_decharge'] == 'RECEVOIR') {
            $TBS->MergeField('sens', 'reçu');
            $TBS->MergeField('compte', 'pour le compte du');
            $TBS->MergeField('type_decharge', 'Retrait de fonds');
        } else {
            $TBS->MergeField('sens', 'remis');
            $TBS->MergeField('compte', 'au');
            $TBS->MergeField('type_decharge', 'Envoi de fonds');
        }
        $TBS->MergeField('mode_paiement', 'Espèces');

        $response = array(
            "status" => 0,
            "datas" => $TBS->Show(OPENTBS_DOWNLOAD, "bon.docx"),
            "message" => "Impression");
        $this->response($this->json($response), 200);
    }





}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new dechargeController;
    $app->processApp();
}
?>