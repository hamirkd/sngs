<?php

session_name('SessSngS');
session_start();

include ("includes/db.php");
$search = $_GET;
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once dirname(__FILE__) . '/../../libs/excel/PHPExcel.php';


/** query */
$query = "SELECT a.code_art,
                 a.nom_art,
                 a.ref_art,
                 a.seuil_art,
                 a.marq_art,
                 a.model_art,
                 a.caract_art, 
                 c.nom_cat,
                 u.nom_unite,
                 p.prix_mini_art,
                 p.prix_gros_art,
                 p.prix_achat_art  
                 FROM t_article a 
                      left join (select * from t_prix_article GROUP BY art_prix_art DESC) p on a.id_art=p.art_prix_art 
                      inner join t_categorie_article c on a.cat_art=c.id_cat
                      inner join t_unite_article u on a.unite_art=u.id_unite
                      ORDER BY c.nom_cat,a.nom_art,a.marq_art,a.model_art";


$r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

/** End query */
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Tongo-Technologies")
        ->setLastModifiedBy(date("Y-m-d"))
        ->setTitle("Articles")
        ->setSubject("Liste des articles")
        ->setDescription("Liste des articles")
        ->setKeywords("Articles")
        ->setCategory("Articles");


$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "CATEGORIE")
        ->setCellValue('B1', "NOM_ART")
        ->setCellValue('C1', "UNIT_VENT")
        ->setCellValue('D1', "PRX_NORM")
        ->setCellValue('E1', "PRX_GROS")
        ->setCellValue('F1', "PRX_ACH")
        ->setCellValue('G1', "SEUIl_STK")
        ->setCellValue('H1', "MARQUE")
        ->setCellValue('I1', "MODEL")
        ->setCellValue('J1', "REFERENCE")
        ->setCellValue('K1', "AUTRES_CARACT");


// Add data
if ($r->num_rows > 0) {
    $i = 2;
    while ($row = $r->fetch_assoc()) {

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row['nom_cat']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $row['nom_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $row['nom_unite']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $row['prix_mini_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $row['prix_gros_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $row['prix_achat_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $row['seuil_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $row['marq_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $row['model_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $row['ref_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $row['caract_art']);

        // Add page breaks every 10 rows
        if ($i % 25 == 0) {
            // Add a page break
            $objPHPExcel->getActiveSheet()->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
        }
        $i++;
    }
}

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('LST-ARTICLES-' . date('Ymd_His'));

$objPHPExcel->getActiveSheet()
        ->getHeaderFooter()->setOddHeader('&C&24&K0000FF&B&U&A');
$objPHPExcel->getActiveSheet()
        ->getHeaderFooter()->setEvenHeader('&C&24&K0000FF&B&U&A');

$objPHPExcel->getActiveSheet()
        ->getHeaderFooter()->setOddFooter('&R&D &T&C&F&LPage &P / &N');
$objPHPExcel->getActiveSheet()
        ->getHeaderFooter()->setEvenFooter('&L&D &T&C&F&RPage &P / &N');



// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Liste-des-articles-'.date('Ymd_His').'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objWriter->save('php://output');
exit;
