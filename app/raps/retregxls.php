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
            fv.code_fact,
            date(fv.date_fact) as date_fact,
            time(fv.date_fact) as heure_fact,
            fv.delai_pay_fact,
            datediff(fv.delai_pay_fact,now()) as nbjours,
             fv.id_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt,
            m.code_mag,
            m.nom_mag,
            fv.code_caissier_fact,
            (crdt_fact-som_verse_crdt) as reste
            FROM   t_facture_vente fv
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt 
            INNER JOIN t_magasin m ON m.id_mag=fv.mag_fact
            WHERE fv.sup_fact=0 AND fv.bl_fact_crdt=1 $condmag AND fv.bl_crdt_regle=0 AND datediff(fv.delai_pay_fact,now())<" . $_SESSION['ret_pay'] . " 
             ";
        
         if (!empty($search['cs']))
            $query.=" AND fv.login_caissier_fact='" . $this->esc($search['cs']) . "'";
        
         if (!empty($search['clt']))
            $query.=" AND fv.clnt_fact=" . intval($search['clt']);
        
        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(fv.delai_pay_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(fv.delai_pay_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";
        
        $query .="order by nbjours ASC";


$r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

/** End query */
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Tongo-Technologies")
        ->setLastModifiedBy(date("Y-m-d"))
        ->setTitle("Etat des retards paiement")
        ->setSubject("Etat des retards paiement")
        ->setDescription("retards paiement.")
        ->setKeywords("Retards")
        ->setCategory("Creances retards");


$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()
        ->setCellValue('A1', "BOUTIQUE")
        ->setCellValue('B1', "DATEFACT")
        ->setCellValue('C1', "NUMFACT")
        ->setCellValue('D1', "CODECLIENT")
        ->setCellValue('E1', "CLIENT")
        ->setCellValue('F1', "MONTANTREST")
        ->setCellValue('G1', "DATELIMIT")
        ->setCellValue('H1', "RETARD")
        ->setCellValue('I1', "CAISSIER");


// Add data
if ($r->num_rows > 0) {
    $i = 2;
    while ($row = $r->fetch_assoc()) {

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row['nom_mag']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $row['date_fact'] . " " . $row['heure_fact']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $row['code_fact']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $row['code_clt']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $row['nom_clt']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $row['reste']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $row['delai_pay_fact']); 
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i,(-1) * ($row['nbjours']));
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $row['code_caissier_fact']); 
         
         

        // Add page breaks every 10 rows
        if ($i % 20 == 0) {
            // Add a page break
            $objPHPExcel->getActiveSheet()->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
        }
        $i++;
    }
}

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Ret-Paiement-' . date('Ymd_His'));

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
header('Content-Disposition: attachment;filename="RETARD-PAIEMENT-'.date('Ymd_His').'.xlsx"');
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
