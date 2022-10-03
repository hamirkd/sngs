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
                ETAT DES FACTURES
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
        $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=0 AND m.id_mag=" . intval($_SESSION['userMag']);

        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

        /* if (!empty($search['art']))
          $query.=" AND id_art=" . intval($search['art']);

          if (!empty($search['cat']))
          $query.=" AND id_cat=" . intval($search['cat']); */

        if (!empty($search['clt']))
            $query.=" AND c.id_clt=" . intval($search['clt']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY m.code_mag,f.date_fact DESC";

        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>FACTURES AU COMPTANT</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            //$totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                    <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['crdt_fact'];
                    // $totalqte +=$row['Qte_vnt'];
                    $grandTotal +=$row['crdt_fact'];
                    ?>
                    <tr >
                        <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            &nbsp;&nbsp; <?php echo $row['heure_vnt']; ?>
                        </td> 
                        <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 80%; text-align: right;">Total comptant : </th>
                    <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
            <?php
        }
        ?>  

        <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND m.id_mag=" . intval($_SESSION['userMag']);

        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

        /* if (!empty($search['art']))
          $query.=" AND id_art=" . intval($search['art']);

          if (!empty($search['cat']))
          $query.=" AND id_cat=" . intval($search['cat']); */

        if (!empty($search['clt']))
            $query.=" AND c.id_clt=" . intval($search['clt']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY m.code_mag,f.date_fact DESC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <br>
            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>FACTURES A CREDIT</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            //$totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                    <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['crdt_fact'];
                    //$totalqte +=$row['Qte_vnt'];
                    $grandTotal +=$row['crdt_fact'];
                    ?>
                    <tr >
                        <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            <br/>
                            <?php echo $row['heure_vnt']; ?></td> 
                        <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 80%; text-align: right;">Total Credit : </th>
                    <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
            <?php
        }
        ?>



        <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND m.id_mag=" . intval($_SESSION['userMag']);

        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

        /* if (!empty($search['art']))
          $query.=" AND id_art=" . intval($search['art']);

          if (!empty($search['cat']))
          $query.=" AND id_cat=" . intval($search['cat']); */

        if (!empty($search['clt']))
            $query.=" AND c.id_clt=" . intval($search['clt']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY m.code_mag,f.date_fact DESC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>FACTURES A GARANTIE</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            // $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                    <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['crdt_fact'];
                    //$totalqte +=$row['Qte_vnt'];
                    $grandTotal +=$row['crdt_fact'];
                    ?>
                    <tr >
                        <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                             &nbsp;&nbsp; <?php echo $row['heure_vnt']; ?></td> 
                        <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 80%; text-align: right;">Total Garatie : </th>
                    <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
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
        $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=0 AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

        /* if (!empty($search['art']))
          $query.=" AND id_art=" . intval($search['art']);

          if (!empty($search['cat']))
          $query.=" AND id_cat=" . intval($search['cat']); */

        if (!empty($search['clt']))
            $query.=" AND c.id_clt=" . intval($search['clt']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY m.code_mag,f.date_fact DESC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>FACTURES AU COMPTANT</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            // $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                    <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['crdt_fact'];
                    // $totalqte +=$row['Qte_vnt'];
                    $grandTotal +=$row['crdt_fact'];
                    ?>
                    <tr >
                        <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            &nbsp;&nbsp;  <?php echo $row['heure_vnt']; ?>
                        </td> 
                        <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 80%; text-align: right;">Total comptant : </th>
                    <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
            <?php
        }
        ?> 

        <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

        /* if (!empty($search['art']))
          $query.=" AND id_art=" . intval($search['art']);

          if (!empty($search['cat']))
          $query.=" AND id_cat=" . intval($search['cat']); */

        if (!empty($search['clt']))
            $query.=" AND c.id_clt=" . intval($search['clt']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY m.code_mag,f.date_fact DESC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <br>
            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>FACTURES A CREDIT</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            //$totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                    <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['crdt_fact'];
                    //$totalqte +=$row['Qte_vnt'];
                    $grandTotal +=$row['crdt_fact'];
                    ?>
                    <tr >
                        <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            &nbsp;&nbsp;  <?php echo $row['heure_vnt']; ?></td> 
                        <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 80%; text-align: right;">Total Credit : </th>
                    <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
            <?php
        }
        ?>



        <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

        /* if (!empty($search['art']))
          $query.=" AND id_art=" . intval($search['art']);

          if (!empty($search['cat']))
          $query.=" AND id_cat=" . intval($search['cat']); */

        if (!empty($search['clt']))
            $query.=" AND c.id_clt=" . intval($search['clt']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY m.code_mag,f.date_fact DESC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>FACTURES A GARANTIE</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            // $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                    <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['crdt_fact'];
                    //$totalqte +=$row['Qte_vnt'];
                    $grandTotal +=$row['crdt_fact'];
                    ?>
                    <tr >
                        <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            &nbsp;&nbsp;  <?php echo $row['heure_vnt']; ?></td> 
                        <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 80%; text-align: right;">Total Garatie : </th>
                    <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
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
            $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=0 AND m.id_mag=" . intval($rowmag['id_mag']);

            if (!empty($search['mg']))
                $query.=" AND m.id_mag=" . intval($search['mg']);

            if (!empty($search['cs']))
                $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

            /* if (!empty($search['art']))
              $query.=" AND id_art=" . intval($search['art']);

              if (!empty($search['cat']))
              $query.=" AND id_cat=" . intval($search['cat']); */

            if (!empty($search['clt']))
                $query.=" AND c.id_clt=" . intval($search['clt']);

            if (!empty($search['tx']) && $search['tx'] != "")
                $query.=" AND f.bl_tva=" . intval($search['tx']);

            if (!empty($search['d']) && empty($search['f']))
                $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

            if (!empty($search['f']))
                $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

            if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
                $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

            if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
                $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

            $query.=" ORDER BY m.code_mag,f.date_fact DESC";

            $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                ?>

                <div style="font-size: 16pt;text-transform: capitalize; 
                     font-weight: bold;" align="left">
                    <u>FACTURES AU COMPTANT</u>   
                </div> 
                <br>

                <?php
                $result = array();
                $total = 0;
                //$totalqte = 0;
                ?>
                <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                    <tr style="">
                        <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                        <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                        <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                        <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                    </tr>


                    <?php
                    while ($row = $r->fetch_assoc()) {
                        $total +=$row['crdt_fact'];
                        // $totalqte +=$row['Qte_vnt'];
                        $grandTotal +=$row['crdt_fact'];
                        $grandTotalMagasin +=$row['crdt_fact'];
                        ?>
                        <tr >
                            <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                               &nbsp;&nbsp;   <?php echo $row['heure_vnt']; ?>
                            </td> 
                            <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                            <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                            <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                        </tr>

                        <?php
                    }
                    ?> 
                </table>

                <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                    <tr>
                        <th style="width: 80%; text-align: right;">Total comptant : </th>
                        <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                    </tr>
                </table>
                <?php
            }
            ?>   

             <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND m.id_mag=" . intval($rowmag['id_mag']);

        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

        /* if (!empty($search['art']))
          $query.=" AND id_art=" . intval($search['art']);

          if (!empty($search['cat']))
          $query.=" AND id_cat=" . intval($search['cat']); */

        if (!empty($search['clt']))
            $query.=" AND c.id_clt=" . intval($search['clt']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY m.code_mag,f.date_fact DESC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <br>
            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>FACTURES A CREDIT</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            //$totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                    <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['crdt_fact'];
                    //$totalqte +=$row['Qte_vnt'];
                    $grandTotal +=$row['crdt_fact'];
                    $grandTotalMagasin +=$row['crdt_fact'];
                    ?>
                    <tr >
                        <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            &nbsp;&nbsp;  <?php echo $row['heure_vnt']; ?></td> 
                        <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 80%; text-align: right;">Total Credit : </th>
                    <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
            <?php
        }
        ?>



             <?php
        $condMag = "";
        $search = $_GET;

        $query = "SELECT date(f.date_fact) as Date_vnt,time(f.date_fact) as heure_vnt,
            f.code_fact,f.bl_fact_grt,f.bl_fact_crdt,f.sup_fact,f.remise_vnt_fact,f.crdt_fact,
            f.som_verse_crdt,f.code_caissier_fact,c.code_clt,m.nom_mag,
            m.code_mag,c.nom_clt FROM t_facture_vente f
            INNER JOIN t_client c ON f.clnt_fact=c.id_clt
            INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
             INNER JOIN t_user u ON f.caissier_fact=u.id_user
             WHERE f.sup_fact=0 AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND m.id_mag=" . intval($rowmag['id_mag']);

        if (!empty($search['mg']))
            $query.=" AND m.id_mag=" . intval($search['mg']);

        if (!empty($search['cs']))
            $query.=" AND f.code_caissier_fact='" . $search['cs'] . "'";

        /* if (!empty($search['art']))
          $query.=" AND id_art=" . intval($search['art']);

          if (!empty($search['cat']))
          $query.=" AND id_cat=" . intval($search['cat']); */

        if (!empty($search['clt']))
            $query.=" AND c.id_clt=" . intval($search['clt']);

        if (!empty($search['tx']) && $search['tx'] != "")
            $query.=" AND f.bl_tva=" . intval($search['tx']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(f.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(f.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] != 2)
            $query.=" AND f.bl_fact_grt=0 AND f.bl_fact_crdt=" . intval($search['tv']);

        if (isset($search['tv']) && $search['tv'] != "" && $search['tv'] == 2)
            $query.=" AND f.bl_fact_crdt=1 AND f.bl_fact_grt=1";

        $query.=" ORDER BY m.code_mag,f.date_fact DESC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            ?>

            <br>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>FACTURES A GARANTIE</u>   
            </div> 
            <br>

            <?php
            $result = array();
            $total = 0;
            // $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 22%;text-align: left;border:1px solid black;">FACTURE</th>
                    <th style="width: 38%;text-align: left;border:1px solid black;">CLIENT</th>
                    <th style="width: 20%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['crdt_fact'];
                    //$totalqte +=$row['Qte_vnt'];
                    $grandTotal +=$row['crdt_fact'];
                    $grandTotalMagasin +=$row['crdt_fact'];
                    ?>
                    <tr >
                        <td style="width: 20%;text-align: left"><?php echo text_reduit($row['Date_vnt'], 10); ?> <?php if ($row['bl_fact_grt'] == 1) echo "(G)"; ?>
                            &nbsp;&nbsp;  <?php echo $row['heure_vnt']; ?></td> 
                        <td style="width: 22%; text-align: left"><?php echo ucfirst(strtolower($row['code_fact'])); ?></td>
                        <td style="width: 38%;text-align: left"><?php echo ucwords(strtolower($row['nom_clt'])); ?></td>
                        <td style="width: 20%; text-align: right;"><?php echo number_format($row['crdt_fact'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 80%; text-align: right;">Total Garatie : </th>
                    <th style="width: 20%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> FCFA</th>
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