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

// $_SESSION['userMag']=1;
$condmag = "";
/** query */
// if ($_SESSION['userMag'] != 0)
//     $query = "select s.*,a.nom_art,a.ref_art,a.code_art,ca.id_cat,ca.nom_cat,a.seuil_art,m.nom_mag,
// p.prix_mini_art,p.prix_gros_art,p.prix_max_art                
// from t_stock s
//    inner join t_magasin m on s.mag_stk=m.id_mag
//   inner join t_article a on s.art_stk=a.id_art
//   inner join t_categorie_article ca on a.cat_art=ca.id_cat
//   left join (select * from t_prix_article GROUP BY art_prix_art DESC) p on s.art_stk=p.art_prix_art
//   WHERE s.mag_stk=" . intval($_SESSION['userMag']);
// else
    $query = "select s.*,a.nom_art,a.ref_art,a.code_art,ca.id_cat,ca.nom_cat,a.seuil_art,m.nom_mag,
                p.prix_mini_art,p.prix_gros_art,p.prix_max_art
                from t_stock s
   inner join t_magasin m on s.mag_stk=m.id_mag
  inner join t_article a on s.art_stk=a.id_art
  inner join t_categorie_article ca on a.cat_art=ca.id_cat
  left join (select * from t_prix_article GROUP BY art_prix_art DESC) p on s.art_stk=p.art_prix_art
  WHERE 1=1";

if (!empty($search['mg']))
    $query.=" AND s.mag_stk=" . intval($search['mg']);
else if ($_SESSION['userMag'] != 0)
$query.=" AND s.mag_stk=" . intval($_SESSION['userMag']);

if (!empty($search['art']))
    $query.=" AND s.art_stk=" . intval($search['art']);

if (!empty($search['cat']))
    $query.=" AND ca.id_cat=" . intval($search['cat']);

$query .= " Order by m.nom_mag,ca.nom_cat,a.nom_art";

$file = fopen("fichier.txt", "a");
            fwrite($file,$query);
            fclose($file);

$r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

/** End query */
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Tongo-Technologies")
        ->setLastModifiedBy(date("Y-m-d"))
        ->setTitle("Etat du stock")
        ->setSubject("inventaire stock")
        ->setDescription("inventaire tehorique du stock.")
        ->setKeywords("stock inventaire etat")
        ->setCategory("stock inventaire");


$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()
        ->setCellValue('A1', "ID")
        ->setCellValue('B1', "BOUTIQUE")
        ->setCellValue('C1', "CATEGORIE")
        ->setCellValue('D1', "CODEART")
        ->setCellValue('E1', "DESIGNATION")
        ->setCellValue('F1', "PRIXNORMAL")
        ->setCellValue('G1', "PRIXMINI")
        ->setCellValue('H1', "PRIXMAX")
        ->setCellValue('I1', "QUANTITE")
        ->setCellValue('J1', "QUANTITE PHY")
        ->setCellValue('K1', "SEUIL")
        ->setCellValue('L1', "REFERENCE")
        ->setCellValue('M1', "ADRESSE");


// Add data
if ($r->num_rows > 0) {
    $i = 2;
    while ($row = $r->fetch_assoc()) {

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row['id_stk']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $row['nom_mag']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $row['nom_cat']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $row['code_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $row['nom_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $row['prix_gros_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $row['prix_mini_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $row['prix_max_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $row['qte_stk']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $row['qte_stk']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $row['seuil_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $row['ref_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $row['adr_stk']);



        // Add page breaks every 10 rows
        if ($i % 20 == 0) {
            // Add a page break
            $objPHPExcel->getActiveSheet()->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
        }
        $i++;
    }
}

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Invent-Stock-' . date('Ymd_His'));

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
header('Content-Disposition: attachment;filename="INVENTAIRE-STOCK-'.date('Ymd_His').'.xlsx"');
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
