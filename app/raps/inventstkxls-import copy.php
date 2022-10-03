<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
  Choisir le fichier d'inventaire:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Importer inventaire" name="submit">
</form>


<?php
if(!isset($_FILES['fileToUpload'])){
    // echo $_POST['file'];
    exit;
}
session_name('SessSngS');
session_start();

include ("includes/db.php");
$search = $_GET;
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once dirname(__FILE__) . '/../../libs/excel/PHPExcel.php';


$inputFileName = $_FILES["fileToUpload"]["tmp_name"];
// $helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');
$spreadsheet = PHPExcel_IOFactory::load($inputFileName);
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
// var_dump($sheetData);
// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
// $objWriter->save('php://output');
// print_r($sheetData);
$i=0;
$inventaire_date_code = date('Ym');
foreach($sheetData as $item){
        if($i==0){
        $i=1;
        $header['nom_mag']='A';
        $header['nom_cat']='B';
        $header['code_art']='C';
        $header['nom_art']='D';
        $header['prix_gros_art']='E';
        $header['prix_mini_art']='F';
        $header['prix_max_art']='G';
        $header['qte_stk']='H';
        $header['seuil_art']='';
        $header['ref_art']='I';
        $header['adr_stk']='J';
        print_r($header);
        $query = "INSERT INTO t_inventaire SET code_inventaire='".$inventaire_date_code."',mag=(SELECT art_stk FROM t_stock WHERE id_stk=".$item['A'].")";
        $Mysqli->query($query) or die($this->mysqli->error . __LINE__);
    }
    else{
        // print_r($header);
        $query = "UPDATE t_stock SET qte_stk=".$item['J']." WHERE id_stk=".$item['A'];
        $Mysqli->query($query) or die($this->mysqli->error . __LINE__);
        if($item['I']>$item['J']){
        $query = "INSERT INTO t_sortie_article SET"
            ." art_sort_art=(SELECT art_stk FROM t_stock WHERE id_stk=".$item['A'].")"
            .", sort_mag=(SELECT mag_stk FROM t_stock WHERE id_stk=".$item['A'].")"
            .", qte_sort_art=".($item['I']-$item['J'])
            .",inventaire='".$inventaire_date_code."'";
            $Mysqli->query($query) or die($this->mysqli->error . __LINE__);
            }
        if($item['I']<$item['J']){
            $query = "INSERT INTO t_sortie_article SET"
            ." art_appro_art=(SELECT art_stk FROM t_stock WHERE id_stk=".$item['A'].")"
            .", mag_appro_art=(SELECT mag_stk FROM t_stock WHERE id_stk=".$item['A'].")"
            .", qte_appro_art=".($item['J']-$item['I'])
            .",inventaire='".$inventaire_date_code."'";
            }
            $Mysqli->query($query) or die($this->mysqli->error . __LINE__);
        }
    }

exit;
?>
</body>
</html>