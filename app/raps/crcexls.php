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
if ($_SESSION['userMag'] > 0)
    $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

$query = "SELECT  
             SUM(fv.crdt_fact-fv.som_verse_crdt) as mnt_crce,
             date(fv.date_fact) as date_fact,
            time(fv.date_fact) as heure_fact,
            cl.code_clt,
            cl.nom_clt,
            cl.tel_clt,
            m.nom_mag
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             inner join t_magasin m on fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_crdt=1 AND bl_fact_grt=0 AND fv.bl_crdt_regle=0  AND fv.sup_fact=0 AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt)>0 $condmag";


if (!empty($search['cs']))
    $query.=" AND fv.code_caissier_fact='" . $search['cs'] . "'";

if (!empty($search['mg']))
    $query.=" AND fv.mag_fact=" . intval($search['mg']);

if (!empty($search['clt']))
    $query.=" AND cl.id_clt=" . intval($search['clt']);

if (!empty($search['grt']))
    $query.=" AND fv.bl_fact_grt=1";

if (!empty($search['d']) && empty($search['f']))
    $query.=" AND date(fv.date_fact)='" . isoToMysqldate($search['d']) . "'";

if (!empty($search['f']))
    $query.=" AND date(fv.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

$query.=" GROUP BY cl.id_clt ORDER BY cl.nom_clt,fv.date_fact DESC";


$r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

/** End query */
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Tongo-Technologies")
        ->setLastModifiedBy(date("Y-m-d"))
        ->setTitle("Etat des creances")
        ->setSubject("Etat des creances")
        ->setDescription("situation des creances.")
        ->setKeywords("Creances")
        ->setCategory("Credits clients");


$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "BOUTIQUE")
        ->setCellValue('B1', "DATE")
        ->setCellValue('C1', "CLIENT")
        ->setCellValue('D1', "CONTACT")
        ->setCellValue('E1', "MONTANT");


// Add data
if ($r->num_rows > 0) {
    $i = 2;
    while ($row = $r->fetch_assoc()) {

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row['nom_mag']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $row['date_fact'] . " " . $row['heure_fact']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $row['nom_clt']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $row['tel_clt']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $row['mnt_crce']);

        // Add page breaks every 10 rows
        if ($i % 20 == 0) {
            // Add a page break
            $objPHPExcel->getActiveSheet()->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
        }
        $i++;
    }
}

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Sit-creances-' . date('Ymd_His'));

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
header('Content-Disposition: attachment;filename="ETAT-CREANCS-GLOBAL-'.date('Ymd_His').'.xlsx"');
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
