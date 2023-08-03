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
        font-size:18px;
        font-weight: bold;
        color : #000;
    }

    table tr th.chiffre{
        font-size:15px;
        font-weight: bold;
    }
    table tr td.chiffre{
        font-size:15px;
        font-weight: bold;
    }

    .titre-formule{
        font-size:14px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .formule{
        font-size:14px; 
    }

    table tr td.signataire{
        font-size:12px;
        font-weight: bold;
        font-style: italic; 
    }

    table tr td{
        font-size:14px; 
        color : #000;
    }
</style>
<!--<page format="120x200" orientation="L" backcolor="#fff" style="font: arial;">-->
<page orientation="L" backtop="30mm" format="A5" backbottom="10mm" backleft="10mm" backright="10mm">
    <page_header>
        <?php
        include "includes/header.php";
        ?>
    </page_header>
    <page_footer>
        <?php include "includes/footer.php"; ?>
    </page_footer>  
    <?php
    $fact = $_GET['f'];

    $idcr = intval($_GET['r']);

    $query = "SELECT c.id_clt,
                     c.code_clt,
                     c.nom_clt,
                     c.adr_clt,
                     c.tel_clt,
                     c.mob_clt,
                     c.bp_clt,
                     m.nom_mag
                     FROM t_client c 
                     INNER JOIN t_creance_client cr ON cr.clnt_crce=c.id_clt
                     INNER JOIN t_facture_vente f ON cr.fact_crce_clnt=f.id_fact
                     INNER JOIN t_magasin m ON f.mag_fact=m.id_mag
                           WHERE cr.id_reg='$fact'  limit 1";

    $r = $Mysqli->query($query);
    $result = $r->fetch_assoc();
    // print_r($query);
    $nom = $result['nom_clt'];
    $bp = $result['bp_clt'];
    $code = $result['code_clt'];
    $tel = $result['tel_clt'];
    $mob = $result['mob_clt'];
    $adr = $result['adr_clt'];
    $mag = $result['nom_mag'];
    ?>
    <?php
    //include "includes/header-fact-tva.php"; 
    ?>
    <table cellspacing="0" style="
           width: 100%; border: solid 1px black; margin-top: 5mm;
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                ACCUSE DE REGLEMENT :::/::: <?php echo "<span style='background-color:#ccc'>[ " . strtoupper($mag) . " ]</span>"; ?>
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 14px;text-transform: capitalize; 
         font-weight: bold;" align="right">
        le <?php echo date("d/m/Y"); ?>

    </div> 
    <br>
    <table style="width:100%;">
        <tr>
            <td style="width: 100%">
                <table style="width:98%;" align='center' border="0">
                    <tr>
                        <td   style="width:56%;text-align:left;border:1px solid black;font-size:8px;font-weight:bold;"> 
                            <table>
                                <tr>
                                    <td><u>CLIENT</u></td>
                        <td>: <?php echo strtoupper($nom); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> <?php echo strtoupper($code); ?> </td>
                    </tr>
                </table>
            </td>
            <td   style="width:4%;text-align:left;">&nbsp;</td>
            <td   style="width:43%;text-align:left;border:1px solid black;font-size:8px;font-weight:bold;">
                <table>
                    <tr>
                        <td><u>TEL</u></td>
            <td>: <?php echo strtoupper($tel); ?></td>
        </tr>
        <tr>
            <td><u>MOBILE</u></td>
        <td>: <?php echo strtoupper($mob); ?></td>
        </tr>
        <tr>
            <td><u>ADRESSE</u></td>
        <td>: BP <?php echo strtoupper($bp); ?> </td>
        </tr>
    </table>
</td>
</tr>
</table>

</td>
</tr>
</table>
<!---  RESUMEE  ---->
<br/>
<br/>
<?php
$query2 = "SELECT c.clnt_crce,c.mnt_glob_crce_clnt,date(c.date_crce_clnt) as date,
        time(c.date_crce_clnt) as heure
                            FROM t_creance_client c 
                          INNER JOIN t_facture_vente f ON c.fact_crce_clnt=f.id_fact
                         WHERE c.id_crce_clnt=$idcr Limit 1";

$r2 = $Mysqli->query($query2) or die($this->mysqli->error . __LINE__);
$row2 = $r2->fetch_assoc();

$id_clt = $row2['clnt_crce'];

$query3 = "SELECT  
            SUM(fv.crdt_fact-fv.som_verse_crdt) as mnt_reste_global
            FROM t_facture_vente fv 
            WHERE fv.clnt_fact=$id_clt and fv.crdt_fact>0 
                AND (fv.crdt_fact-fv.som_verse_crdt)>0 
                and fv.bl_fact_crdt=1 AND fv.bl_crdt_regle=0 AND fv.sup_fact=0 Limit 1";


$r3 = $Mysqli->query($query3) or die($this->mysqli->error . __LINE__);
$row3 = $r3->fetch_assoc();
?>

<table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
    <tr style="">
        <th style="width: 20%;text-align: left;border:1px solid black;">DATE</th>
        <th style="width: 20%;text-align: left;border:1px solid black;">HEURE</th>
        <th style="width: 25%;text-align: left;border:1px solid black;background-color:#bbb;">AVANCE</th> 
        <th style="width: 35%;text-align: left;border:1px solid black;">RESTE GENERAL</th> 
    </tr> 
</table> 

<table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
    <tr >
        <td style="width: 20%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row2['date'])); ?></td>
        <td style="width: 20%;border:1px solid black; text-align: right"><?php echo $row2['heure']; ?></td>
        <td style="width: 25%;border:1px solid black; text-align: right;background-color:#bbb;"><?php echo number_format($row2['mnt_glob_crce_clnt'], 0, ',', ' '); ?></td>
        <td style="width: 35%;border:1px solid black; text-align: right;"><?php echo number_format($row3['mnt_reste_global'], 0, ',', ' '); ?></td>
    </tr> 
</table>
<br/>
Avance/reglement recu pour la somme de (F CFA) :  <strong><?php echo ConvLetter($row2['mnt_glob_crce_clnt'], 0, 'fr') . "<span class='titre-formule'>(" . number_format($row2['mnt_glob_crce_clnt'], 0, ',', ' ') . ")</span>"; ?></strong>

<br/><br/>
<div><i>Cher Client,</i></div>
<div><i>Nous avons bien re&ccedil;u votre avance/reglement et nous vous remercions. Veuillez prendre note des &eacute;ch&eacute;ances auxquelles elle se rapporte..</i></div>


<br/>
<?php
$query1 = "SELECT  
            fv.code_caissier_fact,fv.bl_fact_grt,
             (fv.crdt_fact-fv.som_verse_crdt) as mnt_crce,
            fv.bl_tva,fv.bl_bic,fv.tva_fact,fv.bic_fact, cl.exo_tva_clt,fv.som_verse_crdt,fv.remise_vnt_fact,
            fv.code_fact,fv.id_fact,
            date_format(fv.date_fact,'%d %b %Y') as date_fact,           
            time(fv.date_fact) as heure_fact            
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            WHERE fv.id_fact IN (SELECT DISTINCT fact_crce_clnt FROM t_creance_client WHERE id_reg='$fact')  AND fv.sup_fact=0 and fv.crdt_fact>0 ORDER BY fv.date_fact DESC";

$r1 = $Mysqli->query($query1) or die($this->mysqli->error . __LINE__);
$totalGenerale = 0;
while ($row = $r1->fetch_assoc()) {
    $b_id_fact = $row['id_fact'];
    $b_code_fact = $row['code_fact'];
    $b_caissier = $row['code_caissier_fact'];
    $b_date = $row['date_fact'];
    $b_heure = $row['heure_fact'];
    $b_mnt_rest = $row['mnt_crce'];
    $totalGenerale +=$b_mnt_rest;
    $bl_fact_grt = $row['bl_fact_grt'];
    $bl_tva = $row['bl_tva'];
    $tva_fact = $row['tva_fact'];
    $bic_fact = $row['bic_fact'];
    $bl_bic = $row['bl_bic'];
    $som_vers = $row['som_verse_crdt'];
    $b_remise = $row['remise_vnt_fact'];
    $exo = $row['exo_tva_clt'];
    ?>
    <table cellspacing="0" style="
           width: 100%;">
        <tr >
            <th style="border-bottom:1px solid black;">
                FACTURE No</th>
            <th style="border-bottom:1px solid black;">: <?php echo $b_code_fact; ?> /</th>
            <th style="border-bottom:1px solid black;">DATE</th>
            <th style="border-bottom:1px solid black;">: <?php echo $b_date . " " . $b_heure; ?> &nbsp;
    <?php if ($bl_fact_grt == 1) echo "<span style='color:red;'>[GARANTIE]</span>"; ?>
            </th> 
        </tr>
    </table>
    <br/>
    <!-- debut de la liste des articles -->
    <span class="right"><b>VENDEUR/CAISSIER :</b> <?php echo strtoupper($b_caissier); ?></span>
    <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
        <tr style="">
            <th style="width: 60%;text-align: left;border:1px solid black;">Designation</th>
            <th style="width: 12%;text-align: left;border:1px solid black;">Pu</th>
            <th style="width: 9%;text-align: left;border:1px solid black;">Qte</th>
            <th style="width: 19%;text-align: left;border:1px solid black;">Mnt</th> 
        </tr>

    <?php
    $query2 = "SELECT a.nom_art,
                          v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt
                         FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                         WHERE f.id_fact=$b_id_fact
                        ORDER BY v.id_vnt DESC,a.nom_art ASC";

    $r2 = $Mysqli->query($query2) or die($this->mysqli->error . __LINE__);

    $total = 0;
    ?>


        <?php
        while ($row = $r2->fetch_assoc()) {
            $total +=$row['qte_vnt'] * $row['pu_theo_vnt'];
            ?>
            <tr >
                <td style="width: 60%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                <td style="width: 12%;border:1px solid black; text-align: center"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                <td style="width: 9%;border:1px solid black;text-align: center"><?php echo $row['qte_vnt']; ?></td>
                <td style="width: 19%;border:1px solid black; text-align: center;"><?php echo number_format($row['qte_vnt'] * $row['pu_theo_vnt'], 0, ',', ' '); ?></td>
            </tr>

        <?php
    }
    ?> 
    </table> 
    <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
        <tr>
            <th style="width: 78%; text-align: right;">Total : </th>
            <th style="width: 22%; text-align: center;"><?php echo number_format($total, 0, ',', ' '); ?></th>
        </tr>
    </table>
    <?php if ($b_remise > 0): ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 5pt;font-style: italic;font-weight: normal;">
            <tr>
                <th style="width: 78%; text-align: right;"><em>- Remise</em></th>
                <th style="width: 22%; text-align: right;"><?php echo number_format($b_remise, 0, ',', ' '); ?></th>
            </tr>
        </table>
    <?php endif; ?>
    <?php if ($bl_tva == 1): ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 5pt;font-style: italic;font-weight: normal;">
            <tr>
                <th style="width: 78%; text-align: right;">
        <?php
        if ($exo == 1)
            echo "[Exho] Tva";
        else
            echo "+ Tva :";
        ?>
                </th>
                <th style="width: 22%; text-align: right;"><?php echo number_format($tva_fact, 0, ',', ' '); ?> </th>
            </tr>
        </table>
    <?php endif; ?> 
    <?php if ($bl_bic == 1): ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 5pt;font-style: italic;font-weight: normal;">
            <tr>
                <th style="width: 78%; text-align: right;">
                    <?php
                    if ($exo == 1)
                        echo "[Exho] Bic";
                    else
                        echo "+ Bic :";
                    ?>
                </th>
                <th style="width: 22%; text-align: right;"><?php echo number_format($bic_fact, 0, ',', ' '); ?> </th>
            </tr>
        </table>
    <?php endif; ?>  
    <?php if ($som_vers > 0): ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 5pt;font-style: italic;font-weight: normal;">
            <tr>
                <th style="width: 78%; text-align: right;"><em>- Reglements</em></th>
                <th style="width: 22%; text-align: right;"><?php echo number_format($som_vers, 0, ',', ' '); ?> </th>
            </tr>
        </table>
    <?php endif; ?> 
    <table cellspacing="0" style="  width: 100%;" align="right">
        <tr>
            <th style="border-bottom:1px solid black;background-color:#eee;">RESTE :</th>
            <th style="border-bottom:1px solid black;background-color:#bbb;"><?php echo number_format($b_mnt_rest, 0, ',', ' '); ?> </th>
        </tr>
    </table>

    <!-- fin de la liste des articles -->
 
    <?php
}
?> 

</page>

