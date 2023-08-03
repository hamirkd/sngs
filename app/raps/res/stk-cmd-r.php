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
    <?php
    $search = $_GET;
    ?>
    <br/>
    <br/>

    <table cellspacing="0" style="margin-top: 05mm;
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                COMMANDES
            </th> 
        </tr>
    </table>
    <br>
    <div style="font-size: 20px;text-transform: capitalize; 
         font-weight: bold;" align="left">
        <u>Liste des articles  commandes
        </u>   
    </div> 
    <br>


    <?php
    $query = "SELECT app.bon_cmd,app.date_cmd,
                a.nom_art,c.nom_cat,f.nom_frns,
                apa.qte_cmd_art,apa.prix_cmd_art,apa.bl_art_recu
                FROM t_commande app
                INNER JOIN t_commande_article apa ON app.id_cmd=apa.cmd_cmd_art
                 INNER JOIN t_article a ON apa.art_cmd_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                INNER JOIN t_fournisseur f ON app.frns_cmd=f.id_frns
                WHERE 1=1";


    if (!empty($search['frns']))
        $query.=" AND f.id_frns=" . intval($search['frns']);

    if (!empty($search['art']))
        $query.=" AND apa.art_cmd_art=" . intval($search['art']);

    if (!empty($search['cat']))
        $query.=" AND id_cat=" . intval($search['cat']);

    if (!empty($search['d']) && empty($search['f']))
        $query.=" AND date(app.date_cmd)='" . isoToMysqldate($search['d']) . "'";

    if (!empty($search['f']))
        $query.=" AND date(app.date_cmd) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

    $query .= " Order by app.date_cmd DESC,apa.bl_art_recu DESC,c.nom_cat,a.nom_art";


    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        $totalqte = 0;
        $totalmont = 0;
        ?>

        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 14pt;">
            <tr style="">
                <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                <th style="width: 20%;border:1px solid black;text-align: left">No Bon </th> 
                <th style="width: 47%;border:1px solid black;text-align: left">Designation</th>
                <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
                <th style="width: 8%;border:1px solid black;text-align: left">Prix</th> 
                <th style="width: 7%;border:1px solid black;text-align: left">Recu ?</th> 
            </tr>


            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=1;
                $totalqte +=$row['qte_cmd_art'];
                $totalmont +=$row['qte_cmd_art'] * $row['prix_cmd_art'];
                ?>
                <tr >
                    <td style="width: 13%; text-align: left;"><?php echo $row['date_cmd']; ?></td>
                    <td style="width: 20%; text-align: left;"><?php echo $row['bon_cmd']; ?></td>
                    <td style="width: 47%; text-align: left"><?php echo ucfirst(strtolower($row['nom_art'])); ?></td>
                    <td style="width: 5%;text-align: right"><?php echo $row['qte_cmd_art']; ?></td> 
                    <td style="width: 8%;text-align: right"><?php echo $row['prix_cmd_art']; ?></td> 
                    <td style="width: 7%;text-align: right"><?php echo ($row['bl_art_recu']==1) ? "Oui":"Non"; ?></td> 
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
                <th style="width: 75%; text-align: right;">Qte Commande </th>
                <th style="width: 5%; text-align: right;"><?php echo number_format($totalqte, 0, ',', ' '); ?> </th>
                <th style="width: 8%; text-align: right;">Valeur </th>
                <th style="width: 12%; text-align: right;"><?php echo number_format($totalmont, 0, ',', ' '); ?> </th>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <br/>
        <?php
    }/* numrows existe */
    ?>
</page> 