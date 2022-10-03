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
        <?php include "includes/footer.php"; ?>
    </page_footer>  

    <table cellspacing="0" style="margin-top: 20mm;
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                ETAT DETAILLE DES REGLEMENTS CLIENTS
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste detaillee des reglements clients
        </u>   
    </div> 
    <br>


    <?php
     $search = $_GET;

    $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT date(c.date_crce_clnt) as date_crce_clnt,
            c.code_caissier_crce,
            c.caissier_login_crce,
            c.mnt_paye_crce_clnt,
            c.ref_crce_clnt,
            fv.code_fact,
            fv.date_fact,
            cl.code_clt,
            cl.nom_clt 
            FROM t_creance_client c 
            INNER JOIN t_facture_vente fv ON c.fact_crce_clnt=fv.id_fact
            INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            WHERE fv.bl_fact_grt=0 $condmag";

   /* if (!empty($search['mg']))
        $query.=" AND id_mag=" . intval($search['mg']);*/

    if (!empty($search['cs']))
        $query.=" AND c.code_caissier_crce='" . $search['cs'] . "'";
    
    if (!empty($search['ref']))
        $query.=" AND c.ref_crce_clnt LIKE '%" . $search['ref'] . "%'";

     if (!empty($search['clt']))
        $query.=" AND fv.clnt_fact=" . intval($search['clt']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(c.date_crce_clnt)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(c.date_crce_clnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'"; 
    
    $query.=" ORDER BY c.date_crce_clnt DESC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
                <th style="width: 15%;text-align: left;border:1px solid black;">No FACTURE</th>
                <th style="width: 23%;text-align: left;border:1px solid black;">CLIENT</th>
                <th style="width: 14%;text-align: left;border:1px solid black;">MONTANT</th>
                <th style="width: 20%;text-align: left;border:1px solid black;">REF</th>
                <th style="width: 13%;text-align: left;border:1px solid black;">CAISSIER</th> 
            </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['mnt_paye_crce_clnt'];
                ?>
                <tr >
                    <td style="width: 15%;text-align: left"><?php echo text_reduit($row['date_crce_clnt'],10); ?></td> 
                    <td style="width: 15%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                    <td style="width: 23%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                    <td style="width: 14%; text-align: right"><?php echo number_format($row['mnt_paye_crce_clnt'], 0, ',', ' '); ?></td>
                     <td style="width: 20%; text-align: right"><?php echo text_reduit($row['ref_crce_clnt'],50); ?></td>
                    <td style="width: 13%;text-align: right"><?php echo $row['code_caissier_crce']; ?></td>
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
                <th style="width: 100%; text-align: left;">Aucun reglement client.. </th> 
            </tr>
        </table>
        <?php
    }
    ?> 
</page> 