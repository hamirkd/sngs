<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class paiementController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    
    
    public function savePaiementFromMobile() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $paiement = $_POST;
        $telephoneEnvoi =isset($paiement['expediteur']) ? ($paiement['expediteur']) : "EXTERNE";
        $paiement = $this->filtrage($paiement['address'], $paiement['body'], $paiement['time']);
        if (!$paiement) {
            $paiement = $this->filtrage2($_POST['address'], $_POST['body'], $_POST['time']);
        }
        return $paiement;
        $paiement['telephoneEnvoi'] = $telephoneEnvoi;
        $montant =isset($paiement['montant']) ? intval($paiement['montant']) : null;
        $type_paiement = isset($paiement['type_paiement']) ? $this->esc($paiement['type_paiement']) : $this->esc('ORANGEMONEY');
        $code = isset($paiement['code']) ? $this->esc($paiement['code']) : '';
        $nom_expediteur = isset($paiement['nom_expediteur']) ? $this->esc($paiement['nom_expediteur']):'';
        $date_paiement = (!empty($paiement['date_paiement'])) ? ($paiement['date_paiement']) : date("Y-m-d H:i:s");
        $magasin = !empty($paiement['mag_paiement']) ? intval($paiement['mag_paiement']):'NULL';
        $used_user = isset($paiement['used_paiement_code_user']) ? $this->esc($paiement['used_paiement_code_user']) : '';
        $ref_facture_vente = isset($paiement['ref_facture_vente']) ? $this->esc($paiement['ref_facture_vente']) : '';
        $telephone = isset($paiement['telephone']) ? $this->esc($paiement['telephone']) : '';
        $response = array();
        //file_put_contents("paiement.json", json_encode($_POST) . PHP_EOL, FILE_APPEND); 

        if (!empty($code) && !empty($montant) && $montant >= 0) {
            try {
            $query =  "SELECT *  from t_paiement WHERE code = '$code' ";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $row = $r->fetch_assoc();
                if ($paiement['telephoneEnvoi']!== $row['login_paiement']) {
                    $query =  "UPDATE t_paiement SET login_paiement='$telephoneEnvoi' WHERE code = '$code' ";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
                }
                $response = array("status" => 1,
                "datas" => "Ce code existe deja",
                "message" => "Ce code existe deja");
                $this->response($this->json($response), 400);
            }
            file_put_contents("paiement_success.json", json_encode($paiement) . PHP_EOL, FILE_APPEND); 

        $query.=  " AND date_paiement >= DATE_SUB(now(), INTERVAL 7 DAY)";

                $query = "INSERT INTO  t_paiement (
                     	type_paiement,
                     	code,
                        montant,
                        date_paiement,
                        nom_expediteur,
                        login_paiement,
                        used_paiement_code_user,
                        ref_facture_vente,
                        telephone,
                        mag_paiement,
                        created_at,
                        updated_at) 
                     VALUES('$type_paiement',
                            '$code', 
                            $montant, 
                            '$date_paiement',
                            '$nom_expediteur',
                            '$telephoneEnvoi',
                            '$used_user',
                            '$ref_facture_vente',
                            '$telephone',
                            $magasin, now(), now())";
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Deposer avec success!");
                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                //echo $query;
                $response = array("status" => 1,
                    "datas" => "$query",
                    "message" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        } else {
            if ($type_paiement == 'ORANGEMONEY') {
                file_put_contents("paiement_erreur.json", json_encode($paiement) . PHP_EOL, FILE_APPEND); 
            }
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Attention donnees incorrectes!");

            $this->response($this->json($response), 200);
        }
    }
    function filtrage2($type_paiement, $text, $time) {
        if (strpos($type_paiement, "OrangeMoney") === false) {
            return null;
        }
        $timestamp_seconds = $time / 1000;
        $date = (new DateTime())->setTimestamp($timestamp_seconds)->format("Y-m-d H:i:s");
        // Création d'un objet standard
        $paiement = array();
        $paiement['date_paiement'] = $date;
        // Extraction du montant
        $debutMontant = strlen("Vous avez recu un paiement de ");
        $finMontant = strpos($text, ".00 FCFA du numero ");
        $paiement['montant'] = str_replace(',', '', substr($text, $debutMontant, $finMontant - $debutMontant));
        $text = substr($text, strpos($text, ".00 FCFA du numero ") + strlen(".00 FCFA du numero "));
        $paiement['telephone'] = substr($text, 0, strpos($text, "."));
        $text = substr($text, strpos($text, 'Reference: remarks. Trans ID: ') + strlen('Reference: remarks. Trans ID: '));
            
        $paiement['code'] = substr($text, 0, strpos($text, ". Votre solde est de"));
        $paiement['type_paiement'] = strtoupper(trim($type_paiement));
        return $paiement;
    }
    function filtrage($type_paiement, $text, $time) {
        if (strpos($type_paiement, "OrangeMoney") === false) {
            return null;
        }
        $timestamp_seconds = $time / 1000;
        $date = (new DateTime())->setTimestamp($timestamp_seconds)->format("Y-m-d H:i:s");

        // Création d'un objet standard
        $paiement = array();
        $paiement['date_paiement'] = $date;
        // Extraction du montant
        $posMontant = strpos($text, "recu ") + strlen("recu ");
        $posFCFA = strpos($text, " FCFA", $posMontant);
        $paiement['montant'] = str_replace(',', '', substr($text, $posMontant, $posFCFA - $posMontant));

        // Extraction du numéro de téléphone
        $posTel = strpos($text, "du ") + strlen("du ");
        $posVirgule = strpos($text, ",", $posTel);
        $paiement['telephone'] = substr($text, $posTel, $posVirgule - $posTel);
        if (strlen($paiement['telephone']) > 10) {
            return null;
        }

        // Extraction du nom
        $posNom = $posVirgule + 1;
        $posPoint = strpos($text, ".", $posNom);
        $paiement['nom_expediteur'] = trim(substr($text, $posNom, $posPoint - $posNom));

        // Extraction du Trans ID
        //$posTransId = strpos($text, "Trans id: ") + strlen("Trans id: ");
        
        // Extraction du Trans ID (adapté aux différents formats)
        
        // Extraction du Trans ID
        preg_match('/Trans (?:ID|id): ([\w\d\.\-]+)/', $text, $matches);
        $paiement['code']  = isset($matches[1]) ? $matches[1] : null;
        $lastDotPosition = strrpos($paiement['code'], '. ');

        if ($lastDotPosition !== false) {
            $paiement['code'] = trim(substr($paiement['code'], 0, $lastDotPosition));
        }
        //$paiement['code'] = trim(substr($text, $posTransId));
        $paiement['type_paiement'] = strtoupper(trim($type_paiement));
        return $paiement;
    }





}


$app = new paiementController;
$app->processApp();

?>