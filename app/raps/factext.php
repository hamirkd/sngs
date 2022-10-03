<?php  
 

include ("includes/db.php");

    
    ob_start();
    include(dirname(__FILE__).'/res/factext-r.php');
    $content = ob_get_clean();

    
    require_once(dirname(__FILE__).'/../../libs/html2pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(0, 0, 0, 0));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('Etat-des-Factures-'.date("Ymd-His").'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
