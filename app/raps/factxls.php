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


$condmag = "";
/** query */
$query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=0 ";

    if (!empty($search['mg']))
        $query.=" AND id_mag=" . intval($search['mg']);

    if (!empty($search['cs']))
        $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

    if (!empty($search['art']))
        $query.=" AND id_art=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND id_cat=" . intval($search['cat']);

    if (!empty($search['clt']))
        $query.=" AND id_clt=" . intval($search['clt']);
    
    if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND bl_tva=" . intval($search['tx']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
        $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
        $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

    $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    $file = fopen("fichier.txt", "a");
    fwrite($file,$query);
    fclose($file);
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
$i=1;
$objPHPExcel->getActiveSheet()->setCellValue('A1', "VENTE AU COMPTA");
$i++;
$objPHPExcel->getActiveSheet()
        ->setCellValue('A'.$i, "BOUTIQUE")
        ->setCellValue('B'.$i, "DATE")
        ->setCellValue('C'.$i, "HEURE")
        ->setCellValue('D'.$i, "FACT")
        ->setCellValue('E'.$i, "DESIGNATION")
        ->setCellValue('F'.$i, "CLIENT")
        ->setCellValue('G'.$i, "PU")
        ->setCellValue('H'.$i, "QUANTITE")
        ->setCellValue('I'.$i, "MONTANT")
        ->setCellValue('I'.$i, "ETAT");

// Add data
$i++;
$montant=0;
if ($r->num_rows > 0) {
    while ($row = $r->fetch_assoc()) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row['nom_mag']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, text_reduit($row['Date_vnt'], 10).($row['bl_fact_grt'] == 1?'G':''));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $row['heure_vnt']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, ucfirst(strtolower($row['code_fact'])));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, ucfirst(strtolower($row['nom_art'])));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, ucwords(strtolower($row['nom_clt'])));
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, number_format($row['pu_theo_vnt'], 0, ',', ' '));
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $row['Qte_vnt']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '));
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, 'Compta');
        $montant+=$row['Qte_vnt'] * $row['pu_theo_vnt'];
        // Add page breaks every 10 rows
        if ($i % 20 == 0) {
            // Add a page break
            $objPHPExcel->getActiveSheet()->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
        }
        $i++;
    }
}
$objPHPExcel->getActiveSheet()->setCellValue('I' . $i, number_format($montant));
$i++;

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('VENTES' . date('Ymd_His'));

$objPHPExcel->getActiveSheet()
        ->getHeaderFooter()->setOddHeader('&C&24&K0000FF&B&U&A');
$objPHPExcel->getActiveSheet()
        ->getHeaderFooter()->setEvenHeader('&C&24&K0000FF&B&U&A');

$objPHPExcel->getActiveSheet()
        ->getHeaderFooter()->setOddFooter('&R&D &T&C&F&LPage &P / &N');
$objPHPExcel->getActiveSheet()
        ->getHeaderFooter()->setEvenFooter('&L&D &T&C&F&RPage &P / &N');

/** VENTE A CREDIT */

$query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=0 ";

    if (!empty($search['mg']))
        $query.=" AND id_mag=" . intval($search['mg']);

    if (!empty($search['cs']))
        $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

    if (!empty($search['art']))
        $query.=" AND id_art=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND id_cat=" . intval($search['cat']);

    if (!empty($search['clt']))
        $query.=" AND id_clt=" . intval($search['clt']);
    
    if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND bl_tva=" . intval($search['tx']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
        $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
        $query.=" AND bl_fact_crdt=0 AND bl_fact_grt=1";

    $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);


$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "VENTE A CREDIT");
$i++;
$objPHPExcel->getActiveSheet()
        ->setCellValue('A'.$i, "BOUTIQUE")
        ->setCellValue('B'.$i, "DATE")
        ->setCellValue('C'.$i, "HEURE")
        ->setCellValue('D'.$i, "FACT")
        ->setCellValue('E'.$i, "DESIGNATION")
        ->setCellValue('F'.$i, "CLIENT")
        ->setCellValue('G'.$i, "PU")
        ->setCellValue('H'.$i, "QUANTITE")
        ->setCellValue('I'.$i, "MONTANT")
        ->setCellValue('I'.$i, "ETAT");
$i++;
// Add data
if ($r->num_rows > 0) {
    // $i = 3;
    while ($row = $r->fetch_assoc()) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row['nom_mag']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, text_reduit($row['Date_vnt'], 10).($row['bl_fact_grt'] == 1?'G':''));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $row['heure_vnt']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, ucfirst(strtolower($row['code_fact'])));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, ucfirst(strtolower($row['nom_art'])));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, ucwords(strtolower($row['nom_clt'])));
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, number_format($row['pu_theo_vnt'], 0, ',', ' '));
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $row['Qte_vnt']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '));
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, 'Crédit');
        $montant+=$row['Qte_vnt'] * $row['pu_theo_vnt'];

        // Add page breaks every 10 rows
        if ($i % 20 == 0) {
            // Add a page break
            $objPHPExcel->getActiveSheet()->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
        }
        $i++;
    }
}
$objPHPExcel->getActiveSheet()->setCellValue('I' . $i, number_format($montant));
$i++;

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('VENTES' . date('Ymd_His'));

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
