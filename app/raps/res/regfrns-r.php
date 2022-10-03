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
                ETAT DES REGLEMENTS FOURNISSEURS
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des reglements fournisseurs
        </u>   
    </div> 
    <br>


    <?php
     $search = $_GET;

     $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND ap.user_appro in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT date(d.date_dette_frns) as date_dette_frns,
            d.code_caissier_frns,
            d.caissier_login_frns,
            d.mnt_paye_dette_frns,
            d.ref_dette_frns,
            ap.bon_liv_appro,
            ap.date_appro,
            f.code_frns,
            f.nom_frns 
            FROM t_dette_fournisseur d
            INNER JOIN t_approvisionnement ap ON ap.id_appro=d.bon_dette_frns
            INNER JOIN t_fournisseur f ON ap.frns_appro=f.id_frns
            WHERE 1=1 $condmag";

   /* if (!empty($search['mg']))
        $query.=" AND id_mag=" . intval($search['mg']);*/

    if (!empty($search['cs']))
        $query.=" AND d.code_caissier_frns='" . $search['cs'] . "'";
    
    if (!empty($search['ref']))
        $query.=" AND d.ref_dette_frns LIKE '%" .  $search['ref']  . "%'";

     if (!empty($search['frns']))
        $query.=" AND f.id_frns=" . intval($search['frns']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(d.date_dette_frns)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(d.date_dette_frns) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'"; 
    
    $query.=" ORDER BY d.date_dette_frns DESC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
                <th style="width: 15%;text-align: left;border:1px solid black;">No BON</th>
                <th style="width: 23%;text-align: left;border:1px solid black;">FOURNISSEUR</th>
                <th style="width: 14%;text-align: left;border:1px solid black;">MONTANT</th>
                <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th>
                <th style="width: 13%;text-align: left;border:1px solid black;">AUTEUR</th> 
            </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['mnt_paye_dette_frns'];
                ?>
                <tr >
                    <td style="width: 15%;text-align: left"><?php echo text_reduit($row['date_dette_frns'],10); ?></td> 
                    <td style="width: 15%; text-align: left"><?php echo ucfirst(strtolower($row['bon_liv_appro'])); ?></td>
                    <td style="width: 23%;text-align: left"><?php echo ucwords(strtolower($row['nom_frns'])); ?></td>
                    <td style="width: 14%; text-align: right"><?php echo number_format($row['mnt_paye_dette_frns'], 0, ',', ' '); ?></td>
                    <td style="width: 20%; text-align: right"><?php echo text_reduit($row['ref_dette_frns'],50); ?></td>
                    <td style="width: 13%;text-align: right"><?php echo $row['code_caissier_frns']; ?></td>
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
                <th style="width: 100%; text-align: left;">Aucun reglement fournisseur.. </th> 
            </tr>
        </table>
        <?php
    }
    ?> 
</page> 