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
        border:1px solid #000;
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
                RAPPORT GENERAL DES GARANTIES
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 14px;text-transform: capitalize; 
         font-weight: bold;" align="right">
        Ouagadougou, le <?php echo date("d/m/Y"); ?>

    </div> 
     
<br/>
 
<table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
        <tr style="">
              <th style="width: 60%;text-align: left;border:1px solid black;">Designation</th>
            <th style="width: 9%;text-align: left;border:1px solid black;">Qte</th>
             <th style="width: 12%;text-align: left;border:1px solid black;">Pu</th>
            <th style="width: 19%;text-align: left;border:1px solid black;">Mnt Estim</th> 
        </tr>
        
        <?php
    
         $query2 = "SELECT a.id_art,a.nom_art,
                          sum(v.qte_vnt) as qte_vnt,v.pu_theo_vnt
                         FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                         WHERE f.sup_fact=0 AND f.bl_fact_crdt=1 AND f.bl_encaiss_grt=0 AND f.bl_fact_grt=1
                        GROUP BY a.id_art ORDER BY a.nom_art ASC";

        $r2 = $Mysqli->query($query2) or die($this->mysqli->error . __LINE__);
 
         $total = 0;
            ?>
    
    
            <?php 
            while ($row = $r2->fetch_assoc()) {
                $total +=$row['qte_vnt']*$row['pu_theo_vnt'];
                ?>
         <tr >
             <td style="width: 60%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
            <td style="width: 9%;text-align: right"><?php echo $row['qte_vnt']; ?></td>
             <td style="width: 12%; text-align: right"><?php echo number_format($row['pu_theo_vnt'],0,',',' '); ?></td>
            <td style="width: 19%; text-align: right;"><?php echo number_format($row['qte_vnt']*$row['pu_theo_vnt'],0,',',' '); ?></td>
        </tr>
    
    <?php 
        }  
    ?> 
  </table> 
 <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
        <tr>
            <th style="width: 40%; text-align: right;">Valeur Total (Estim) : </th>
            <th style="width: 60%; text-align: right;"><?php echo number_format($total, 0, ',', ' '); ?> FCFA</th>
        </tr>
    </table> 
</page>

