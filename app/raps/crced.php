<?php  
 

include ("includes/db.php");


    ob_start();
    include(dirname(__FILE__).'/res/crced-r.php');
    $content = ob_get_clean();
    require_once(dirname(__FILE__).'/../../libs/html2pdf/html2pdf.class.php');
    
    try
    {
        //  echo $content;
        $html2pdf = new HTML2PDF('P', 'A4', 'it', true, 'UTF-8', 3);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content);
        $html2pdf->Output('Etat-des-creances-detaille-'.date("Ymd-His").'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
