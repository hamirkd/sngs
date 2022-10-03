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
<page orientation="paysage" format="A4" backtop="30mm" backbottom="10mm" backleft="10mm" backright="10mm">
    <page_header>
        <?php
        include "includes/header.php";
        ?>
    </page_header>
    <page_footer>
        <?php include "includes/footer.php"; ?>
    </page_footer>  
    <?php
    $search = $_GET;
    ?>
    <table cellspacing="0" style="margin-top: 10mm;
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                ETAT DES VENTES
            </th> 
        </tr>
    </table>
    <br>


    <?php
    $grandTotal = 0;
    ?>

    <?php
    $condMag = "";

    if ($_SESSION['userMag'] > 0)/**/ {
        $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($_SESSION['userMag']) . " Limit 1";
        $rmag = $Mysqli->query($querymag);
        $rowmag = $rmag->fetch_assoc();
        ?>
        <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
        <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br>

    <?php
    $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=0 AND id_mag=" . intval($_SESSION['userMag']);

    if (!empty($search['mg']))
        $query.=" AND id_mag=" . intval($search['mg']);

    if (!empty($search['cs']))
        $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

    if (!empty($search['art']))
        $query.=" AND id_art=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND id_cat=" . intval($search['cat']);

    if (!empty($search['clt']))
        $query.=" AND id_clt=" . intval($search['clt']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
        $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
        $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

    $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        ?>

            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>VENTES AU COMPTANT</u>   
            </div> 
            <br>

        <?php
        $result = array();
        $total = 0;
        $totalqte = 0;
        ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                </tr>


        <?php
        while ($row = $r->fetch_assoc()) {
            $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
            $totalqte +=$row['Qte_vnt'];
            $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
            ?>
                    <tr >
                        <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                        <br/>
                        <?php echo $row['heure_vnt']; ?>
                        </td> 
                        <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                    </tr>

            <?php
        }
        ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 26%; text-align: right;">Qte Vendu </th>
                    <th style="width: 26%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                    <th style="width: 26%; text-align: right;">Total comptant : </th>
                    <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
        <?php
    }
    ?>  

        <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=1 AND bl_fact_grt=0 AND id_mag=" . intval($_SESSION['userMag']);

        if (!empty($search['mg']))
            $query.=" AND id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

        if (!empty($search['art']))
            $query.=" AND id_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (!empty($search['clt']))
            $query.=" AND id_clt=" . intval($search['clt']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

        $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <br>
            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>VENTES A CREDIT</u>   
            </div> 
            <br>

        <?php
        $result = array();
        $total = 0;
        $totalqte = 0;
        ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                   <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                </tr>


        <?php
        while ($row = $r->fetch_assoc()) {
            $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
            $totalqte +=$row['Qte_vnt'];
            $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
            ?>
                    <tr >
                        <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                        <br/>
                        <?php echo $row['heure_vnt']; ?></td> 
                       <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                   </tr>

            <?php
        }
        ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 26%; text-align: right;">Qte Vendu </th>
                    <th style="width: 26%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                    <th style="width: 26%; text-align: right;">Total Credit : </th>
                    <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
        <?php
    }
    ?>



        <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=1 AND bl_fact_grt=1 AND id_mag=" . intval($_SESSION['userMag']);

        if (!empty($search['mg']))
            $query.=" AND id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

        if (!empty($search['art']))
            $query.=" AND id_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (!empty($search['clt']))
            $query.=" AND id_clt=" . intval($search['clt']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

        $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>VENTES A GARANTIE</u>   
            </div> 
            <br>

        <?php
        $result = array();
        $total = 0;
        $totalqte = 0;
        ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                </tr>


        <?php
        while ($row = $r->fetch_assoc()) {
            $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
            $totalqte +=$row['Qte_vnt'];
            $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
            ?>
                    <tr >
                        <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                        <br/>
                        <?php echo $row['heure_vnt']; ?></td> 
                        <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                   </tr>

            <?php
        }
        ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 26%; text-align: right;">Qte Vendu </th>
                    <th style="width: 26%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                    <th style="width: 26%; text-align: right;">Total Garatie : </th>
                    <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
        <?php
    }
    ?>

        <br/>
        <br/>
        <br/>
        <table cellspacing="0" style="width: 60%; border-bottom: solid 1px black; background: #E7E7E7; text-align: right; font-size: 20pt;" align="right">
            <tr>
                <th style="width: 100%; text-align: right;background-color:#E7E7E7;">TOTAL : <?php echo number_format($grandTotal, 0, ',', ' '); ?> FCFA</th>
            </tr>
        </table>
<?php } elseif ($_SESSION['userMag'] < 1 && !empty($search['mg'])) {/* fin du premier if */ ?>  


    <?php
    $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($search['mg']) . " Limit 1";
    $rmag = $Mysqli->query($querymag);
    $rowmag = $rmag->fetch_assoc();
    ?>
        <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
        <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br>

        <?php
        $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=0 AND id_mag=" . intval($search['mg']);

        if (!empty($search['mg']))
            $query.=" AND id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

        if (!empty($search['art']))
            $query.=" AND id_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (!empty($search['clt']))
            $query.=" AND id_clt=" . intval($search['clt']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

        $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>VENTES AU COMPTANT</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                   <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $totalqte +=$row['Qte_vnt'];

                $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                ?>
                    <tr >
                        <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                        <br/>
                        <?php echo $row['heure_vnt']; ?></td> 
                         <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 26%; text-align: right;">Qte Vendu : </th>
                    <th style="width: 26%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                    <th style="width: 26%; text-align: right;">Total comptant : </th>
                    <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
                <?php
            }
            ?>  

    <?php
    $condMag = "";
    $search = $_GET;

    $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=1 AND bl_fact_grt=0 AND id_mag=" . intval($search['mg']);

    if (!empty($search['mg']))
        $query.=" AND id_mag=" . intval($search['mg']);

    if (!empty($search['cs']))
        $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

    if (!empty($search['art']))
        $query.=" AND id_art=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND id_cat=" . intval($search['cat']);

    if (!empty($search['clt']))
        $query.=" AND id_clt=" . intval($search['clt']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
        $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
        $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

    $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        ?>

            <br>
            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>VENTES A CREDIT</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $totalqte +=$row['Qte_vnt'];
                $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                ?>
                    <tr >
                        <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                        <br/>
                        <?php echo $row['heure_vnt']; ?></td> 
                        <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                     </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 26%; text-align: right;">Qte Vendu : </th>
                    <th style="width: 26%; text-align: right;"> <?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                    <th style="width: 26%; text-align: right;">Total credit : </th>
                    <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
                <?php
            }
            ?> 



    <?php
    $condMag = "";
    $search = $_GET;

    $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=1 AND bl_fact_grt=1 AND id_mag=" . intval($search['mg']);

    if (!empty($search['mg']))
        $query.=" AND id_mag=" . intval($search['mg']);

    if (!empty($search['cs']))
        $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

    if (!empty($search['art']))
        $query.=" AND id_art=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND id_cat=" . intval($search['cat']);

    if (!empty($search['clt']))
        $query.=" AND id_clt=" . intval($search['clt']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
        $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

    if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
        $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

    $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        ?>

            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>VENTES A GARANTIE</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $totalqte +=$row['Qte_vnt'];
                $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                ?>
                    <tr >
                        <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                        <br/>
                        <?php echo $row['heure_vnt']; ?></td> 
                       <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                      </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 26%; text-align: right;">Qte Vendu : </th>
                    <th style="width: 26%; text-align: right;"> <?php echo number_format($totalqte, 0, ',', ' '); ?>  </th>
                    <th style="width: 26%; text-align: right;">Total garantie : </th>
                    <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
        <?php
    }
    ?> 

        <br/>
        <br/>
        <br/>
        <table cellspacing="0" style="width: 60%; border-bottom: solid 1px black; background: #E7E7E7; text-align: right; font-size: 20pt;" align="right">
            <tr>
                <th style="width: 100%; text-align: right;background-color:#E7E7E7;">TOTAL : <?php echo number_format($grandTotal, 0, ',', ' '); ?> FCFA</th>
            </tr>
        </table>




        <?php
    }/* fin du elseif */
    else { /* else drenier cas des test super */
        ?>


    <?php
    $grandTotalMagasin = 0;
    $querymag = "SELECT id_mag,nom_mag FROM t_magasin ";
    $rmag = $Mysqli->query($querymag);
    while ($rowmag = $rmag->fetch_assoc()) {
        $grandTotal = 0;
        ?>
            <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
            <?php echo $rowmag['nom_mag']; ?>    
            </div> 
            <br>

            <?php
            $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=0 AND id_mag=" . intval($rowmag['id_mag']);

            if (!empty($search['mg']))
                $query.=" AND id_mag=" . intval($search['mg']);

            if (!empty($search['cs']))
                $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

            if (!empty($search['art']))
                $query.=" AND id_art=" . intval($search['art']);

            if (!empty($search['cat']))
                $query.=" AND id_cat=" . intval($search['cat']);

            if (!empty($search['clt']))
                $query.=" AND id_clt=" . intval($search['clt']);

            if (!empty($search['d']) && empty($search['f']))
                $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

            if (!empty($search['f']))
                $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

            if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
                $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

            if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
                $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

            $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


            $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                ?>

                <div style="font-size: 16pt;text-transform: capitalize; 
                     font-weight: bold;" align="left">
                    <u>VENTES AU COMPTANT</u>   
                </div> 
                <br>

                <?php
                $result = array();
                $total = 0;
                $totalqte = 0;
                ?>
                <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                    <tr style="">
                        <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                    </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $totalqte +=$row['Qte_vnt'] ;
                $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $grandTotalMagasin +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                ?>
                        <tr >
                            <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            <br/>
                        <?php echo $row['heure_vnt']; ?></td> 
                           <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                   </tr>

                <?php
            }
            ?> 
                </table>

                <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                    <tr>
                        <th style="width: 26%; text-align: right;">Qte Vendu : </th>
                        <th style="width: 26%; text-align: right;"> <?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                        <th style="width: 26%; text-align: right;">Total comptant : </th>
                        <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                    </tr>
                </table>
            <?php
        }
        ?>  

        <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=1 AND bl_fact_grt=0 AND id_mag=" . intval($rowmag['id_mag']);

        if (!empty($search['mg']))
            $query.=" AND id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

        if (!empty($search['art']))
            $query.=" AND id_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (!empty($search['clt']))
            $query.=" AND id_clt=" . intval($search['clt']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

        $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

                <br>
                <br>
                <div style="font-size: 16pt;text-transform: capitalize; 
                     font-weight: bold;" align="left">
                    <u>VENTES A CREDIT</u>   
                </div> 
                <br>

                <?php
                $result = array();
                $total = 0;
                $totalqte = 0;
                ?>
                <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                    <tr style="">
                       <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                    </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $totalqte +=$row['Qte_vnt'];
                $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $grandTotalMagasin +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                ?>
                        <tr >
                            <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            <br/>
                        <?php echo $row['heure_vnt']; ?></td> 
                           <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                    </tr>

                <?php
            }
            ?> 
                </table>

                <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                    <tr>
                        <th style="width: 26%; text-align: right;">Qte Vendu : </th>
                        <th style="width: 26%; text-align: right;"> <?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                        <th style="width: 26%; text-align: right;">Total credit : </th>
                        <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                    </tr>
                </table>
            <?php
        }
        ?> 



        <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT *,time(Date_vnt) as heure_vnt FROM v_etat_ventes WHERE sup_fact=0 AND bl_fact_crdt=1 AND bl_fact_grt=1 AND id_mag=" . intval($rowmag['id_mag']);

        if (!empty($search['mg']))
            $query.=" AND id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND code_caissier_fact='" . $search['cs'] . "'";

        if (!empty($search['art']))
            $query.=" AND id_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (!empty($search['clt']))
            $query.=" AND id_clt=" . intval($search['clt']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(Date_vnt)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(Date_vnt) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND bl_fact_grt=0 AND bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND bl_fact_crdt=1 AND bl_fact_grt=1";

        $query.=" ORDER BY code_mag,Date_vnt DESC,nom_cat ASC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

                <br>
                <div style="font-size: 16pt;text-transform: capitalize; 
                     font-weight: bold;" align="left">
                    <u>VENTES A GARANTIE</u>   
                </div> 
                <br>

                <?php
                $result = array();
                $total = 0;
                $totalqte = 0;
                ?>
                <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                    <tr style="">
                        <th style="width: 7%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 10%;text-align: left;border:1px solid black;">FACT</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">DESIGNATION</th>
                    <th style="width: 18%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 8%;text-align: left;border:1px solid black;">PU</th>
                    <th style="width: 5%;text-align: left;border:1px solid black;">QTE</th>
                    <th style="width: 12%;text-align: left;border:1px solid black;">MNT</th> 
                    </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $totalqte +=$row['Qte_vnt'];
                $grandTotal +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                $grandTotalMagasin +=$row['Qte_vnt'] * $row['pu_theo_vnt'];
                ?>
                        <tr>
                            <td style="width: 7%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            <br/>
                        <?php echo $row['heure_vnt']; ?></td> 
                           <td style="width: 10%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 18%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 8%; text-align: right"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        <td style="width: 5%;text-align: right"><?php echo $row['Qte_vnt']; ?></td>
                        <td style="width: 12%; text-align: right;"><?php echo number_format($row['Qte_vnt'] * $row['pu_theo_vnt'], 2, ',', ' '); ?></td>
                    </tr>

                <?php
            }
            ?> 
                </table>

                <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                    <tr>
                        <th style="width: 26%; text-align: right;">Qte Vendu : </th>
                        <th style="width: 26%; text-align: right;"> <?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                        <th style="width: 26%; text-align: right;">Total garantie : </th>
                        <th style="width: 22%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                    </tr>
                </table>
            <?php
        }
        ?> 

            <br/>
            <br/>
            <br/>
            <table cellspacing="0" style="width: 60%; border-bottom: solid 1px black; background: #E7E7E7; text-align: right; font-size: 20pt;" align="right">
                <tr>
                    <th style="width: 100%; text-align: right;background-color:#E7E7E7;">TOTAL : <?php echo number_format($grandTotal, 0, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
            <br/>
            <br/>
            <br/>
    <?php } /* fin du while */ ?> 
        <br/>
        <table cellspacing="0" style="width: 100%; border:  1px double black; background: #E7E7E7; text-align: center; font-size: 20pt;" align="center">
            <tr>
                <th style="width: 40%; text-align: right;background-color:#E7E7E7;">TOTAL GENERAL</th>
                <th style="width: 60%; text-align: right;background-color:#E7E7E7;"><?php echo number_format($grandTotalMagasin, 0, ',', ' '); ?> FCFA</th>
            </tr>
        </table> 
    <?php
}/* fin du else de tous les cas */
?>
</page> 