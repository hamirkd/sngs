 <?php
         
 
session_name('SessSngS');
session_start();
?>
<?php
         include "includes/const.php"; 
 ?>
<style type="text/css">
    table tr td{
        border:1px solid #000;
        padding:2px;
    }
    table tr th{
        padding:2px;
    }
</style>
<page backtop="30mm" backbottom="10mm" backleft="20mm" backright="20mm">
    <page_header>
        <?php
         include "includes/header.php";
        ?>
    </page_header>
    <page_footer>
        <?php //include "includes/footer.php"; ?>
    </page_footer>  

    <table cellspacing="0" style="margin-top: 20mm;
           width: 100%; border: solid 1px black; 
            text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
               PROVISIONS DE CAISSE
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des provisions
        </u>   
    </div> 
    <br>


    <?php
    $condMag = "";
    $search = $_GET; 
          
    $query = "SELECT date(cais.date_cais) as date_cais,cais.id_cais, cais.mnt_cais,cais.detail_cais,cais.code_caissier_cais 
                           FROM 
                           t_caisse cais
                            WHERE 1=1  ";
     if ($_SESSION['userMag'] > 0)
        $query = "SELECT date(cais.date_cais) as date_cais,cais.id_cais, cais.mnt_cais,cais.detail_cais,cais.code_caissier_cais 
                           FROM 
                           t_caisse cais
                            WHERE user_cais in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ") ";
       
     if (!empty($search['cs']))
            $query.=" AND cais.code_caissier_cais='" . $search['cs'] . "'";

         
    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(cais.date_cais)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(cais.date_cais) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";
    
    $query.=" ORDER BY cais.date_cais DESC"; 
 
     
    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
                  <th style="width: 12%;text-align: left;border:1px solid black;">MONTANT</th>
                <th style="width: 63%;text-align: left;border:1px solid black;">DETAILS</th>
                <th style="width: 10%;text-align: left;border:1px solid black;">AUTEUR</th> 
            </tr>


    <?php
    while ($row = $r->fetch_assoc()) {
        $total +=$row['mnt_cais'];
        ?>
                <tr >
                    <td style="width: 15%; text-align: left"><?php echo ucfirst(strtolower($row['date_cais'])); ?></td>
                     <td style="width: 12%; text-align: right"><?php echo number_format($row['mnt_cais'], 0, ',', ' '); ?></td>
                    <td style="width: 63%;text-align: right"><?php echo ucfirst(strtolower($row['detail_cais'])); ?></td>
                    <td style="width: 10%; text-align: right;"><?php echo ucfirst($row['code_caissier_cais']); ?></td>
                </tr>

        <?php
    }
    ?> 
        </table>

        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
            <tr>
                <th style="width: 78%; text-align: right;">Total : </th>
                <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
            </tr>
        </table>
    <?php
} else {
    ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
            <tr>
                <th style="width: 100%; text-align: left;">Aucune provision.. </th> 
            </tr>
        </table>
    <?php
}
?> 
</page> 