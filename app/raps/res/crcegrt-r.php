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
<page backtop="30mm" format="A4" backbottom="10mm" backleft="10mm" backright="10mm">
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
    <table cellspacing="0" style="margin-top: 5mm;
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                ETAT DES GARANTIES
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des Garanties clients
        </u>   
    </div> 
    <br>
    <?php
    $grandTotal = 0;
    ?>
    <?php
    if ($_SESSION['userMag'] > 0)/**/ {
        $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($_SESSION['userMag']) . " Limit 1";
        $rmag = $Mysqli->query($querymag);
        $rowmag = $rmag->fetch_assoc();
        ?>
        <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
            <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br/>
        <br/> 

         <?php
        $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($_SESSION['userMag']) . ")";

        $query = "SELECT  
            fv.code_caissier_fact,
            fv.login_caissier_fact,
            fv.bl_fact_grt,
            SUM(fv.crdt_fact-fv.som_verse_crdt-fv.remise_vnt_fact) as mnt_crce,
            fv.code_fact,
            date(fv.date_fact) as date_fact,
            time(fv.date_fact) as heure_fact,
            cl.code_clt,
            cl.nom_clt,
            cl.tel_clt,
            m.code_mag
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             inner join t_magasin m on fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_crdt=1 AND bl_fact_grt=1 AND fv.bl_crdt_regle=0 AND fv.sup_fact=0 AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt-fv.remise_vnt_fact)>0 $condmag ";


        if (!empty($search['cs']))
            $query.=" AND fv.code_caissier_fact='" . $search['cs'] . "'";

        if (!empty($search['mg']))
            $query.=" AND fv.mag_fact=" . intval($search['mg']);

        if (!empty($search['clt']))
            $query.=" AND cl.id_clt=" . intval($search['clt']);

        if (!empty($search['grt']))
            $query.=" AND fv.bl_fact_grt=1";

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(fv.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(fv.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query.=" GROUP BY cl.id_clt ORDER BY cl.nom_clt,fv.date_fact ASC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            ?>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>GARANTIES</u>   
            </div> 
            <br>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">CLIENT</th> 
                    <th style="width: 20%;text-align: left;border:1px solid black;">CONTACT</th>
                    <th style="width: 25%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['mnt_crce'];
                    $grandTotal +=$row['mnt_crce'];
                    ?>
                    <tr >
                        <td style="width: 15%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['date_fact'])); ?><br/><?php echo $row['heure_fact']; ?></td>
                        <td style="width: 40%;border:1px solid black; text-align: left">&nbsp;<?php echo strtolower($row['nom_clt']); ?></td>
                        <td style="width: 20%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['tel_clt'])); ?>  <?php /*if ($row['bl_fact_grt'] == 1) echo "(G)";*/ ?></td>
                        <td style="width: 25%;border:1px solid black; text-align: right;"><?php echo number_format($row['mnt_crce'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 60%; text-align: right;">Total garanties : </th>
                    <th style="width: 40%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> F</th>
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
    }elseif ($_SESSION['userMag'] < 1 && !empty($search['mg']))/**/ {

        $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($search['mg']) . " Limit 1";
        $rmag = $Mysqli->query($querymag);
        $rowmag = $rmag->fetch_assoc();
        ?>
        <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
            <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br/>
        <br/> 

         

        <?php
        $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($search['mg']) . ")";

        $query = "SELECT  
            fv.code_caissier_fact,
            fv.login_caissier_fact,
            fv.bl_fact_grt,
            SUM(fv.crdt_fact-fv.som_verse_crdt-fv.remise_vnt_fact) as mnt_crce,
            fv.code_fact,
            date(fv.date_fact) as date_fact,
            time(fv.date_fact) as heure_fact,
            cl.code_clt,
            cl.nom_clt,
            cl.tel_clt,
            m.code_mag
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             inner join t_magasin m on fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_crdt=1 AND bl_fact_grt=1 AND fv.bl_crdt_regle=0 AND fv.sup_fact=0 AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt-fv.remise_vnt_fact)>0 $condmag ";


        if (!empty($search['cs']))
            $query.=" AND fv.code_caissier_fact='" . $search['cs'] . "'";

        if (!empty($search['mg']))
            $query.=" AND fv.mag_fact=" . intval($search['mg']);

        if (!empty($search['clt']))
            $query.=" AND cl.id_clt=" . intval($search['clt']);

        if (!empty($search['grt']))
            $query.=" AND fv.bl_fact_grt=1";

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(fv.date_fact)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(fv.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query.=" GROUP BY cl.id_clt ORDER BY cl.nom_clt,fv.date_fact ASC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            ?>
            <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>GARANTIES</u>   
            </div> 
            <br>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
                    <th style="width: 40%;text-align: left;border:1px solid black;">CLIENT</th> 
                    <th style="width: 20%;text-align: left;border:1px solid black;">CONTACT</th>
                    <th style="width: 25%;text-align: left;border:1px solid black;">MONTANT</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=$row['mnt_crce'];
                    $grandTotal +=$row['mnt_crce'];
                    ?>
                    <tr >
                        <td style="width: 15%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['date_fact'])); ?><br/><?php echo $row['heure_fact']; ?></td>
                        <td style="width: 40%;border:1px solid black; text-align: left">&nbsp;<?php echo strtolower($row['nom_clt']); ?></td>
                        <td style="width: 20%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['tel_clt'])); ?>  <?php /*if ($row['bl_fact_grt'] == 1) echo "(G)";*/ ?></td>
                        <td style="width: 25%;border:1px solid black; text-align: right;"><?php echo number_format($row['mnt_crce'], 2, ',', ' '); ?></td>
                    </tr>

                    <?php
                }
                ?> 
            </table>

            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 60%; text-align: right;">Total garanties : </th>
                    <th style="width: 40%; text-align: right;"><?php echo number_format($total, 2, ',', ' '); ?> F</th>
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
    }else {
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
            $condmag = " AND fv.caissier_fact in (SELECT id_user FROM t_user where mag_user=" . intval($rowmag['id_mag']) . ")";

            $query = "SELECT  
            fv.code_caissier_fact,
            fv.login_caissier_fact,
            fv.bl_fact_grt,
            SUM(fv.crdt_fact-fv.som_verse_crdt-fv.remise_vnt_fact) as mnt_crce,
            fv.code_fact,
            date(fv.date_fact) as date_fact,
            time(fv.date_fact) as heure_fact,
            cl.code_clt,
            cl.nom_clt,
            cl.tel_clt,
            m.code_mag
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             inner join t_magasin m on fv.mag_fact=m.id_mag
            WHERE fv.bl_fact_crdt=1  AND bl_fact_grt=1 AND fv.bl_crdt_regle=0 AND fv.sup_fact=0 AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt-fv.remise_vnt_fact)>0 $condmag ";


            if (!empty($search['cs']))
                $query.=" AND fv.code_caissier_fact='" . $search['cs'] . "'";

            if (!empty($search['mg']))
                $query.=" AND fv.mag_fact=" . intval($search['mg']);

            if (!empty($search['clt']))
                $query.=" AND cl.id_clt=" . intval($search['clt']);

            if (!empty($search['grt']))
                $query.=" AND fv.bl_fact_grt=1";

            if (!empty($search['d']) && empty($search['f']))
                $query.=" AND date(fv.date_fact)='" . isoToMysqldate($search['d']) . "'";

            if (!empty($search['f']))
                $query.=" AND date(fv.date_fact) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

            $query.=" GROUP BY cl.id_clt ORDER BY cl.nom_clt,fv.date_fact ASC";


            $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $result = array();
                $total = 0;
                ?>
                <div style="font-size: 16pt;text-transform: capitalize; 
                 font-weight: bold;" align="left">
                <u>GARANTIES</u>   
            </div> 
                <br/>  
                <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                    <tr style="">
                        <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
                        <th style="width: 40%;text-align: left;border:1px solid black;">CLIENT</th> 
                        <th style="width: 20%;text-align: left;border:1px solid black;">CONTACT</th>
                        <th style="width: 25%;text-align: left;border:1px solid black;">MONTANT</th>  
                    </tr>


                    <?php
                    while ($row = $r->fetch_assoc()) {
                        $total +=$row['mnt_crce'];
                        $grandTotal +=$row['mnt_crce'];
                        $grandTotalMagasin +=$row['mnt_crce'];
                        ?>
                        <tr >
                            <td style="width: 15%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['date_fact'])) ?><br/><?php echo $row['heure_fact']; ?></td>
                            <td style="width: 40%;border:1px solid black; text-align: left">&nbsp;<?php echo strtolower($row['nom_clt']); ?></td>
                            <td style="width: 20%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['tel_clt'])); ?>  <?php /*if ($row['bl_fact_grt'] == 1) echo "(G)";*/ ?></td>
                            <td style="width: 25%;border:1px solid black; text-align: right;"><?php echo number_format($row['mnt_crce'], 2, ',', ' '); ?></td>
                        </tr>

                        <?php
                    }
                    ?> 
                </table>

                <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                    <tr>
                        <th style="width: 60%; text-align: right;">Total garanties : </th>
                        <th style="width: 40%; text-align: left;"><?php echo number_format($total, 2, ',', ' '); ?> F</th>
                    </tr>
                </table>
            
                <br>  
                <br/>
                
           
                 
                <?php
             } /* num_rows */
          ?>
                <table cellspacing="0" style="width: 60%; border-bottom: solid 1px black; background: #E7E7E7; text-align: right; font-size: 20pt;" align="right">
                <tr>
                    <th style="width: 100%; text-align: right;background-color:#E7E7E7;">TOTAL : <?php echo number_format($grandTotal, 0, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
            <br/>
            <br/>
            <br/>
                  
             <?php
             
                    }/* fin du while */
                    ?>
                 <br/>
        <table cellspacing="0" style="width: 100%; border:  1px double black; background: #E7E7E7; text-align: center; font-size: 20pt;" align="center">
            <tr>
                <th style="width: 40%; text-align: right;background-color:#E7E7E7;">TOTAL GENERAL</th>
                <th style="width: 60%; text-align: right;background-color:#E7E7E7;"><?php echo number_format($grandTotalMagasin, 0, ',', ' '); ?> FCFA</th>
            </tr>
        </table> 
                <?php
    }/* fin du else */
    ?>
</page> 