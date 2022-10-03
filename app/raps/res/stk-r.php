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
<page  orientation="paysage" format="A4"  backtop="30mm" backbottom="10mm" backleft="10mm" backright="10mm">
    <page_header>
        <?php
        include "includes/header.php";
        ?>
    </page_header>
    <page_footer>
        <?php include "includes/footer.php"; ?>
    </page_footer>  

    <table cellspacing="0" style="margin-top: 5mm;
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                INVENTAIRE DU STOCK
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize;  font-weight: bold;" align="right"> 
        DATE :  <?php   echo date("Y-m-d h:i:s");  ?> 
    </div> 
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Articles en stock
        </u>   
    </div> 
    <br>


    <?php
    $condMag = "";
    $search = $_GET;

    if ($_SESSION['userMag'] != 0)
        $query = "select s.*,a.nom_art,a.ref_art,a.code_art,ca.id_cat,ca.nom_cat,a.seuil_art,m.nom_mag,
p.prix_mini_art,p.prix_gros_art                
from t_stock s
   inner join t_magasin m on s.mag_stk=m.id_mag
  inner join t_article a on s.art_stk=a.id_art
  inner join t_categorie_article ca on a.cat_art=ca.id_cat
  left join (select * from t_prix_article GROUP BY art_prix_art DESC) p on s.art_stk=p.art_prix_art
  WHERE s.mag_stk =" . $_SESSION['userMag'];
    else
        $query = "select s.*,a.nom_art,a.ref_art,a.code_art,ca.id_cat,ca.nom_cat,a.seuil_art,m.nom_mag,
p.prix_mini_art,p.prix_gros_art                
from t_stock s
   inner join t_magasin m on s.mag_stk=m.id_mag
  inner join t_article a on s.art_stk=a.id_art
  inner join t_categorie_article ca on a.cat_art=ca.id_cat
  left join (select * from t_prix_article GROUP BY art_prix_art DESC) p on s.art_stk=p.art_prix_art
  WHERE 1=1";

    if (!empty($search['mg']))
        $query.=" AND s.mag_stk=" . intval($search['mg']);

    if (!empty($search['art']))
        $query.=" AND s.art_stk=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND ca.id_cat=" . intval($search['cat']);

    $query.=" ORDER BY m.nom_mag,ca.nom_cat,a.nom_art ASC";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        $grantTotalmin = 0;
        $grantTotalmax = 0;
        $grantTotalmax = 0;
        $grantTotalQte= 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 12%;border:1px solid black;text-align: left">Magasin</th> 
                <th style="width: 15%;border:1px solid black;text-align: left">Categorie</th>
                <th style="width: 40%;border:1px solid black;text-align: left">Designation</th>
                <th style="width: 16%;border:1px solid black;text-align: left">Reference</th>
                <th style="width: 6%;border:1px solid black;text-align: left">P.Min</th>
                <th style="width: 6%;border:1px solid black;text-align: left">P.Gros</th>
                <th style="width: 5%;border:1px solid black;text-align: left">Qte</th>
            </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=1;
                $grantTotalmin += $row['qte_stk'] * $row['prix_gros_art'];
                $grantTotalmax += $row['qte_stk'] * $row['prix_mini_art'];
                $grantTotalQte += $row['qte_stk'];
                
                ?>
                <tr >
                    <td style="width: 12%;border:1px solid black; text-align: left;"><?php echo $row['nom_mag']; ?></td>
                    <td style="width: 15%;border:1px solid black;text-align: left"><?php echo ucwords(strtolower($row['nom_cat'])); ?></td>
                    <td style="width: 40%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                    <td style="width: 16%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['ref_art'])); ?></td>
                    <td style="width: 6%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['prix_mini_art'])); ?></td>
                    <td style="width: 6%;border:1px solid black; text-align: left"><?php echo ucfirst(strtolower($row['prix_gros_art'])); ?></td>
                    <td style="width: 5%;border:1px solid black;text-align: right"><?php echo $row['qte_stk']; ?></td>
                </tr>
     

                <?php
            }
            ?> 
        </table>

        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 8pt;">
            <tr>
                <th style="width: 60%; text-align: left;"><em>Total : <?php echo $total; ?> article(s)</em></th>
                <th style="width: 40%; text-align: right;padding-right:10px;">QANTITE : <?php echo $grantTotalQte; ?></th>
            </tr>
        </table>
               <br>
       <table cellspacing="0" style="width: 100%; border:  1px double black; background: #E7E7E7; text-align: center; font-size: 20pt;" align="center">
            <tr>
                <th style="width: 40%; text-align: right;background-color:#E7E7E7;">VALEUR STOCK MIN # MAX</th>
                <th style="width: 60%; text-align: right;background-color:#E7E7E7;"><?php echo number_format($grantTotalmin, 0, ',', ' '); ?> FCFA  #  <?php echo number_format($grantTotalmax, 0, ',', ' '); ?> FCFA</th>
            </tr>
        </table>
        <?php
    } else {
        ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 10pt;">
            <tr>
                <th style="width: 100%; text-align: left;">Aucun stock.. </th> 
            </tr>
        </table>
        <?php
    }
    ?> 
</page> 