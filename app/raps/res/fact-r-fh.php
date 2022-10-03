<?php
session_name('SessSngS');
session_start();
include ("includes/db.php");
?>
<?php
include "includes/const.php";
?>
<style>

    table tr td{
        padding-right:2mm;
        padding-left:2mm;
        vertical-align: middle;
    }
    table tr th{
        padding:1.5mm;
    }
    table tr th{
        font-size:12px;/*18px;*/
        font-weight: bold;
        color : #000;
    }

    table tr th.chiffre{
        font-size:11px;/*15px;*/
        font-weight: bold;
    }
    table tr td.chiffre{
        font-size:11px;/*15px;*/
        font-weight: bold;
    }

    .titre-formule{
        font-size:11px;/*14px;*/
        font-weight: bold;
        text-transform: uppercase;
    }
    .formule{
        font-size:11px;/*14px; /
    }

    table tr td.signataire{
        font-size:11px;/*12px;*/
        font-weight: bold;
        font-style: italic; 
    }

    table tr td{
        font-size:10px;/*14px; */
        color : #000;
    }
</style>
<!--<page format="140x200" orientation="P" backcolor="#fff" style="font: arial;">-->
<page backtop="5mm" format="A5" orientation="L" backbottom="5mm" backleft="10mm" backright="10mm" backcolor="#fff" style="font: arial;">
    <page_footer>
        <?php include "includes/footer.php"; ?>
    </page_footer>  
    <?php
    $condMag = "";
    $fact = intval($_GET['f']);

    $query = "SELECT f.id_fact,f.bl_fact_crdt,f.bl_tva,
        f.bl_bic,f.remise_vnt_fact,f.code_fact,
        COALESCE(c.nom_clt,'- - - - - - -') as nom_clt,
        COALESCE(c.bp_clt,'- - - - - - -') as bp_clt,
          m.nom_mag,f.code_caissier_fact,DATE(f.date_fact) as date_fact,time(f.date_fact) as heure_fact,
        m.nom_mag
                           FROM 
                           t_facture_vente f
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.id_fact=$fact  limit 1";


    $r = $Mysqli->query($query);
    $result = $r->fetch_assoc();
    $nom = $result['nom_clt'];
    $bp = $result['bp_clt'];
    $num = $result['code_fact'];
    $date = $result['date_fact'];
    $heure = $result['heure_fact'];
    $magasin = $result['nom_mag'];
    $login = $result['code_caissier_fact'];
    $remise = intval($result['remise_vnt_fact']);
    $bltva = $result['bl_tva'];
    $blbic = $result['bl_bic'];
    $blcrdt = $result['bl_fact_crdt'];
    ?>
    <?php
    // if ($bltva==1 )
    include "includes/header-fact-tva.php";
    // else
    //include "includes/header-fact.php";
    ?>
    <table style="width:100%;">
        <tr>
            <td style="width: 100%">

                <?php
                $query = "SELECT a.code_art,a.nom_art,
                         v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt 
                          FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE f.id_fact=$fact ORDER BY a.nom_art ASC";


                $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $result = array();
                $total = 0;
                $som = 0;
                ?>
                <table style="width:98%;" align='center'>
                    <tr>
                        <td coslpan='3'><u><h5>&nbsp;FACTURE No <?php echo $num; ?>/<?php $dt = explode('-', $date);
                echo $dt[0];
                ?>/<?php echo NOM_STRUCT_ABBR; ?></h5></u>  </td>
        </tr>
    </table>
     
<?php //if ($bltva==1 ):  ?>
    <table style="width:98%;" align='center' border="0">
        <tr>
            <td   style="width:50%;text-align:left;border:1px solid black;font-size:8px;font-weight:bold;"> 
                <table>
                    <tr>
                        <td><u>DOIT</u></td>
            <td>: <?php echo strtoupper($nom); ?></td>
        </tr>
        <tr>
            <td></td>
            <td> <?php echo strtoupper($bp); ?></td>
        </tr>
    </table>
</td>
<td   style="width:4%;text-align:left;">&nbsp;</td>
<td   style="width:46%;text-align:left;border:1px solid black;font-size:8px;font-weight:bold;">
    <table>
        <tr>
            <td>REGIME D'IMPOSITION</td>
            <td>: <?php echo REG_IMP; ?></td>
        </tr>
        <tr>
            <td>DIVISION FISCALE</td>
            <td>: <?php echo DIV_FISC; ?></td>
        </tr>
        <tr>
            <td>SITUATION GEO</td>
            <td>: <?php echo SIT_GEO; ?> </td>
        </tr>
    </table>
</td>
</tr>
</table> 
<br/>
 <table style="width:98%" align="center" cellspacing="0">
    <tr style="color:black;">
        <th style="width: 57%;text-align: left;border:1px solid black;">Designation</th>
        <th style="width: 9%;text-align: center;border:1px solid black;">Qte</th>
        <th style="width: 17%;text-align: left;border:1px solid black;">PU</th>
        <th style="width: 17%;text-align: left;border:1px solid black;">Total</th>
    </tr> 

    <?php
    while ($row = $r->fetch_assoc()) {
        $total +=1;
        $som +=$row['qte_vnt'] * $row['pu_theo_vnt'];
        ?>
        <tr >
            <td  style="width: 57%; text-align: left;border-left:1px solid black;font-weight:bold;"><?php echo strtoupper($row['nom_art']); ?></td>
            <td class="chiffre" style="width: 9%;text-align: center;border-left:1px solid black;"><?php echo $row['qte_vnt']; ?></td>
            <td class="chiffre" style="width: 17%; text-align: right;border-left:1px solid black;"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
            <td class="chiffre" style="width: 17%; text-align: right;border-left:1px solid black;border-right:1px solid black;"><?php echo number_format($row['qte_vnt'] * $row['pu_theo_vnt'], 0, ',', ' '); ?></td>
        </tr>

        <?php
    }
    ?> 
    <tr >
        <td style="width: 57%; text-align: left;border-left:1px solid black;">
            
        </td>
        <td style="width: 9%;text-align: right;border-left:1px solid black;"></td>
        <td style="width: 17%; text-align: right;border-left:1px solid black;"></td>
        <td style="width: 17%; text-align: right;border-left:1px solid black;;border-right:1px solid black;"></td>
    </tr>
</table>
<table align="center" cellspacing="0" style="width: 98%; border: solid 1px black; background: #fff; text-align: center; font-size: 10pt;">
<?php if ($bltva == 1): ?>
        <tr>
            <th style="width: 83%; text-align: right;border-right:1px solid black;">TOTAL HT : </th>
            <th class="chiffre" style="width: 17%; text-align: right;font-style:italic;"><?php echo number_format($som, 0, ',', ' '); ?></th>
        </tr> 
    <?php endif; ?>
<?php if ($blbic == 1): ?>
        <tr>
            <th style="width: 83%; text-align: right;border-right:1px solid black;">TVA (18%) : </th>
            <th class="chiffre" style="width: 17%; text-align: right;font-style:italic;"><?php echo number_format($som * 0.18, 2, ',', ' '); ?></th>
        </tr>
        <tr>
            <th style="width: 83%; text-align: right;border-right:1px solid black;">BIC (2%) : </th>
            <th class="chiffre" style="width: 17%; text-align: right;font-style:italic;"><?php echo number_format($som * 0.18 * 0.02, 2, ',', ' '); ?></th>
        </tr>
    <?php endif; ?>
<?php if ($blbic == 0 && $bltva == 1): ?>
        <tr>
            <th style="width: 83%; text-align: right;border-right:1px solid black;">TVA (18%) : </th>
            <th class="chiffre" style="width: 17%; text-align: right;font-style:italic;"><?php echo number_format($som * 0.18, 2, ',', ' '); ?></th>
        </tr> 
    <?php endif; ?> 
<?php if ($remise > 0): ?>
        <tr>
            <th style="width: 83%; text-align: right;border-right:1px solid black;color:grey">Remise : </th>
            <th class="chiffre" style="width: 17%; text-align: right;font-style:italic;"><?php echo number_format($remise, 0, ',', ' '); ?></th>
        </tr>
    <?php endif; ?> 
    <?php
    if ($blbic == 1)
        $som = $som + $som * 0.18 + $som * 0.18 * 0.02;
    if ($blbic == 0 && $bltva == 1)
        $som = $som + $som * 0.18;
    ?>

    <tr>
        <th style="width: 83%; text-align: right;border-right:1px solid black;"> <?php if ($bltva == 1)
        echo "TOTAL TTC";
    else
        echo "TOTAL HT";
    ?>: </th>
        <th class="chiffre" style="width: 17%; text-align: right;font-style:italic;"><?php echo number_format($som - $remise, 2, ',', ' '); ?></th>
    </tr>
</table>
<?php $mntt = $som - $remise; ?>
<table cellspacing="0" style="width: 98%;background: #fff; text-align: center; font-size: 8pt;" align="center">
    <tr>
        <td class="" style="width: 100%; text-align: left;"><em>Total : <?php echo $total; ?> article(s)</em></td>
    </tr>
    <tr>
        <td style="width: 100%; text-align: left;"><span class="titre-formule" ><u>Arr&Ecirc;t&Eacute; la presente facture &Agrave; la somme de :</u></span> &nbsp;<span class="formule"><?php echo ConvLetter($mntt, 0, 'fr') . "<span class='titre-formule'>(" . $mntt . ")</span>"; ?>  FRANCS/CFA TTC</span></td>
    </tr>
</table>
<br/>
<br/> 
<table cellspacing="0" style="width: 100%;background: #fff; text-align: center; font-size: 8pt;" align="center">
    <tr>
        <td class="signataire" style="width: 50%; text-align: center;"><strong>LE CLIENT</strong> 

        </td>
        <td class="signataire" style="width: 50%; text-align: center;">

            <strong> 
                <?php
                if ($_SESSION['userMag'] == 0)
                    echo " LE DIRECTEUR GENERAL";
                else
                    echo " LE DIRECTEUR COMMERCIAL";
                ?>
            </strong> 
             
        </td>
    </tr>
</table>

</td>
</tr>
</table>
</page>
 
 