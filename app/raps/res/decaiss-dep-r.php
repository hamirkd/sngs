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
                ETAT DES DEPENSES
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des depenses
        </u>   
    </div> 
    <br>


    <?php
    $condMag = "";
    $search = $_GET; 
          
    $query = "SELECT date(dep.date_dep) as date_dep,dep.id_dep, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
            td.lib_type_dep
                           FROM 
                           t_depense dep
                           INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
                           WHERE 1=1  ";
     if ($_SESSION['userMag'] > 0)
        $query = "SELECT date(dep.date_dep) as date_dep,dep.id_dep, dep.mnt_dep,dep.details_dep,dep.code_user_dep,
            td.lib_type_dep
                           FROM 
                           t_depense dep
                           INNER JOIN t_type_depense td ON dep.type_dep=td.id_type_dep
                           WHERE user_dep in (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ") ";
       
     if (!empty($search['cs']))
            $query.=" AND dep.code_user_dep='" . $search['cs'] . "'";

    if (!empty($search['td']))
        $query.=" AND td.id_type_dep=" . intval($search['td']);

     
    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(dep.date_dep)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(dep.date_dep) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";
    
    $query.=" ORDER BY dep.date_dep DESC,td.lib_type_dep ASC"; 
 
     
    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
                <th style="width: 32%;text-align: left;border:1px solid black;">NATURE</th>
                <th style="width: 12%;text-align: left;border:1px solid black;">MONTANT</th>
                <th style="width: 31%;text-align: left;border:1px solid black;">DETAILS</th>
                <th style="width: 10%;text-align: left;border:1px solid black;">AUTEUR</th> 
            </tr>


    <?php
    while ($row = $r->fetch_assoc()) {
        $total +=$row['mnt_dep'];
        ?>
                <tr >
                    <td style="width: 15%; text-align: left"><?php echo ucfirst(strtolower($row['date_dep'])); ?></td>
                    <td style="width: 32%;text-align: left"><?php echo ucwords(strtolower($row['lib_type_dep'])); ?></td>
                    <td style="width: 12%; text-align: right"><?php echo number_format($row['mnt_dep'], 0, ',', ' '); ?></td>
                    <td style="width: 31%;text-align: right"><?php echo ucfirst(strtolower($row['details_dep'])); ?></td>
                    <td style="width: 10%; text-align: right;"><?php echo ucfirst($row['code_user_dep']); ?></td>
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
                <th style="width: 100%; text-align: left;">Aucune depense.. </th> 
            </tr>
        </table>
    <?php
}
?> 
</page> 