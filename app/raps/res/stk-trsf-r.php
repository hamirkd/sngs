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
<page backtop="30mm" format="A4" backbottom="10mm" backleft="10mm" backright="10mm" orientation="paysage">
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
    <table cellspacing="0" style="margin-top: 05mm;
           width: 100%; border: solid 1px black; 
            text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                TRANSFERTS DE STOCK
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des transferts de stock
        </u>   
    </div> 
    <br>


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
        $query = "SELECT t.*,u.code_user,m.code_mag as code_mag_src,
                m1.code_mag as code_mag_dst,
                a.nom_art,a.code_art,c.code_cat,c.nom_cat
                FROM t_transfert t
                 INNER JOIN t_magasin m  ON t.mag_src_transf=m.id_mag
                 INNER JOIN t_magasin m1  ON t.mag_dst_transf=m1.id_mag
                 INNER JOIN t_article a ON t.art_transf=a.id_art
                 INNER JOIN t_categorie_article c ON a.cat_art=c.id_cat 
                 INNER JOIN t_user u ON t.user_transf=u.id_user
                 WHERE t.mag_src_transf=" . intval($_SESSION['userMag']);
   

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(t.date_transf)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(t.date_transf) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    if (!empty($search['mgs']))
        $query.=" AND t.mag_src_transf=" . intval($search['mgs']);

    if (!empty($search['mgd']))
        $query.=" AND t.mag_dst_transf=" . intval($search['mgd']);

    if (!empty($search['art']))
        $query.=" AND t.art_transf=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND c.id_cat=" . intval($search['cat']);

    $query .= " Order by m.nom_mag,c.nom_cat,a.nom_art";

 
    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        $totalqte = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                 <th style="width: 15%;border:1px solid black;text-align: left">Mg/Bt dst </th> 
                <th style="width: 25%;border:1px solid black;text-align: left">Categorie</th>
                <th style="width: 42%;border:1px solid black;text-align: left">Designation</th>
                <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
            </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=1;
                $totalqte +=$row['qte_transf'];
                ?>
                <tr >
                    <td style="width: 13%; text-align: left;"><?php echo $row['date_transf']; ?></td>
                      <td style="width: 15%; text-align: left;"><?php echo $row['code_mag_dst']." (".$row['code_user'].")"; ?></td>
                    <td style="width: 25%;text-align: left"><?php echo ucwords(strtolower($row['nom_cat'])); ?></td>
                    <td style="width: 42%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                    <td style="width: 5%;text-align: right"><?php echo $row['qte_transf']; ?></td> 
                </tr>

                <?php
            }
            ?> 
        </table>

        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 8pt;">
            <tr>
                <th style="width: 100%; text-align: left;"><em>Total : <?php echo $total; ?> article(s)</em></th>
            </tr>
        </table>
         <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 52%; text-align: right;">Qte Transferee </th>
                    <th style="width: 48%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                </tr>
            </table>
           
  <?php
        }  
        }elseif ($_SESSION['userMag'] < 1 && !empty($search['mgs']))/**/ {
   
        $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($search['mgs']) . " Limit 1";
        $rmag = $Mysqli->query($querymag);
        $rowmag = $rmag->fetch_assoc();
        ?>
        <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
            <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br>     <br/>
            
        <?php
        $query = "SELECT t.*,u.code_user,m.code_mag as code_mag_src,
                m1.code_mag as code_mag_dst,
                a.nom_art,a.code_art,c.code_cat,c.nom_cat
                FROM t_transfert t
                 INNER JOIN t_magasin m  ON t.mag_src_transf=m.id_mag
                 INNER JOIN t_magasin m1  ON t.mag_dst_transf=m1.id_mag
                 INNER JOIN t_article a ON t.art_transf=a.id_art
                 INNER JOIN t_categorie_article c
                 ON a.cat_art=c.id_cat 
                 INNER JOIN t_user u ON t.user_transf=u.id_user
                 WHERE t.mag_src_transf=" . intval($search['mgs']);
   

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(t.date_transf)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(t.date_transf) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    if (!empty($search['mgs']))
        $query.=" AND t.mag_src_transf=" . intval($search['mgs']);

    if (!empty($search['mgd']))
        $query.=" AND t.mag_dst_transf=" . intval($search['mgd']);

    if (!empty($search['art']))
        $query.=" AND t.art_transf=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND c.id_cat=" . intval($search['cat']);

    $query .= " Order by m.nom_mag,c.nom_cat,a.nom_art";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        $totalqte = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                <th style="width: 15%;border:1px solid black;text-align: left">Mg/Bt dst </th> 
                <th style="width: 25%;border:1px solid black;text-align: left">Categorie</th>
                <th style="width: 42%;border:1px solid black;text-align: left">Designation</th>
                <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
            </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=1;
                $totalqte +=$row['qte_transf'];
                ?>
                <tr >
                    <td style="width: 13%; text-align: left;"><?php echo $row['date_transf']; ?></td>
                     <td style="width: 15%; text-align: left;"><?php echo $row['code_mag_dst']." (".$row['code_user'].")"; ?></td>
                    <td style="width: 25%;text-align: left"><?php echo ucwords(strtolower($row['nom_cat'])); ?></td>
                    <td style="width: 42%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                    <td style="width: 5%;text-align: right"><?php echo $row['qte_transf']; ?></td> 
                </tr>

                <?php
            }
            ?> 
        </table>

        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 8pt;">
            <tr>
                <th style="width: 100%; text-align: left;"><em>Total : <?php echo $total; ?> article(s)</em></th>
            </tr>
        </table>
         <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 52%; text-align: right;">Qte Transferee </th>
                    <th style="width: 48%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                </tr>
            </table>
           
       <?php
        }  
        }else {
        ?> 
        <?php
          $querymag = "SELECT id_mag,nom_mag FROM t_magasin ";
        $rmag = $Mysqli->query($querymag);
        while ($rowmag = $rmag->fetch_assoc()) {
             ?>
           
       
        <?php
        $query = "SELECT t.*,u.code_user,m.code_mag as code_mag_src,
                m1.code_mag as code_mag_dst,
                a.nom_art,a.code_art,c.code_cat,c.nom_cat
                FROM t_transfert t
                 INNER JOIN t_magasin m  ON t.mag_src_transf=m.id_mag
                 INNER JOIN t_magasin m1  ON t.mag_dst_transf=m1.id_mag
                 INNER JOIN t_article a ON t.art_transf=a.id_art
                 INNER JOIN t_categorie_article c
                 ON a.cat_art=c.id_cat 
                 INNER JOIN t_user u ON t.user_transf=u.id_user
                 WHERE t.mag_src_transf=" . intval($rowmag['id_mag']);
   

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(t.date_transf)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(t.date_transf) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    if (!empty($search['mgs']))
        $query.=" AND t.mag_src_transf=" . intval($search['mgs']);

    if (!empty($search['mgd']))
        $query.=" AND t.mag_dst_transf=" . intval($search['mgd']);

    if (!empty($search['art']))
        $query.=" AND t.art_transf=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND c.id_cat=" . intval($search['cat']);

    $query .= " Order by m.nom_mag,c.nom_cat,a.nom_art";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        $totalqte = 0;
        ?>
        <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
            <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br/>
           <br/>
           
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                 <th style="width: 15%;border:1px solid black;text-align: left">Mg/Bt dst </th> 
                <th style="width: 25%;border:1px solid black;text-align: left">Categorie</th>
                <th style="width: 42%;border:1px solid black;text-align: left">Designation</th>
                <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
            </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=1;
                $totalqte +=$row['qte_transf'];
                ?>
                <tr >
                    <td style="width: 13%; text-align: left;"><?php echo $row['date_transf']; ?></td>
                    <td style="width: 15%; text-align: left;"><?php echo $row['code_mag_dst']." (".$row['code_user'].")"; ?></td>
                    <td style="width: 25%;text-align: left"><?php echo ucwords(strtolower($row['nom_cat'])); ?></td>
                    <td style="width: 42%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                    <td style="width: 5%;text-align: right"><?php echo $row['qte_transf']; ?></td> 
                </tr>

                <?php
            }
            ?> 
        </table>

        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 8pt;">
            <tr>
                <th style="width: 100%; text-align: left;"><em>Total : <?php echo $total; ?> article(s)</em></th>
            </tr>
        </table>
         <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 52%; text-align: right;">Qte Transferee </th>
                    <th style="width: 48%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                </tr>
            </table>
           <br/>
           <br/>
           <br/>
           <br/>
        <?php
    }
    }
   }
    ?> 
</page> 