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
           vertical-align: middle;
    }
    table tr td{
        padding:1.5mm;
    }
   

    table tr td{
        font-size:14px; 
        color : #000;
    }
</style>
<page backtop="30mm" backbottom="10mm" backleft="10mm" backright="10mm" backcolor="#fff" style="font: arial;">
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

    $query = "SELECT s.id_sort,s.bon_sort,s.date_sort,
        ms.code_mag,ms.nom_mag,code_user_sort    FROM 
                           t_sortie s
                            INNER JOIN t_magasin ms ON s.mag_sort_src=ms.id_mag 
                           WHERE s.id_sort=$fact limit 1";


    $r = $Mysqli->query($query);
    $result = $r->fetch_assoc();
    $emt = $result['code_user_sort'];
    $num = $result['bon_sort'];
    $magsc = $result['code_mag'];
    $magsn = $result['nom_mag'];
    $dates = $result['date_sort'];


    $query = "SELECT 
        md.code_mag,md.nom_mag  FROM 
                           t_sortie s
                            INNER JOIN t_magasin md ON s.mag_sort_dst=md.id_mag 
                           WHERE s.id_sort=$fact  limit 1";


    $r = $Mysqli->query($query);
    $result = $r->fetch_assoc();
    $magdc = $result['code_mag'];
    $magdn = $result['nom_mag'];
    ?>
    <?php
    //include "includes/header-fact-tva.php";
    ?>
    <table style="width:100%;" border="0" align="center">
        <tr>
            <td style="width: 100%">
                <table style="width:100%;border-collapse: collapse;" align='center' border="0">
                    <tr>
                        <td colspan="3" 
                            style="background-color:#ccc;text-align:center;
                            border-left:1px solid black;
                            border-right:1px solid black;
                            border-top:1px solid black;"><u><h3>BON DE SORTIE (BS)</h3></u> </td> 
        </tr>
        <tr>
            <td colspan="3" style="border-left:1px solid black;
                border-right:1px solid black;">&nbsp;</td> 
        </tr>
        <tr>
            <td style="width:30%;border-left:1px solid black;  "><strong>Num&eacute;ro :  <?php echo $num; ?></strong></td>
            <td style="width:35%;"><strong>Emetteur : </strong>  <?php echo $emt; ?></td>
            <td style="width:35%; border-right:1px solid black; "><strong>Demandeur : </strong>  <?php echo $magdn; ?></td>
        </tr>

        <tr>
            <td style="width:30%;border-left:1px solid black;"><strong>Date : </strong> <?php echo $dates; ?></td>
            <td style="width:35%; "><strong>Mag/Btq : </strong> <?php echo $magsn; ?></td>
            <td style="width:35% ;border-left:1px solid black;
                border-right:1px solid black;  "><strong> &nbsp;</strong></td>
        </tr>
        <tr>
            <td style="width:30%;border-left:1px solid black;
                border-bottom:1px solid black; ">&nbsp;</td>
            <td style="width:35%; border-bottom:1px solid black;">&nbsp;</td>
            <td style="width:35% ;border-left:1px solid black;
                border-right:1px solid black;
                border-bottom:1px solid black; "><strong> &nbsp;</strong></td>
        </tr>

    </table> 
</td>
</tr>
</table> 
    
  
   <table style="width:100%;border-collapse: collapse;" align='center' border="0">
        <tr>
            <td style="width:5%;border:1px solid black;"><strong>N&deg;</strong></td>
            <td style="width:15%;border:1px solid black;"><strong>Code Art</strong></td>
            <td style="width:72%;border:1px solid black;"><strong>Designation</strong></td>
            <td style="width:8%;border:1px solid black;"><strong>Qte</strong></td>
        </tr>

        <?php
        $query = "SELECT a.code_art,a.nom_art,
                         sa.qte_sort_art
                          FROM t_sortie s
                         INNER JOIN t_sortie_article sa ON s.id_sort=sa.sort_sort_art
                         INNER JOIN t_article a ON sa.art_sort_art=a.id_art
                           WHERE s.id_sort=$fact ORDER BY a.nom_art ASC";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $total = 0;
        ?>
        <?php
        while ($row = $r->fetch_assoc()) {
            $total +=1;
            ?>
            <tr>
                <td style="width:5%;border:1px solid black;"><?php echo $total; ?> </td>
                <td style="width:15%;border:1px solid black;"><?php echo $row['code_art']; ?> </td>
                <td style="width:72%;border:1px solid black;"><?php echo strtoupper($row['nom_art']); ?></td>
                <td style="width:8%;border:1px solid black;"><?php echo $row['qte_sort_art']; ?></td>
            </tr>
            <?php
        }
        ?>   

    </table>
    <br/>  
     
    <table style="width:100%;" align='center' border="0">
         <tr>
            <td style="width:50%;text-align:center;"><strong>Visa du Magasin/Boutique</strong> </td>
            <td style="width:50%;text-align:center;"><strong>Visa du Demandeur</strong> </td>
        </tr>  
    </table>
</page> 
