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
    ->setCellValue('A1', "Date")
    ->setCellValue('B1', "Bon")
    ->setCellValue('C1', "Designation")
    ->setCellValue('D1', "Qte")
    ->setCellValue('E1', "Prix")
    ->setCellValue('F1', "Montant");

$i = 2;


$querymag = "SELECT nom_mag FROM t_magasin WHERE 1=1 ";
if (!empty($search['mg']))
    $querymag.=" AND id_mag=" . intval($search['mg']) . " Limit 1";
else if ($_SESSION['userMag'] != 0)
$query.=" AND id_mag=" . intval($_SESSION['userMag']) . " Limit 1";

$rmag = $Mysqli->query($querymag);
$rowmag = $rmag->fetch_assoc();

$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $rowmag['nom_mag']);
$i=3;
    
$query = "SELECT app.bon_liv_appro,date_format(app.date_appro,'%d %b %Y') as date_appro,
    a.nom_art,m.code_mag,c.nom_cat,f.nom_frns,
    apa.qte_appro_art,apa.prix_appro_art
    FROM t_approvisionnement app
    INNER JOIN t_approvisionnement_article apa ON app.id_appro=apa.appro_appro_art
    INNER JOIN t_magasin m on m.id_mag=apa.mag_appro_art
    INNER JOIN t_article a ON apa.art_appro_art=a.id_art 
    INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
    INNER JOIN t_fournisseur f ON app.frns_appro=f.id_frns
    WHERE  1=1 ";


if (!empty($search['mg']))
    $query.=" AND apa.mag_appro_art=" . intval($search['mg']);
else if ($_SESSION['userMag'] != 0)
$query.=" AND apa.mag_appro_art=" . intval($_SESSION['userMag']);

if (!empty($search['frns']))
    $query.=" AND f.id_frns=" . intval($search['frns']);

if (!empty($search['art']))
    $query.=" AND apa.art_appro_art=" . intval($search['art']);

if (!empty($search['cat']))
    $query.=" AND id_cat=" . intval($search['cat']);

if (isset($search['bc']) && $search['bc'] != "")
    $query.=" AND app.bl_bon_dette=" . intval($search['bc']);

if (!empty($search['d']) && empty($search['f']))
    $query.=" AND date(app.date_appro)='" . isoToMysqldate($search['d']) . "'";

if (!empty($search['f']))
    $query.=" AND date(app.date_appro) between '" . isoToMysqldate($search['d']) . "' 
        AND '" . isoToMysqldate($search['f']) . "'";

$query .= " Order by app.date_appro DESC,c.nom_cat,a.nom_art";

$file = fopen("fichier.txt", "a");
fwrite($file,$query);
fclose($file);

$r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

if ($r->num_rows > 0) {
    $result = array();
    $total = 0;
    $totalqte = 0;
    $totalmont = 0;
    $total = 0;
    $totalqte =0;
    $totalmont =0;
    while ($row = $r->fetch_assoc()) {

        $total +=1;
        $totalqte +=$row['qte_appro_art'];
        $totalmont +=$row['qte_appro_art'] * $row['prix_appro_art'];
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row['date_appro']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $row['bon_liv_appro']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, ucfirst(strtolower($row['nom_art'])));
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $row['qte_appro_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $row['prix_appro_art']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $row['qte_appro_art'] * $row['prix_appro_art']);

        // Add page breaks every 10 rows
        if ($i % 20 == 0) {
            // Add a page break
            $objPHPExcel->getActiveSheet()->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
        }
        $i++;
    }
}

/** End query */


$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('ETAT-DE-STOCK-' . date('Ymd_His'));

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
header('Content-Disposition: attachment;filename="ETAT-DE-STOCK-'.date('Ymd_His').'.xlsx"');
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
