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
<page backtop="30mm" backbottom="10mm" backleft="20mm" backright="20mm" >
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
                ETAT DES DETTES
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des dettes fournisseurs
        </u>   
    </div> 
    <br>


    <?php
    $condMag = "";
    $search = $_GET; 
          
    $condmag = "";
        if ($_SESSION['userMag'] > 0)
            $condmag = " AND ap.user_appro in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";


        $query = "SELECT 
            ap.code_user_appro,
            ap.login_appro,
            (ap.mnt_revient_appro-ap.som_verse_dette) as mnt_dette ,
            ap.bon_liv_appro,
            date(ap.date_appro) as date_appro,
            f.code_frns,
            f.nom_frns 
            FROM t_approvisionnement ap  
            INNER JOIN t_fournisseur f ON ap.frns_appro=f.id_frns
            WHERE ap.bl_bon_dette=1 AND ap.bl_dette_regle=0 $condmag ";
       
   
    if (!empty($search['cs']))
            $query.=" AND ap.code_user_appro='" . $search['cs'] . "'";

     if (!empty($search['frns']))
        $query.=" AND f.id_frns=" . intval($search['frns']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(ap.date_appro)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(ap.date_appro) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";
 
    $query.=" ORDER BY ap.date_appro DESC"; 
 
     
    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                <th style="width: 20%;text-align: left;border:1px solid black;">No BON</th>
                <th style="width: 35%;text-align: left;border:1px solid black;">FOURNISSEUR</th> 
                <th style="width: 25%;text-align: left;border:1px solid black;">MONTANT</th>   
            </tr>


    <?php
    while ($row = $r->fetch_assoc()) {
        $total +=$row['mnt_dette'];
        ?>
                <tr >
                    <td style="width: 20%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['date_appro'])); ?></td>
                    <td style="width: 20%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['bon_liv_appro'])); ?></td>
                    <td style="width: 35%;border:1px solid black; text-align: left">&nbsp;<?php echo strtolower($row['nom_frns']); ?></td>
                    <td style="width: 25%;border:1px solid black; text-align: right;"><?php echo number_format($row['mnt_dette'], 2, ',', ' '); ?></td>
                 </tr>

        <?php
    }
    ?> 
        </table>

        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
            <tr>
                <th style="width: 60%; text-align: right;">Total : </th>
                <th style="width: 40%; text-align: left;"><?php echo number_format($total, 2, ',', ' '); ?> F</th>
            </tr>
        </table>
    <?php
} else {
    ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
            <tr>
                <th style="width: 100%; text-align: left;">Aucune dette.. </th> 
            </tr>
        </table>
    <?php
}
?> 
</page> 