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
    
    $fact = intval($_GET['f']);

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
                           WHERE cr.fact_crce_clnt=$fact  limit 1";
 
    $r = $Mysqli->query($query);
    $result = $r->fetch_assoc();
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
                ACCUSE DE REGLEMENT :::/::: <?php echo "<span style='background-color:#ccc'>[ ". strtoupper($mag). " ]</span>"; ?>
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
<br/>
<?php
$query1 = "SELECT  
            fv.code_caissier_fact,fv.bl_fact_grt,
             fv.bl_tva,fv.bl_bic,fv.tva_fact,fv.bic_fact, cl.exo_tva_clt,fv.som_verse_crdt,fv.remise_vnt_fact,
            fv.code_fact,fv.id_fact,
            date(fv.date_fact) as date_fact,           
            time(fv.date_fact) as heure_fact            
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            WHERE fv.id_fact=$fact limit 1";

$r1 = $Mysqli->query($query1) or die($this->mysqli->error . __LINE__);
 
 $row = $r1->fetch_assoc();
     $b_code_fact = $row['code_fact'];
    $b_caissier = $row['code_caissier_fact'];
    $b_date = $row['date_fact'];
    $b_heure = $row['heure_fact'];
     $bl_fact_grt = $row['bl_fact_grt']; 
    ?>
    <table cellspacing="0" style="
           width: 100%;">
        <tr >
            <th style="border-bottom:1px solid black;">
                FACTURE No</th>
            <th style="border-bottom:1px solid black;">: <?php echo $b_code_fact; ?> /</th>
            <th style="border-bottom:1px solid black;">DATE</th>
            <th style="border-bottom:1px solid black;">: <?php echo $b_date . " " . $b_heure; ?> &nbsp;
                <?php if ($bl_fact_grt == 1) echo "<span style='color:red;'>[GARANTIE]</span>";
                else echo "<span style='color:red;'>[CREDIT]</span>"; ?>
            </th> 
        </tr>
    </table>
    <br/>
    <!-- debut de la liste des articles -->
    <span class="right"><b>VENDEUR/CAISSIER :</b> <?php echo strtoupper($b_caissier); ?></span>

    <?php
    $query2 = "SELECT c.clnt_crce,c.mnt_paye_crce_clnt,date(c.date_crce_clnt) as date,time(c.date_crce_clnt) as heure,
        (f.crdt_fact-f.som_verse_crdt) as mnt_crce
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
            <th style="width: 15%;text-align: left;border:1px solid black;">DATE</th>
            <th style="width: 15%;text-align: left;border:1px solid black;">HEURE</th>
            <th style="width: 20%;text-align: left;border:1px solid black;">AVANCE</th> 
            <th style="width: 20%;text-align: left;border:1px solid black;">RESTE FACT</th> 
            <th style="width: 30%;text-align: left;border:1px solid black;">RESTE GENERAL</th> 
        </tr> 
</table> 
    
     <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
         <tr >
            <td style="width: 15%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row2['date'])); ?></td>
            <td style="width: 15%;border:1px solid black; text-align: right"><?php echo $row2['heure']; ?></td>
             <td style="width: 20%;border:1px solid black; text-align: right;"><?php echo number_format($row2['mnt_paye_crce_clnt'], 0, ',', ' '); ?></td>
             <td style="width: 20%;border:1px solid black; text-align: right;"><?php echo number_format($row2['mnt_crce'], 0, ',', ' '); ?></td>
             <td style="width: 30%;border:1px solid black; text-align: right;"><?php echo number_format($row3['mnt_reste_global'], 0, ',', ' '); ?></td>
    </tr> 
</table>
    <br/>
    Avance recu pour la somme de (F CFA) :  <strong><?php echo ConvLetter($row2['mnt_paye_crce_clnt'], 0, 'fr') . "<span class='titre-formule'>(" . number_format($row2['mnt_paye_crce_clnt'], 0, ',', ' ') . ")</span>"; ?></strong>
    
    <br/><br/>
    <div><i>Cher Client,</i></div>
    <div><i>Nous avons bien re&ccedil;u votre avance et nous vous remercions. Veuillez prendre note des &eacute;ch&eacute;ances auxquelles elle se rapporte..</i></div>
    

</page>

