<?php  
 

include ("includes/db.php");

    
    ob_start();
    include(dirname(__FILE__).'/res/crcegrt-r.php');
    $content = ob_get_clean();

    
    require_once(dirname(__FILE__).'/../../libs/html2pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 3);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content);
        $html2pdf->Output('Etat-des-Garanties-'.date("Ymd-His").'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
