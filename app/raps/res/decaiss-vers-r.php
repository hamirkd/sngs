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
<page backtop="30mm" backbottom="10mm" backleft="10mm" backright="10mm" orientation="paysage">
    <page_header>
        <?php
        include "includes/header.php";
        ?>
    </page_header>
    <page_footer>
        <?php include "includes/footer.php"; ?>
    </page_footer>  

    <table cellspacing="0" style="margin-top: 5mm;
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                ETAT DES VERSEMENTS
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des versements
        </u>   
    </div> 
    <br>


    <?php
     $search = $_GET;

     if ($_SESSION['userMag'] > 0)
            $query = "SELECT v.id_vrsmnt,DATE(v.date_vrsmnt) as date_vrsmnt, v.mnt_vrsmnt,v.obj_vrsmnt,v.code_caissier_vrsmnt,
            b.nom_bank
                           FROM 
                           t_versement v
                           INNER JOIN t_banque b ON v.bank_vrsmnt=b.id_bank
                           WHERE   v.caissier_vrsmnt in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")
                            ";
        else
            $query = "SELECT v.id_vrsmnt,DATE(v.date_vrsmnt) as date_vrsmnt, v.mnt_vrsmnt,v.obj_vrsmnt,v.code_caissier_vrsmnt,
            b.nom_bank
                           FROM 
                           t_versement v
                           INNER JOIN t_banque b ON v.bank_vrsmnt=b.id_bank
                           WHERE 1=1 ";

   

    if (!empty($search['cs']))
        $query.=" AND v.code_caissier_vrsmnt='" . $search['cs'] . "'";

     if (!empty($search['bnk']))
        $query.=" AND v.bank_vrsmnt=" . intval($search['bnk']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(v.date_vrsmnt)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(v.date_vrsmnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'"; 
    
    $query.=" ORDER BY v.date_vrsmnt DESC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
                <th style="width: 25%;text-align: left;border:1px solid black;">BANQUE</th>
                <th style="width: 14%;text-align: left;border:1px solid black;">MONTANT</th>
                <th style="width: 33%;text-align: left;border:1px solid black;">DETAILS</th>
                <th style="width: 13%;text-align: left;border:1px solid black;">AUTEUR</th> 
            </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['mnt_vrsmnt'];
                ?>
                <tr >
                    <td style="width: 15%;text-align: left"><?php echo text_reduit($row['date_vrsmnt'],10); ?></td> 
                    <td style="width: 25%; text-align: left"><?php echo ucfirst(strtolower($row['nom_bank'])); ?></td>
                    <td style="width: 14%; text-align: right"><?php echo number_format($row['mnt_vrsmnt'], 0, ',', ' '); ?></td>
                    <td style="width: 33%;text-align: left"><?php echo ucwords(strtolower($row['obj_vrsmnt'])); ?></td>
                    <td style="width: 13%;text-align: right"><?php echo $row['code_caissier_vrsmnt']; ?></td>
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
                <th style="width: 100%; text-align: left;">Aucun versement.. </th> 
            </tr>
        </table>
        <?php
    }
    ?> 
</page> 