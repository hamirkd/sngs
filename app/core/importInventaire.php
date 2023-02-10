<?php
class Stock {
    public $code;
    public $nom_mag;
    public $nom_cat;
    public $nom_art;
    public $qte_the;
    public $qte_phy;
    public $ecart;
    public $erreur;
}


if(!isset($_FILES['fichierImporte'])){
    $response = array("status" => 1,
    "datas" => $_FILES,
    "message" => "Veuillez choisir un fichier !");
    // $this->response($this->json($response), 200);
}

session_name('SessSngS');
session_start();

include (dirname(__FILE__) ."/../raps/includes/db.php");
$search = $_GET;
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once dirname(__FILE__) . '/../../libs/excel/PHPExcel.php';

// $response = array("status" => 1,
// "datas" => $_FILES,
// "message" => "Veuillez choisir un fichier !");
// print_r($response);
$inputFileName = $_FILES["fichierInventaire"]["tmp_name"];
$spreadsheet = PHPExcel_IOFactory::load($inputFileName);
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
$i=0;
$response = array();
$data = [];

        foreach($sheetData as $item){
            if($i==0){
                $i=1;
                if($item['A']!='code'||$item['B']!='nom_mag'||$item["C"]!='nom_cat'||$item["D"]!='nom_art'||$item["E"]!='qte_the'||$item["F"]!='qte_phy'){
                    $response = array("status" => -1,
                                "datas" => "-1",
                                "message" => "Veuillez verifier l'ordre des champs, code|nom_mag|nom_cat|nom_art|qte_the|qte_phy");
                    echo json_encode($response);
                    return;
                }
            }
            else{
                // print_r($item);
                $a = new Stock();
                $a->code = $item['A'];
                $a->nom_mag = $item['B'];
                $a->nom_cat = $item['C'];
                $a->nom_art = $item['D'];
                $a->qte_the = $item['E'];
                $a->qte_phy = $item['F'];
                $a->ecart = $a->qte_phy-$a->qte_the;
                $a->erreur = true;
                $data[]=$a;
                if($a->code>0){
                    $a->erreur = false;
                    continue;}
                // recupérer le code des articles
                $query = "SELECT id_art FROM `t_article` WHERE `id_art`=$a->code";

                $r = $Mysqli->query($query) or die($Mysqli->error . __LINE__);

                if ($r->num_rows > 0) {
                $result = $r->fetch_assoc();
                $a->code = $result['id_art'];
                $a->erreur = false;
                } else {
                    $a->code = 0;
                }
                
                }
            }
        $response = array("status" => 0,
        "datas" => $data,
        "message" => "Veuillez verifier avec l'importation!");
        
        echo json_encode($response);
        // $this->response($this->json($response), 200);
