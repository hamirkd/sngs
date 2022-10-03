<?php
session_name('SessSngS');
session_start();
?>
<?php
include "includes/const.php";
?>
<?php

function getExtEtatCaisse($mag, $deb, $fin, $Mysqli) {

    $result = array();

    $ccpt = "";
    $crse = "";
    $crc = "";
    $grt = "";
    $crf = "";
    $cdp = "";
    $cvrs = "";
    $prov = "";

    if ($_SESSION['userMag'] > 0) {
        $ccpt = " AND f.mag_fact=" . intval($_SESSION['userMag']);
        $crse = " AND f.mag_fact=" . intval($_SESSION['userMag']);
        $crc = " AND cr.caissier_crce_clnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
        $grt = " AND f.caissier_fact IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
        $crf = " AND df.caissier_dette_frns IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
        $cdp = " AND d.user_dep IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
        $prov = " AND cais.user_cais IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
        $cvrs = " AND vrs.caissier_vrsmnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($_SESSION['userMag']) . ")";
    } else {

        $ccpt = "";
        $crse = "";
        $crc = "";
        $grt = "";
        $crf = "";
        $cdp = "";
        $cvrs = "";
        $prov = "";

        if (!empty($mag)) {
            $ccpt = " AND f.mag_fact=" . intval($mag);
            $crse = " AND f.mag_fact=" . intval($mag);
            $crc = " AND cr.caissier_crce_clnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($mag) . ")";
            $grt = " AND f.caissier_fact IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($mag) . ")";
            $crf = " AND df.caissier_dette_frns IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($mag) . ")";
            $cdp = " AND d.user_dep IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($mag) . ")";
            $prov = " AND cais.user_cais IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($mag) . ")";
            $cvrs = " AND vrs.caissier_vrsmnt IN (SELECT id_user FROM t_user WHERE mag_user=" . intval($mag) . ")";
        }
    }

    if (!empty($deb) && empty($fin))
        $dateq = "='$deb'";

    if (!empty($fin))
        $dateq = " between '$deb' 
                AND '$fin'";

    $query = "SELECT sum(f.crdt_fact) as comptant,sum(f.tva_fact+f.bic_fact) as taxe
                           FROM 
                          t_facture_vente f 
                           WHERE f.bl_fact_crdt=0 AND f.sup_fact=0 $ccpt
                           AND date(f.date_fact) $dateq";


    $r = $Mysqli->query($query);

    if ($r->num_rows > 0) {
        $res = $r->fetch_assoc();
        $result['comptant'] = doubleval($res['comptant']);
    }
    
    $query = "SELECT  sum(v.marge_vnt) as margecpt
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_crdt=0 AND f.sup_fact=0 $ccpt
                           AND date(v.date_vnt) $dateq";


    $r = $Mysqli->query($query);

    if ($r->num_rows > 0) {
        $res = $r->fetch_assoc();
        $result['margecpt'] = doubleval($res['margecpt']);
    }


    $query = "SELECT sum(f.remise_vnt_fact) as remise
                           FROM 
                            t_facture_vente f   
                           WHERE  date(f.date_fact) $dateq $crse AND f.sup_fact=0";

    $r = $Mysqli->query($query);


    if ($r->num_rows > 0) {
        $res = $r->fetch_assoc();
        $result['remise'] = intval($res['remise']);
    }

     $query = "SELECT sum(cr.mnt_paye_crce_clnt) as creance
                           FROM 
                           t_creance_client cr
                           inner join t_facture_vente f ON cr.fact_crce_clnt=f.id_fact
                            WHERE f.bl_fact_grt=0 AND f.bl_fact_crdt=1 AND date(cr.date_crce_clnt) $dateq $crc";
            
             

            $r = $Mysqli->query($query);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['creance'] = intval($res['creance']);
            }
            
            
            
             $query = "SELECT SUM(v.marge_vnt) as margecrdt
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.sup_fact=0 $ccpt
                           AND date(v.date_vnt) $dateq";


            $r = $Mysqli->query($query);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                 $result['margecrdt'] = doubleval($res['margecrdt']);
            }
            
            
            
            $query = "SELECT sum(f.crdt_fact) as grtencaiss
                           FROM t_facture_vente f 
                           WHERE f.bl_fact_grt=1 AND f.bl_encaiss_grt=1 
                           AND f.bl_crdt_regle=1 AND f.sup_fact=0
                           AND date(f.date_encaiss_grt) $dateq $grt";

            $r = $Mysqli->query($query) ;

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                $result['grtencaiss'] = intval($res['grtencaiss']);
            }
            
            $query = "SELECT SUM(v.marge_vnt) as margegrtencaiss
                           FROM 
                           t_vente v
                           INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact 
                           WHERE f.bl_fact_grt=1 AND f.bl_encaiss_grt=1 
                           AND f.bl_crdt_regle=1 AND f.sup_fact=0 $ccpt
                           AND date(f.date_encaiss_grt) $dateq";

            $r = $Mysqli->query($query);

            if ($r->num_rows > 0) {
                $res = $r->fetch_assoc();
                 $result['margegrtencaiss'] = doubleval($res['margegrtencaiss']);
            }


    $query = "SELECT sum(df.mnt_paye_dette_frns) as dette
                           FROM 
                           t_dette_fournisseur df
                            WHERE date(df.date_dette_frns) $dateq $crf";

    $r = $Mysqli->query($query);

    if ($r->num_rows > 0) {
        $res = $r->fetch_assoc();
        $result['dette'] = intval($res['dette']);
    }

    $query = "SELECT IFNULL(sum(d.mnt_dep),0) as depense
                           FROM 
                           t_depense d
                            WHERE date(d.date_dep) $dateq $cdp";


    $r = $Mysqli->query($query);

    if ($r->num_rows > 0) {
        $res = $r->fetch_assoc();
        $result['depense'] = intval($res['depense']);
    }

    $query = "SELECT IFNULL(sum(cais.mnt_cais),0) as provision
                           FROM 
                           t_caisse cais
                            WHERE date(cais.date_cais) $dateq $prov";

    $r = $Mysqli->query($query);

    if ($r->num_rows > 0) {
        $res = $r->fetch_assoc();
        $result['provision'] = intval($res['provision']);
    }



    $query = "SELECT IFNULL(sum(vrs.mnt_vrsmnt),0) as versement
                           FROM 
                           t_versement vrs
                            WHERE date(vrs.date_vrsmnt) $dateq $cvrs";

    $r = $Mysqli->query($query);

    if ($r->num_rows > 0) {
        $res = $r->fetch_assoc();
        $result['versement'] = intval($res['versement']);
    }


    return $result;
}
?>

<style type="text/css">
    table tr td{
        border-bottom:1px solid #000;
        padding:2px;
    }
    table tr th{
        padding:2px;
        border-bottom:1px solid #000;
    }
</style>
<page orientation="paysage" format="A4" backtop="30mm" backbottom="10mm" backleft="20mm" backright="20mm">
    <page_header>
        <?php
        include "includes/header.php";
        ?>
    </page_header>
    <page_footer>
        <?php include "includes/footer.php"; ?>
    </page_footer>  


    <table cellspacing="0" style="margin-top: 20mm;
           width: 100%; border: solid 1px black; 
           text-align: center; 
           font-size: 30pt;">
        <tr>
            <th style="width: 100%; text-align: center;">
                ETATS DE LA CAISSE
            </th> 
        </tr>
    </table>
    <br>


    <?php
    $grandTotal = 0;
    $search = $_GET;
    ?>

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
        $query = "SELECT periode FROM v_etat_periode_vente WHERE 1=1 ";

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND periode='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND periode between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query.=" ORDER BY periode DESC";


        $r = $Mysqli->query($query);
        $entreestotal = 0;
        $sortiestotal = 0;
        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            $entreestotal = 0;
            $sortiestotal = 0;
            ?>
            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=0;
                $grandTotal +=0;

                $response = getExtEtatCaisse($search['mg'], $row['periode'], "", $Mysqli);
                $entrees = $response['comptant'] + $response['grtencaiss'] + $response['creance'] + $response['provision'];
                $sorties = $response['depense'] + $response['dette'] /*+ $response['remise'] */+ $response['versement'];
                $entreestotal +=$entrees;
                $sortiestotal +=$sorties;
                ?>
                <div style="font-size: 16pt;text-transform: capitalize; 
                     font-weight: bold;" align="left">
                    <u><?php echo $row['periode']; ?></u>   
                </div> 
                <br>
                <table style="width:100%" align="center"> 
                    <tr>
                        <th style="width:25%">Ventes au comptant</th>
                        <td style="width:25%"><?php echo $response['comptant']; ?></td>
                        <th style="width:25%">Depenses</th>
                        <td style="width:25%"><?php echo $response['depense']; ?></td>
                    </tr>
                    <?php if($_SESSION['grt']==1){ ?>
                    <tr>
                        <th style="width:25%">Garanties Encaiss</th>
                        <td style="width:25%"><?php echo $response['grtencaiss']; ?></td>
                        <th style="width:25%">Regl fourn..rs</th>
                        <td style="width:25%"><?php echo $response['dette']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">Reglements Clients</th>
                        <td style="width:25%"><?php echo $response['creance']; ?> </td>
                        <th style="width:25%">Remises</th>
                        <td style="width:25%"><?php echo $response['remise']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">Provisions de Caisse</th>
                        <td style="width:25%"><?php echo $response['provision']; ?> </td>
                         <th style="width:25%">Versements</th>
                        <td style="width:25%"><?php echo $response['versement']; ?> </td>
                    </tr>
                    <?php }else { ?>
                    <tr>
                        <th style="width:25%">Reglements Clients</th>
                        <td style="width:25%"><?php echo $response['creance']; ?> </td>
                        <th style="width:25%">Regl fourn..rs</th>
                        <td style="width:25%"><?php echo $response['dette']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">Provisions de Caisse</th>
                        <td style="width:25%"><?php echo $response['provision']; ?> </td>
                        <th style="width:25%">Remises</th>
                        <td style="width:25%"><?php echo $response['remise']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">-</th>
                        <td style="width:25%">-</td>
                         <th style="width:25%">Versements</th>
                        <td style="width:25%"><?php echo $response['versement']; ?> </td>
                    </tr>
                    <?php }?>
                    <tr>
                        <th style="width:25%">&nbsp;</th>
                        <td style="width:25%">&nbsp;</td>
                        <th style="width:25%">&nbsp;</th>
                        <td style="width:25%">&nbsp;</td>
                       
                    </tr>
                    <tr>
                        <th style="width:25%">Total Entrees</th>
                        <td style="width:25%"><?php echo $entrees; ?></td>
                        <th style="width:25%">Total Sorties</th>
                        <td style="width:25%"><?php echo $sorties; ?></td>
                    </tr>

                </table>
                <table style="width:100%" align="center">
                    <tr>
                        <th style="width:100%;background: #E7E7E7; text-align: center; font-size: 10pt;" align="center">Total Caisse : <?php echo $entrees - $sorties; ?> </th>
                    </tr>
                </table>
                <br/>
                <br/>
                <br/>
                <?php
            }
            ?> 

            <?php
        } else {
            ?> 
            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 100%; text-align: left;">Aucune operation </th> 
                </tr>
            </table>
            <?php
        }
        ?> 

        <br/>
        <br/>
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;">
            <tr>
                <th style="width: 15%; text-align: Left;background-color:#E7E7E7;">ENTREES</th>
                <th style="width: 35%; text-align: right;background-color:#bbb;"><?php echo number_format($entreestotal, 0, ',', ' '); ?></th>
                <th style="width: 15%; text-align: right;background-color:#E7E7E7;"> SORTIES</th>
                <th style="width: 35%; text-align: right;background-color:#bbb;"><?php echo number_format($sortiestotal, 0, ',', ' '); ?></th>
            </tr>
        </table>
        <br/>
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;">
            <tr>
                <th style="width: 40%; text-align: Left;background-color:#E7E7E7;">TOTAL CAISSE : </th>
                <th style="width: 60%; text-align: right;background-color:#bbb;"><?php echo number_format($entreestotal - $sortiestotal, 0, ',', ' '); ?> FCFA</th>
            </tr>
        </table>
    <?php } elseif ($_SESSION['userMag'] < 1 && !empty($search['mg'])) {/* fin du premier if */ ?>  

        <?php
        $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($search['mg']) . " Limit 1";
        $rmag = $Mysqli->query($querymag);
        $rowmag = $rmag->fetch_assoc();
        ?>
        <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
            <?php echo $rowmag['nom_mag']; ?>    
        </div> 
        <br>
        <?php
        $query = "SELECT periode FROM v_etat_periode_vente WHERE 1=1 ";

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND periode='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND periode between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query.=" ORDER BY periode DESC";


        $r = $Mysqli->query($query);

        $entreestotal = 0;
        $sortiestotal = 0;
        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            $entreestotal = 0;
            $sortiestotal = 0;
            ?>
            <?php
            while ($row = $r->fetch_assoc()) {
                $total +=0;
                $grandTotal +=0;

                $response = getExtEtatCaisse($search['mg'], $row['periode'], "", $Mysqli);
                $entrees = $response['comptant'] + $response['grtencaiss'] + $response['creance'] + $response['provision'];
                $sorties = $response['depense'] + $response['dette'] /*+ $response['remise'] */+ $response['versement'];
                $entreestotal +=$entrees;
                $sortiestotal +=$sorties;
                ?>
                <div style="font-size: 16pt;text-transform: capitalize; 
                     font-weight: bold;" align="left">
                    <u><?php echo $row['periode']; ?></u>   
                </div> 
                <br>
                <table style="width:100%" align="center"> 
                    <tr>
                        <th style="width:25%">Ventes au comptant</th>
                        <td style="width:25%"><?php echo $response['comptant']; ?>
                            <br/>
                            <br/>
                            MARGE : <?php echo $response['margecpt']; ?>
                        </td>
                        <th style="width:25%">Depenses</th>
                        <td style="width:25%"><?php echo $response['depense']; ?></td>
                    </tr>
                    <tr>
                        <th style="width:25%">Garanties Encaiss</th>
                        <td style="width:25%"><?php echo $response['grtencaiss']; ?>
                            <br/>
                            <br/>
                            MARGE : <?php echo $response['margegrtencaiss']; ?>
                        </td>
                       <th style="width:25%">Regl fourn..rs</th>
                        <td style="width:25%"><?php echo $response['dette']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">Reglements Clients</th>
                        <td style="width:25%"><?php echo $response['creance']; ?> 
                            <br/>
                            <br/>
                            MARGE : <?php echo $response['margecrdt']; ?>
                        </td>
                        <th style="width:25%">Remises</th>
                        <td style="width:25%"><?php echo $response['remise']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">Provisions de Caisse</th>
                        <td style="width:25%"><?php echo $response['provision']; ?> </td>
                         <th style="width:25%">Versements</th>
                        <td style="width:25%"><?php echo $response['versement']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">&nbsp;</th>
                        <td style="width:25%">&nbsp;</td>
                        <th style="width:25%">&nbsp;</th>
                        <td style="width:25%">&nbsp;</td>
                       
                    </tr>
                    <tr>
                        <th style="width:25%">Total Entrees</th>
                        <td style="width:25%"><?php echo $entrees; ?></td>
                        <th style="width:25%">Total Sorties</th>
                        <td style="width:25%"><?php echo $sorties; ?></td>
                    </tr>

                </table>
                <table style="width:100%" align="center">
                    <tr>
                        <th style="width:100%;background: #E7E7E7; text-align: center; font-size: 10pt;" align="center">Total Caisse : <?php echo $entrees - $sorties; ?> </th>
                    </tr>
                </table>
                <br/>
                <br/>
                <br/>
                <?php
            }
            ?> 

            <?php
        } else {
            ?> 
            <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                <tr>
                    <th style="width: 100%; text-align: left;">Aucune operation </th> 
                </tr>
            </table>
            <?php
        }
        ?> 

        <br/>
        <br/>
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;">
            <tr>
                <th style="width: 15%; text-align: Left;background-color:#E7E7E7;">ENTREES</th>
                <th style="width: 35%; text-align: right;background-color:#bbb;"><?php echo number_format($entreestotal, 0, ',', ' '); ?></th>
                <th style="width: 15%; text-align: right;background-color:#E7E7E7;"> SORTIES</th>
                <th style="width: 35%; text-align: right;background-color:#bbb;"><?php echo number_format($sortiestotal, 0, ',', ' '); ?></th>
            </tr>
        </table>
        <br/>
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;">
            <tr>
                <th style="width: 40%; text-align: Left;background-color:#E7E7E7;">TOTAL CAISSE : </th>
                <th style="width: 60%; text-align: right;background-color:#bbb;"><?php echo number_format($entreestotal - $sortiestotal, 0, ',', ' '); ?> FCFA</th>
            </tr>
        </table>

    <?php } else { ?>  

        <?php
        $grandTotalEntrees = 0;
        $grandTotalSorties = 0;
        $querymag = "SELECT id_mag,nom_mag FROM t_magasin ";
        $rmag = $Mysqli->query($querymag);
        while ($rowmag = $rmag->fetch_assoc()) {
            $grandTotal = 0;
            ?>
            <div style="font-size: 20pt;text-transform: capitalize;background: #bbb; font-weight: bold; padding:5pt;" align="left">
                <?php echo $rowmag['nom_mag']; ?>    
            </div> 
            <br>

            <?php
            $query = "SELECT periode FROM v_etat_periode_vente WHERE 1=1 ";

            if (!empty($search['d']) && empty($search['f']))
                $query.=" AND periode='" . isoToMysqldate($search['d']) . "'";

            if (!empty($search['f']))
                $query.=" AND periode between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

            $query.=" ORDER BY periode DESC";


            $r = $Mysqli->query($query);

            $entreestotal = 0;
            $sortiestotal = 0;

            if ($r->num_rows > 0) {
                $result = array();
                $total = 0;
                $entreestotal = 0;
                $sortiestotal = 0;
                ?>
                <?php
                while ($row = $r->fetch_assoc()) {
                    $total +=0;
                    $grandTotal +=0;

                    $response = getExtEtatCaisse($rowmag['id_mag'], $row['periode'], "", $Mysqli);
                    $entrees = $response['comptant'] + $response['grtencaiss'] + $response['creance'] + $response['provision'];
                    $sorties = $response['depense'] + $response['dette'] /*+ $response['remise']*/ + $response['versement'];
                    $entreestotal +=$entrees;
                    $sortiestotal +=$sorties;
                    $grandTotalEntrees +=$entrees;
                    $grandTotalSorties +=$sorties;
                    ?>
                    <div style="font-size: 16pt;text-transform: capitalize; 
                         font-weight: bold;" align="left">
                        <u><?php echo $row['periode']; ?></u>   
                    </div> 
                    <br>
                    <table style="width:100%" align="center"> 
                        <tr>
                        <th style="width:25%">Ventes au comptant</th>
                        <td style="width:25%"><?php echo $response['comptant']; ?>
                            <br/>
                            <br/>
                            MARGE : <?php echo $response['margecpt']; ?>
                        </td>
                        <th style="width:25%">Depenses</th>
                        <td style="width:25%"><?php echo $response['depense']; ?></td>
                    </tr>
                    <tr>
                        <th style="width:25%">Garanties Encaiss</th>
                        <td style="width:25%"><?php echo $response['grtencaiss']; ?>
                            <br/>
                            <br/>
                            MARGE : <?php echo $response['margegrtencaiss']; ?>
                        </td>
                       <th style="width:25%">Regl fourn..rs</th>
                        <td style="width:25%"><?php echo $response['dette']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">Reglements Clients</th>
                        <td style="width:25%"><?php echo $response['creance']; ?> 
                            <br/>
                            <br/>
                            MARGE : <?php echo $response['margecrdt']; ?>
                        </td>
                        <th style="width:25%">Remises</th>
                        <td style="width:25%"><?php echo $response['remise']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">Provisions de Caisse</th>
                        <td style="width:25%"><?php echo $response['provision']; ?> </td>
                         <th style="width:25%">Versements</th>
                        <td style="width:25%"><?php echo $response['versement']; ?> </td>
                    </tr>
                    <tr>
                        <th style="width:25%">&nbsp;</th>
                        <td style="width:25%">&nbsp;</td>
                        <th style="width:25%">&nbsp;</th>
                        <td style="width:25%">&nbsp;</td>
                       
                    </tr>
                        <tr>
                            <th style="width:25%">Total Entrees</th>
                            <td style="width:25%"><?php echo $entrees; ?></td>
                            <th style="width:25%">Total Sorties</th>
                            <td style="width:25%"><?php echo $sorties; ?></td>
                        </tr>

                    </table>
                    <table style="width:100%" align="center">
                        <tr>
                            <th style="width:100%;background: #E7E7E7; text-align: center; font-size: 10pt;" align="center">Total Caisse : <?php echo $entrees - $sorties; ?> </th>
                        </tr>
                    </table>
                    <br/>
                    <br/>
                    <br/>
                    <?php
                }
                ?> 

                <?php
            } else {
                ?> 
                <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 10pt;">
                    <tr>
                        <th style="width: 100%; text-align: left;">Aucune operation </th> 
                    </tr>
                </table>
                <?php
            }
            ?> 

            <br/>
            <br/>
            <table align="right" cellspacing="0" style="width: 50%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;" >
                <tr>
                    <th style="width: 35%;border:none;border-bottom:1px solid black; text-align: Left;background-color:#E7E7E7;">ENTREES</th>
                    <th style="width: 65%;border:none;border-bottom:1px solid black; text-align: right;background-color:#bbb;"><?php echo number_format($entreestotal, 0, ',', ' '); ?></th>
                </tr>

            </table>
            <br/>
            <br/>
            <table align="right" cellspacing="0" style="width: 50%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;" >
                <tr>
                    <th style="width: 35%;border:none;border-bottom:1px solid black; text-align: left;background-color:#E7E7E7;"> SORTIES</th>
                    <th style="width: 65%;border:none;border-bottom:1px solid black; text-align: right;background-color:#bbb;"><?php echo number_format($sortiestotal, 0, ',', ' '); ?></th>
                </tr>
            </table>
            <br/>
            <br/>
            <table align="right" cellspacing="0" style="width: 50%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;">
                <tr>
                    <th style="width: 40%; text-align: Left;background-color:#E7E7E7;">CAISSE : </th>
                    <th style="width: 60%; text-align: right;background-color:#bbb;"><?php echo number_format($entreestotal - $sortiestotal, 0, ',', ' '); ?> FCFA</th>
                </tr>
            </table>
            <br/>
            <br/>
            <br/>
            <br/>
            <?php
        }/* fin du while */
        ?>

        <br/>
        <br/>
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;">
            <tr>
                <th style="width: 15%; text-align: Left;background-color:#E7E7E7;">ENTREES</th>
                <th style="width: 35%; text-align: right;background-color:#bbb;"><?php echo number_format($grandTotalEntrees, 0, ',', ' '); ?></th>
                <th style="width: 15%; text-align: right;background-color:#E7E7E7;"> SORTIES</th>
                <th style="width: 35%; text-align: right;background-color:#bbb;"><?php echo number_format($grandTotalSorties, 0, ',', ' '); ?></th>
            </tr>
        </table>
        <br/>
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 20pt;">
            <tr>
                <th style="width: 40%; text-align: Left;background-color:#E7E7E7;">TOTAL GENERAL : </th>
                <th style="width: 60%; text-align: right;background-color:#bbb;"><?php echo number_format($grandTotalEntrees - $grandTotalSorties, 0, ',', ' '); ?> FCFA</th>
            </tr>
        </table>
        <?php
    }/* fin du else */
    ?>
</page> 