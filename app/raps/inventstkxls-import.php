<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
  Choisir le fichier d'inventaire:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Importer inventaire" name="submit">
</form>


<?php
class Stock {
    public $nom_mag;
    public $nom_cat;
    public $nom_art;
    public $ecrart;
}
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
$data = [];
foreach($sheetData as $item){
        if($i==0){
        $i=1;
    }
    else{
        // print_r($item);
        $a = new Stock();
        $a->nom_mag = $item['A'];
        $a->nom_cat = $item['B'];
        $a->nom_art = $item['C'];
        $a->ecart = $item['D'];
        $data[]=$a;
        }
    }
    print_r($data);

exit;
?>
</body>
</html>