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
<page backtop="30mm" backbottom="10mm" backleft="20mm" backright="20mm" backcolor="#fff" style="font: arial;">
     <page_header>
        <?php
        include "includes/header.php";
        ?>
    </page_header>
    <page_footer>
        <?php include "includes/footer.php"; ?>
    </page_footer>  
   
    <table cellspacing="0" style="
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                RAPPORT DETAILLE DES GARANTIES
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 14px;text-transform: capitalize; 
         font-weight: bold;" align="right">
        Ouagadougou, le <?php echo date("d/m/Y"); ?>

    </div> 
     <?php
    $totalgaranties=0;
    $reksql = "SELECT DISTINCT(cl.id_clt),cl.nom_clt,cl.code_clt,cl.bp_clt,cl.tel_clt,cl.mob_clt,cl.adr_clt
        FROM t_client cl INNER JOIN t_facture_vente f ON cl.id_clt=f.clnt_fact
        WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=1 AND f.sup_fact=0 AND f.bl_encaiss_grt=0";
    
    $rrek = $Mysqli->query($reksql);
    
     while ($rowecl = $rrek->fetch_assoc()) {
         
    $client = $rowecl['id_clt'];
  
    $nom = $rowecl['nom_clt'];
    $bp = $rowecl['bp_clt'];
    $code = $rowecl['code_clt'];
    $tel = $rowecl['tel_clt'];
    $mob = $rowecl['mob_clt'];
    $adr = $rowecl['adr_clt'];
    ?>
    <?php
    //include "includes/header-fact-tva.php"; 
    ?>
    <br>
    <table style="width:100%;background-color:#aaa">
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
<br/>
<?php
$query1 = "SELECT DISTINCT(fv.id_fact), 
            fv.code_caissier_fact,fv.bl_fact_grt,
             (fv.crdt_fact-fv.som_verse_crdt) as mnt_crce,
            fv.bl_tva,fv.bl_bic,fv.tva_fact,fv.bic_fact, cl.exo_tva_clt,fv.som_verse_crdt,fv.remise_vnt_fact,
            fv.code_fact,
            date(fv.date_fact) as date_fact            
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
             INNER JOIN t_vente v ON fv.id_fact=v.facture_vnt
            WHERE fv.clnt_fact=$client AND fv.sup_fact=0 AND fv.bl_fact_crdt=1 AND fv.bl_fact_grt=1 AND fv.bl_encaiss_grt=0 ORDER BY fv.date_fact ASC";
 
$r1 = $Mysqli->query($query1) or die($this->mysqli->error . __LINE__);
$totalGenerale = 0;
while ($row = $r1->fetch_assoc()) {
    $b_id_fact = $row['id_fact'];
    $b_code_fact = $row['code_fact'];
    $b_caissier = $row['code_caissier_fact'];
    $b_date = $row['date_fact'];
    $b_mnt_rest = $row['mnt_crce'];
    $totalGenerale +=$b_mnt_rest;
    $totalgaranties +=$b_mnt_rest;
    $bl_fact_grt = $row['bl_fact_grt'];
    $bl_tva=$row['bl_tva'];
    $tva_fact=$row['tva_fact'];
    $bic_fact=$row['bic_fact'];
    $bl_bic=$row['bl_bic'];
    $som_vers=$row['som_verse_crdt'];
    $b_remise=$row['remise_vnt_fact'];
    $exo=$row['exo_tva_clt'];
    ?>
    <table cellspacing="0" style="
           width: 100%;">
        <tr >
            <th style="border-bottom:1px solid black;">
                FACTURE No</th>
            <th style="border-bottom:1px solid black;">: <?php echo $b_code_fact; ?> /</th>
            <th style="border-bottom:1px solid black;">DATE</th>
            <th style="border-bottom:1px solid black;">: <?php echo $b_date; ?> &nbsp; 
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
                $total +=$row['qte_vnt']*$row['pu_theo_vnt'];
                ?>
         <tr >
             <td style="width: 60%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
             <td style="width: 12%; border:1px solid black;text-align: right"><?php echo number_format($row['pu_theo_vnt'],0,',',' '); ?></td>
            <td style="width: 9%;border:1px solid black;text-align: right"><?php echo $row['qte_vnt']; ?></td>
            <td style="width: 19%;border:1px solid black; text-align: right;"><?php echo number_format($row['qte_vnt']*$row['pu_theo_vnt'],0,',',' '); ?></td>
        </tr>
    
    <?php 
        }  
    ?> 
  </table> 
 <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
        <tr>
            <th style="width: 78%; text-align: right;">Total : </th>
            <th style="width: 22%; text-align: right;"><?php echo number_format($total, 0, ',', ' '); ?> FCFA</th>
        </tr>
    </table>
<?php if($bl_tva==1): ?> 
 <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 5pt;font-style: italic;font-weight: normal;">
        <tr>
            <th style="width: 78%; text-align: right;">
                <?php if($exo==1) echo "[Exho] Tva";
                    else echo "+ Tva :"; 
                    ?>
            </th>
            <th style="width: 22%; text-align: right;"><?php echo number_format($tva_fact, 0, ',', ' '); ?> FCFA</th>
        </tr>
    </table>
<?php endif; ?> 
<?php if($bl_bic==1): ?> 
 <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 5pt;font-style: italic;font-weight: normal;">
        <tr>
            <th style="width: 78%; text-align: right;">
                <?php if($exo==1) echo "[Exho] Bic";
                    else echo "+ Bic :"; 
                    ?>
            </th>
            <th style="width: 22%; text-align: right;"><?php echo number_format($bic_fact, 0, ',', ' '); ?> FCFA</th>
        </tr>
    </table>
<?php endif; ?> 
<?php if($b_remise>0): ?> 
 <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 5pt;font-style: italic;font-weight: normal;">
        <tr>
            <th style="width: 78%; text-align: right;"><em>- Remise</em></th>
            <th style="width: 22%; text-align: right;"><?php echo number_format($b_remise, 0, ',', ' '); ?> FCFA</th>
        </tr>
    </table>
<?php endif; ?> 
<?php if($som_vers>0): ?> 
 <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 5pt;font-style: italic;font-weight: normal;">
        <tr>
            <th style="width: 78%; text-align: right;"><em>- Reglements</em></th>
            <th style="width: 22%; text-align: right;"><?php echo number_format($som_vers, 0, ',', ' '); ?> FCFA</th>
        </tr>
    </table>
<?php endif; ?> 
   <table cellspacing="0" style="  width: 100%;" align="right">
        <tr>
            <th style="border-bottom:1px solid black;background-color:#eee;">RESTE :</th>
            <th style="border-bottom:1px solid black;background-color:#bbb;"><?php echo number_format($b_mnt_rest,0,',',' '); ?> FCFA</th>
        </tr>
    </table>
 
<!-- fin de la liste des articles -->
<br/><br/>
    <?php
}
?> 
<table cellspacing="0" style="width: 50%; border: double 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;" align="right">
        <tr>
            <th style="width: 35%; text-align: left;background-color:#E7E7E7;">MONTANT</th>
            <th style="width: 65%; text-align: right;background-color:#bbb;"><?php echo number_format($totalGenerale, 0, ',', ' '); ?> FCFA</th>
        </tr>
</table>

<br/>
<br/>
<br/>
<br/>
     <?php }/* fin du while des clients*/ ?>

<table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
        <tr>
            <th style="width: 78%; text-align: Left;background-color:#E7E7E7;">TOTAL GARANTIES : </th>
            <th style="width: 22%; text-align: right;background-color:#bbb;"><?php echo number_format($totalgaranties, 0, ',', ' '); ?> FCFA</th>
        </tr>
</table>
</page>

