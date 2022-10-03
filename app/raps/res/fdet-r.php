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
<page backtop="15mm" backbottom="10mm" backleft="20mm" backright="20mm" backcolor="#fff" style="font: arial;">
    <page_footer>
        <?php include "includes/footer.php"; ?>
    </page_footer>  
    <?php
    $client = intval($_GET['c']);

    $query = "SELECT c.id_frns,
                     c.code_frns,
                     c.nom_frns,
                     c.adr_frns,
                     c.tel_frns,
                     c.mob_frns,
                     c.bp_frns
                     FROM t_fournisseur c 
                           WHERE c.id_frns=$client  limit 1";

     $r = $Mysqli->query($query);
    $result = $r->fetch_assoc();
    $nom = $result['nom_frns'];
    $bp = $result['bp_frns'];
    $code = $result['code_frns'];
    $tel = $result['tel_frns'];
    $mob = $result['mob_frns'];
    $adr = $result['adr_frns'];
    ?>
    <?php
    //include "includes/header-fact-tva.php"; 
    ?>
    <table cellspacing="0" style="
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                ETAT DETTES FOURNISSEUR
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
                                    <td><u>FOURNISSEUR</u></td>
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

      $query1 = "SELECT 
            ap.code_user_appro,
            ap.login_appro,
            (ap.mnt_revient_appro-ap.som_verse_dette) as mnt_dette ,
            ap.som_verse_dette as som_verse_dette ,
            ap.id_appro,
            ap.bon_liv_appro,
              date_format(ap.date_appro,'%d %b %Y') as date_appro,           
            time(ap.date_appro) as heure_appro ,  
            f.code_frns,
            f.nom_frns 
            FROM t_approvisionnement ap  
            INNER JOIN t_fournisseur f ON ap.frns_appro=f.id_frns
            WHERE f.id_frns=$client 
                AND ap.mnt_revient_appro>0
                AND (ap.mnt_revient_appro-ap.som_verse_dette)>0
                AND ap.bl_bon_dette=1 
                AND ap.bl_dette_regle=0 
              ORDER BY ap.date_appro DESC";
 
$r1 = $Mysqli->query($query1) or die($this->mysqli->error . __LINE__);
$totalGenerale = 0;
while ($row = $r1->fetch_assoc()) {
    $b_id_appro = $row['id_appro'];
    $b_bon_appro = $row['bon_liv_appro'];
    $b_caissier = $row['login_appro'];
    $b_date = $row['date_appro'];
    $b_heure = $row['heure_appro'];
    $b_mnt_rest = $row['mnt_dette'];
    $totalGenerale +=$b_mnt_rest; 
    $som_vers=$row['som_verse_dette']; 
    ?>
    <table cellspacing="0" style="
           width: 100%;">
        <tr >
            <th style="border-bottom:1px solid black;">
                Bon No</th>
            <th style="border-bottom:1px solid black;">: <?php echo $b_bon_appro; ?> /</th>
            <th style="border-bottom:1px solid black;">DATE</th>
            <th style="border-bottom:1px solid black;">: <?php echo $b_date. " ".$b_heure; ?> &nbsp;
            </th> 
        </tr>
    </table>
<br/>
<!-- debut de la liste des articles -->
<span class="right"><b>Compte :</b> <?php echo strtoupper($b_caissier); ?></span>
<table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
        <tr style="">
              <th style="width: 90%;text-align: left;border:1px solid black;">Designation</th>
             <!--<th style="width: 12%;text-align: left;border:1px solid black;">Pu</th>-->
            <th style="width: 10%;text-align: left;border:1px solid black;">Qte</th>
            <!--<th style="width: 19%;text-align: left;border:1px solid black;">Mnt</th>--> 
        </tr>
        
        <?php
    
         $query2 = "SELECT a.nom_art,
                          v.qte_appro_art
                         FROM t_approvisionnement_article v 
                         INNER JOIN t_article a ON v.art_appro_art=a.id_art
                         INNER JOIN t_approvisionnement f ON v.appro_appro_art=f.id_appro
                         WHERE f.id_appro=$b_id_appro
                        ORDER BY v.id_appro_art DESC,a.nom_art ASC";

        $r2 = $Mysqli->query($query2) or die($this->mysqli->error . __LINE__);
 
         $total = 0;
            ?>
    
    
            <?php 
            while ($row = $r2->fetch_assoc()) {
                //$total +=$row['qte_vnt']*$row['pu_theo_vnt'];
                ?>
         <tr >
             <td style="width: 90%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
             <!--<td style="width: 12%;border:1px solid black; text-align: center"><?php echo number_format($row['pu_theo_vnt'],0,',',' '); ?></td>-->
            <td style="width: 10%;border:1px solid black;text-align: center"><?php echo $row['qte_appro_art']; ?></td>
            <!--<td style="width: 19%;border:1px solid black; text-align: center;"><?php echo number_format($row['qte_vnt']*$row['pu_theo_vnt'],0,',',' '); ?></td>-->
        </tr>
    
    <?php 
        }  
    ?> 
  </table> 
<!-- <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
        <tr>
            <th style="width: 78%; text-align: right;">Total : </th>
            <th style="width: 22%; text-align: center;"><?php echo number_format($total, 0, ',', ' '); ?></th>
        </tr>
    </table>-->
  
<?php if($som_vers>0): ?> 
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
            <th style="border-bottom:1px solid black;background-color:#bbb;"><?php echo number_format($b_mnt_rest,0,',',' '); ?> </th>
        </tr>
    </table>
 
<!-- fin de la liste des articles -->
<br/><br/>
    <?php
}
?> 
<table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
        <tr>
            <th style="width: 78%; text-align: Left;background-color:#E7E7E7;">TOTAL DETTES : </th>
            <th style="width: 22%; text-align: right;background-color:#bbb;"><?php echo number_format($totalGenerale, 0, ',', ' '); ?> </th>
        </tr>
</table>

<br/>

</page>

