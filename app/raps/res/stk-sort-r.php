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
<page backtop="10mm" backbottom="10mm" backleft="20mm" backright="20mm" orientation="paysage">
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

    <table cellspacing="0" style="margin-top: 20mm;
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                SORTIES DE STOCK
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des articles  Sorties
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
        <br>

        <?php
        $query = "SELECT sort.bon_sort,sort.date_sort,
                a.nom_art,m.nom_mag,m.code_mag,c.nom_cat,
                apa.qte_sort_art
                FROM t_sortie sort
                INNER JOIN t_sortie_article apa ON sort.id_sort=apa.sort_sort_art
                INNER JOIN t_magasin m on m.id_mag=sort.mag_sort_dst
                INNER JOIN t_article a ON apa.art_sort_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                WHERE sort.mag_sort_src=" . intval($_SESSION['userMag']);
        

        if (!empty($search['mg']))
            $query.=" AND sort.mag_sort_dst=" . intval($search['mg']);

        if (!empty($search['art']))
            $query.=" AND apa.art_sort_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(sort.date_sort)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(sort.date_sort) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query .= " Order by sort.date_sort DESC,m.code_mag,c.nom_cat,a.nom_art";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                    <th style="width: 15%;border:1px solid black;text-align: left">Bon </th> 
                    <th style="width: 10%;border:1px solid black;text-align: left">Dest</th> 
                    <th style="width: 20%;border:1px solid black;text-align: left">Categorie</th>
                    <th style="width: 37%;border:1px solid black;text-align: left">Designation</th>
                    <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=1;
                    $totalqte +=$row['qte_sort_art'];
                    ?>
                    <tr >
                        <td style="width: 13%;border:1px solid black; text-align: left;"><?php echo $row['date_sort']; ?></td>
                        <td style="width: 15%;border:1px solid black; text-align: left;"><?php echo $row['bon_sort']; ?></td>
                        <td style="width: 10%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['code_mag'])); ?></td>
                        <td style="width: 20%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['nom_cat'])); ?></td>
                        <td style="width: 37%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 5%;border:1px solid black;text-align: right"><?php echo $row['qte_sort_art']; ?></td> 
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
                    <th style="width: 52%; text-align: right;">Qte Sortie </th>
                    <th style="width: 48%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                </tr>
            </table>
           

 <?php
        }  
        }elseif ($_SESSION['userMag'] < 1 && !empty($search['mg']))/**/ {
        $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($search['mg']) . " Limit 1";
        $rmag = $Mysqli->query($querymag);
        $rowmag = $rmag->fetch_assoc();
        ?>
        <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
            <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br>

        <?php
        $query = "SELECT sort.bon_sort,sort.date_sort,
                a.nom_art,m.nom_mag,m.code_mag,c.nom_cat,
                apa.qte_sort_art
                FROM t_sortie sort
                INNER JOIN t_sortie_article apa ON sort.id_sort=apa.sort_sort_art
                INNER JOIN t_magasin m on m.id_mag=sort.mag_sort_dst
                INNER JOIN t_article a ON apa.art_sort_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                WHERE sort.mag_sort_src=" . intval($search['mg']);
        

        if (!empty($search['mgb']))
            $query.=" AND sort.mag_sort_dst=" . intval($search['mgb']);

        if (!empty($search['art']))
            $query.=" AND apa.art_sort_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(sort.date_sort)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(sort.date_sort) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query .= " Order by sort.date_sort DESC,m.code_mag,c.nom_cat,a.nom_art";

 
        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            $totalqte = 0;
            ?>
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                    <th style="width: 15%;border:1px solid black;text-align: left">Bon </th> 
                    <th style="width: 10%;border:1px solid black;text-align: left">Dest </th> 
                    <th style="width: 20%;border:1px solid black;text-align: left">Categorie</th>
                    <th style="width: 37%;border:1px solid black;text-align: left">Designation</th>
                    <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=1;
                    $totalqte +=$row['qte_sort_art'];
                    ?>
                    <tr >
                        <td style="width: 13%;border:1px solid black; text-align: left;"><?php echo $row['date_sort']; ?></td>
                        <td style="width: 15%;border:1px solid black; text-align: left;"><?php echo $row['bon_sort']; ?></td>
                        <td style="width: 10%;border:1px solid black; text-align: left;"><?php echo $row['code_mag']; ?></td>
                        <td style="width: 20%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['nom_cat'])); ?></td>
                        <td style="width: 37%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 5%;border:1px solid black;text-align: right"><?php echo $row['qte_sort_art']; ?></td> 
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
                    <th style="width: 52%; text-align: right;">Qte Sortie </th>
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
        $query = "SELECT sort.bon_sort,sort.date_sort,
                a.nom_art,m.nom_mag,m.code_mag,c.nom_cat,
                apa.qte_sort_art
                FROM t_sortie sort
                INNER JOIN t_sortie_article apa ON sort.id_sort=apa.sort_sort_art
                INNER JOIN t_magasin m on m.id_mag=sort.mag_sort_dst
                INNER JOIN t_article a ON apa.art_sort_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                WHERE sort.mag_sort_src=".$rowmag['id_mag'];
        

        if (!empty($search['mg']))
            $query.=" AND sort.mag_sort_dst=" . intval($search['mgb']);

        if (!empty($search['art']))
            $query.=" AND apa.art_sort_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(sort.date_sort)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(sort.date_sort) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query .= " Order by sort.date_sort DESC,m.code_mag,c.nom_cat,a.nom_art";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            $totalqte = 0;
            ?>
         <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
            <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br>
 
            <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
                <tr style="">
                    <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                    <th style="width: 15%;border:1px solid black;text-align: left">Bon </th> 
                    <th style="width: 10%;border:1px solid black;text-align: left">Dest </th> 
                    <th style="width: 20%;border:1px solid black;text-align: left">Categorie</th>
                    <th style="width: 37%;border:1px solid black;text-align: left">Designation</th>
                    <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
                </tr>


                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=1;
                    $totalqte +=$row['qte_sort_art'];
                    ?>
                    <tr >
                        <td style="width: 13%;border:1px solid black; text-align: left;"><?php echo $row['date_sort']; ?></td>
                        <td style="width: 15%;border:1px solid black; text-align: left;"><?php echo $row['bon_sort']; ?></td>
                        <td style="width: 10%;border:1px solid black; text-align: left;"><?php echo $row['code_mag']; ?></td>
                        <td style="width: 20%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['nom_cat'])); ?></td>
                        <td style="width: 37%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                        <td style="width: 5%;border:1px solid black;text-align: right"><?php echo $row['qte_sort_art']; ?></td> 
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
                    <th style="width: 52%; text-align: right;">Quantite Sortie </th>
                    <th style="width: 48%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                </tr>
            </table>
      <br/>
            <br/>
            <br/>
            <br/>
        <?php
        }/*numrows existe*/
    } /*fi du while*/
} /* fin du else*/
?>
</page> 