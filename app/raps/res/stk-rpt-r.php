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
                ETAT RUPTURE DE  STOCK
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
        <u>Liste des articles en rupture de  stock
        </u>   
    </div> 
    <br>


    <?php
    $condMag = "";
    $search = $_GET; 

     if($_SESSION['userMag']!=0)
        $query = "SELECT * FROM v_etat_alerte WHERE mag_stk=".$_SESSION['userMag'];
        else
        $query = "SELECT * FROM v_etat_alerte WHERE 1=1 ";
        
        if (!empty($search['mg']))
            $query.=" AND mag_stk=" . intval($search['mg']);

        if (!empty($search['art']))
            $query.=" AND art_stk=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']); 
    
    $query.=" ORDER BY nom_mag,nom_cat,nom_art ASC"; 
 
     
    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 17%;border:1px solid black;text-align: left">Magasin</th> 
                <th style="width: 15%;border:1px solid black;text-align: left">Categorie</th>
                <th style="width: 40%;border:1px solid black;text-align: left">Designation</th>
                <th style="width: 16%;border:1px solid black;text-align: left">Reference</th>
                <th style="width: 6%;border:1px solid black;text-align: left">Qte stk</th>
                <th style="width: 6%;border:1px solid black;text-align: left">Seuil</th>
            </tr>


    <?php
    while ($row = $r->fetch_assoc()) {
        $total +=1
        ?>
                <tr >
                    <td style="width: 17%; text-align: left;"><?php echo $row['nom_mag']; ?></td>
                    <td style="width: 15%;text-align: left"><?php echo ucwords(strtolower($row['nom_cat'])); ?></td>
                    <td style="width: 40%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                    <td style="width: 16%; text-align: left"><?php echo ucfirst(strtolower($row['ref_art'])); ?></td>
                    <td style="width: 6%;text-align: right"><?php echo $row['qte_stk']; ?></td>
                    <td style="width: 6%; text-align: right"><?php echo $row['seuil_art']; ?></td>
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
    <?php
} else {
    ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 10pt;">
            <tr>
                <th style="width: 100%; text-align: left;">Aucune rupture de stock d'articles.. </th> 
            </tr>
        </table>
    <?php
}
?> 
</page> 