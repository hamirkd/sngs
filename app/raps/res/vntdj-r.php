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
       <?php include "includes/header.php";?>
    </page_header>
    <page_footer>
       <?php include "includes/footer.php";?>
    </page_footer>  
    
     <table cellspacing="0" style="margin-top: 20mm;
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
    <div style="font-size: 20px;text-transform: capitalize; 
          font-weight: bold;" align="left">
        <u>Ventes du <em style="color:#777;"><?php echo date("d/m/Y");?></em>  -  Boutique/Magasin : <em style="color:#777;"><?php echo $_SESSION['nomMag'];?></em>
        </u>   
    </div> 
    <br>
    
     
    <?php
    
     $condMag="";
        if($_SESSION['userMag']>0)
            $condMag="AND f.mag_fact=".$_SESSION['userMag'].""; 
        $query = "SELECT a.nom_art,
                         m.nom_mag,
                         v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,
                         COALESCE(c.nom_clt,'Passant') as clt,
                         f.login_caissier_fact,f.code_caissier_fact
                         FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                         INNER JOIN t_magasin m on f.mag_fact=m.id_mag
                         LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                         WHERE DATE(v.date_vnt)='" . date('Y-m-d') . "'
                             $condMag ORDER BY v.id_vnt DESC,a.nom_art ASC";

        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            ?>
     <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
        <tr style="">
              <th style="width: 30%;text-align: left;border:1px solid black;">Designation</th>
            <th style="width: 32%;text-align: left;border:1px solid black;">Client</th>
            <th style="width: 12%;text-align: left;border:1px solid black;">Pu</th>
            <th style="width: 9%;text-align: left;border:1px solid black;">Qte</th>
            <th style="width: 17%;text-align: left;border:1px solid black;">Mnt</th> 
        </tr>
    
    
            <?php 
            while ($row = $r->fetch_assoc()) {
                $total +=$row['qte_vnt']*$row['pu_theo_vnt'];
                ?>
         <tr >
             <td style="width: 30%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
             <td style="width: 32%;text-align: left"><?php echo ucwords(strtolower($row['clt'])); ?></td>
            <td style="width: 12%; text-align: right"><?php echo number_format($row['pu_theo_vnt'],0,',',' '); ?></td>
            <td style="width: 9%;text-align: right"><?php echo $row['qte_vnt']; ?></td>
            <td style="width: 17%; text-align: right;"><?php echo number_format($row['qte_vnt']*$row['pu_theo_vnt'],0,',',' '); ?></td>
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
    <?php 
        } 
        else
        { 
    ?> 
    <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
        <tr>
            <th style="width: 100%; text-align: left;">Aucune vente.. </th> 
        </tr>
    </table>
     <?php 
        }  
    ?> 
   </page> 