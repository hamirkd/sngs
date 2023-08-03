<?php
session_name('SessSngS');
session_start();
include ("includes/db.php");
?>
<?php
include "includes/const.php";
?>
<style>

    /*table tr td{
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
    }*/

    table tr th,td{ 
        font-size:13px;
        font-family: arial;
        font-style:italic;
        text-align:center;
        /* padding:2px;*/

    }

    table.line-montant tr th,table.line-montant tr td{  
        font-weight:bold;   
    }
    .bold{
        font-weight: bold;
    }
    .items{
        border:0px;
        border-left:initial; 
        border-right:initial; 
    }



    /*table tr th.chiffre{
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
    }*/
</style>

<page backtop="45mm" backbottom="10mm" backleft="10mm" backright="10mm" backcolor="#fff" style="font: arial;">
    <page_footer>
        <?php //include "includes/footer.php"; ?>
    </page_footer>  
    <?php
    $condMag = "";
    $fact = intval($_GET['f']);

    $query = "SELECT f.id_fact,f.bl_fact_crdt,f.bl_fact_grt,f.bl_tva,
        f.bl_bic,f.remise_vnt_fact,f.code_fact,
        COALESCE(c.nom_clt,'-') as nom_clt,
        COALESCE(c.bp_clt,'-') as bp_clt,
        COALESCE(c.adr_clt,'-') as adr_clt,
        COALESCE(c.tel_clt,'-') as tel_clt,
        COALESCE(c.forme_juri_clt,'-') as forme_juri_clt,
        COALESCE(c.regime_clt,'-') as regime_clt,
        COALESCE(c.division_clt,'-') as division_clt,
        COALESCE(c.situation_clt,'-') as situation_clt,
        COALESCE(c.ifu_clt,'-') as ifu_clt,
        COALESCE(c.rccm_clt,'-') as rccm_clt,
          m.nom_mag,f.code_caissier_fact,DATE(f.date_fact) as date_fact,time(f.date_fact) as heure_fact,
        m.nom_mag
                           FROM 
                           t_facture_vente f
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.id_fact=$fact  limit 1";


    $r = $Mysqli->query($query);
    $result = $r->fetch_assoc();
    $nom = $result['nom_clt'];
    $bp = $result['bp_clt'];
    $adr = $result['adr_clt'];
    $tel = $result['tel_clt'];
    $forme = $result['forme_juri_clt'];
    $regime = $result['regime_clt'];
    $division = $result['division_clt'];
    $situation = $result['situation_clt'];
    $ifu = $result['ifu_clt'];
    $rccm = $result['rccm_clt'];

    $num = $result['code_fact'];
    $nc_array = explode("-", $num);
    $num_court = str_pad($nc_array[1], 4, "0", STR_PAD_LEFT);
    $date = $result['date_fact'];
    $heure = $result['heure_fact'];
    $magasin = $result['nom_mag'];
    $login = $result['code_caissier_fact'];
    $remise = intval($result['remise_vnt_fact']);
    $bltva = $result['bl_tva'];
    $blbic = $result['bl_bic'];
    $blcrdt = $result['bl_fact_crdt'];
    $blgrt = $result['bl_fact_grt'];

    $query2 = "SELECT m.resp_mag,m.titre_resp_mag FROM  t_magasin m 
        inner join t_facture_vente f ON f.mag_fact=m.id_mag 
                           WHERE f.id_fact=$fact  limit 1";


    $r2 = $Mysqli->query($query2);
    $result2 = $r2->fetch_assoc();
    $resp_mag = $result2['resp_mag'];
    $titre_resp_mag = $result2['titre_resp_mag'];
    ?>
    <?php
    //include "includes/header-fact-tva.php";
    ?>

    <table style="width: 100%;" cellspacing="4mm" cellpadding="0">
        <tr> 
            <td style="width: 40%;vertical-align: top;text-align: left;font-size:20px;">
                <strong>FACTURE</strong>
            </td>
            <td style="width: 20%"> 
            </td>
            <td style="width: 40%;text-align: right;">

            </td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td style="width: 100%">

                <?php
                $query = "SELECT a.code_art,a.nom_art,
                         v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt 
                          FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE f.id_fact=$fact ORDER BY a.nom_art ASC";


                $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $result = array();
                $total = 0;
                $som = 0;
                ?>

                <table style="width:98%;border-collapse: collapse;" border="0" align='right'>
                    <tr>
                        <td style="width: 45%;text-align: center;padding:5px;vertical-align: middle;">
                            <table border="01" align="center" style="vertical-align: middle;">
                                <tr>
                                    <th>Num&eacute;ro</th>
                                    <th>Date</th>
                                </tr>
                                <tr>
                                    <td>
                                        <strong> <?php
                                             if ($bltva == 0) { 
                                           /* if ($blcrdt == 1 && $blgrt == 0)
                                                echo "C-";
                                            elseif ($blcrdt == 1 && $blgrt == 1)
                                                echo "G-";
                                            else
                                                echo "T-";*/
                                            echo $num_court;
                                             }
                                              else
                                              echo $num_court; 
                                            ?>/<?php
                                            $dt = explode('-', $date);
                                            echo $dt[0];
                                            ?>/<?php echo NOM_STRUCT_ABBR; ?>
                                        <?php if ($bltva == 0)  echo "/HT";?> 
                                        </strong>
                                    </td>
                                    <td>
                                        <strong><?php
                                            $dt = explode('-', $date);
                                            echo $dt[2] . "/" . $dt[1] . "/" . $dt[0];
                                            echo " " . $heure;
                                            ?> </strong>&nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td  style="width: 55%;text-align: left;padding:5px;vertical-align: top;">
                            <div style="text-align: center;border:4px double #111;padding:5px;font-weight: bold;"> <u>DOIT (ADRESSE DE FACTURATION) :</u><br/>
                                <br><?php echo strtoupper($nom); ?><br>  
                                <?php if ($situation != "" && $situation != "-") echo strtoupper($situation) . "<br>"; ?> 
                                <?php if ($bp != "" && $bp != "-") echo strtoupper($bp) . "<br>"; ?> 
                                <?php if ($tel != "" && $tel != "-") echo "TEL :  " . strtoupper($tel) . "<br>"; ?> 
                                <?php if ($forme != "" && $forme != "-") echo "FORME JURIDIQUE :  " . strtoupper($forme) . "<br>"; ?> 
                                <?php if ($ifu != "" && $ifu != "-") echo "No IFU :   " . strtoupper($ifu) . "<br>"; ?>  
                                <?php if ($regime != "" && $regime != "-") echo "REGIME IMPOSITION : " . strtoupper($regime) . "<br>"; ?> 
                                <?php if ($division != "" && $division != "-") echo "DOMICILIATION FISCALE :   " . strtoupper($division) . "<br>"; ?>   
                                <?php if ($rccm != "" && $rccm != "-") echo "No RCCM :   " . strtoupper($rccm) . "<br>"; ?>   
                            </div>  
                        </td> 
                    </tr> 
                </table>

                <br/>

                <table style="width:98%;border-collapse: initial;" align="center" border="01">
                    <tr>
                        <th style="width: 53%;">DESIGNATION</th>
                        <th style="width: 13%;text-align: center;">QUANTITE</th>
                        <th style="width: 17%;">PRIX UNITAIRE</th>
                        <th style="width: 17%;">PRIX TOTAL</th>
                    </tr> 

                    <?php
                    while ($row = $r->fetch_assoc()) {
                        $total +=1;
                        $som +=$row['qte_vnt'] * $row['pu_theo_vnt'];
                        ?>
                        <tr >
                            <td class="items" style="border:0px;width: 53%; text-align: left;font-weight:normal;"><?php echo strtoupper($row['nom_art']); ?></td>
                            <td class="chiffre items" style="width: 13%;"><?php echo $row['qte_vnt']; ?></td>
                            <td class="chiffre items" style="width: 17%;"><?php echo number_format($row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                            <td class="chiffre items" style="width: 17%;"><?php echo number_format($row['qte_vnt'] * $row['pu_theo_vnt'], 0, ',', ' '); ?></td>
                        </tr>

                        <?php
                    }
                    ?> 
                    <tr >
                        <td style="width: 53%; text-align: left;border:1px solid black;border-top:0 transparent;">
                        </td>
                        <td style="width: 13%;text-align: right;border:1px solid black;border-top:0 transparent;"></td>
                        <td style="width: 17%; text-align: right;border:1px solid black;border-top:0 transparent;"></td>
                        <td style="width: 17%; text-align: right;border:1px solid black;border-top:0 transparent;"></td>
                    </tr>
                </table>
                <table cellspacing="0" style="width: 98%;background: #fff; text-align: center; font-size: 8pt;" align="center">
                    <tr>
                        <td class="" style="width: 100%; text-align: left;"><em>Total : <?php echo $total; ?> article(s)</em></td>
                    </tr>
                </table> 


                <!-- 
                     ==========================================
                     ======FACTURE : | SIMPLE - NI TVA - NI BIC  ===============
                    ===========================================
                -->
                <?php if ($bltva == 0 && $blbic == 0): ?> 
                    <table style="width:98%;border-collapse: initial;" align="center" border="01" class="line-montant" cellpadding="01"> 
                        <tr>
                            <th style="width: 25%; text-align: center;">TOTAL HT</th>
                            <th style="width: 35%; text-align: center;">TOTAL TTC</th>
                            <th style="width: 40%; text-align: center;">NET &Agrave; PAYER</th>
                        </tr>
                        <tr>
                            <td style="width: 25%; text-align: center;">
                                <?php
                                $som = $som - $remise;
                                echo number_format($som, 0, ',', ' ');
                                ?>
                            </td>
                            <td style="width: 35%; text-align: center;">
                                <?php echo number_format($som, 2, ',', ' '); ?>
                            </td>
                            <td style="width: 40%; text-align: center;">
                                <?php echo number_format($som, 2, ',', ' '); ?>
                            </td>
                        </tr>
                    </table> 
                    <?php
                    $som = $som;

                endif;
                ?> 


                <!-- 
                      ==========================================
                      ======FACTURE : TVA ===============
                     ===========================================
                -->
                <?php if ($bltva == 1 && $blbic == 0): ?> 
                    <table style="width:98%;border-collapse: initial;" align="center" border="01" class="line-montant" cellpadding="01"> 
                        <tr>
                            <th style="width: 25%; text-align: center;">TOTAL HT</th>
                            <th style="width: 16%;text-align: center;">TVA (<?php echo $_SESSION['tva']*100; ?>%)</th>
                            <th style="width: 25%; text-align: center;">TOTAL TTC</th>
                            <th style="width: 9%; text-align: center;">BIC (<?php echo $_SESSION['bic']*100; ?>%)</th>
                            <th style="width: 25%; text-align: center;">NET &Agrave; PAYER</th>
                        </tr>
                        <tr>
                            <td style="width: 25%; text-align: center;">
                                <?php
                                $som = $som - $remise;
                                echo number_format($som, 0, ',', ' ');
                                ?>
                            </td>
                            <td style="width: 16%;text-align: center;">
                                <?php echo number_format($som * 0.18, 2, ',', ' '); ?>
                            </td>
                            <td style="width: 25%; text-align: center;">
                                <?php echo number_format($som+$som * $_SESSION['tva'], 2, ',', ' '); ?>
                            </td>
                            <td style="width: 9%; text-align: center;">
                                <?php echo number_format(0, 2, ',', ' '); ?>
                            </td>
                            <td style="width: 25%; text-align: center;">
                                <?php echo number_format($som+$som * $_SESSION['tva'], 2, ',', ' '); ?>
                            </td>
                        </tr>
                    </table> 
                    <?php
                    $som = $som+$som * $_SESSION['tva'];

                endif;
                ?> 

                <!-- 
                     ==========================================
                     ======FACTURE : BIC && TVA ===============
                    ===========================================
                -->
                <?php if ($blbic == 1): ?> 
                    <table style="width:98%;border-collapse: initial;" align="center" border="01" class="line-montant" cellpadding="01"> 
                        <tr>
                            <th style="width: 19%; text-align: center;">TOTAL HT</th>
                            <th style="width: 16%;text-align: center;">TVA (<?php echo $_SESSION['tva']*100; ?>%)</th>
                            <th style="width: 22%; text-align: center;">TOTAL TTC</th>
                            <th style="width: 18%;text-align: center;">BIC (<?php echo $_SESSION['bic']*100; ?>%)</th>
                            <th style="width: 25%; text-align: center;">NET &Agrave; PAYER</th>
                        </tr>
                        <tr>
                            <td style="width: 19%; text-align: center;">
                                <?php
                                $som = $som - $remise;
                                echo number_format($som, 0, ',', ' ');
                                ?>
                            </td>
                            <td style="width: 16%;text-align: center;">
                                <?php echo number_format($som * 0.18, 2, ',', ' '); ?>
                            </td>
                            <td style="width: 22%;text-align: center;">
                                <?php echo number_format($som+$som * $_SESSION['tva'], 2, ',', ' '); ?>
                            </td>
                            <td style="width: 18%; text-align: center;">
                                <?php echo number_format(($som+$som * $_SESSION['tva'])*$_SESSION['bic'], 2, ',', ' '); ?>
                            </td>
                            <td style="width: 25%; text-align: center;">
                                <?php echo number_format(($som+$som * $_SESSION['tva'])+(($som+$som * $_SESSION['tva'])*$_SESSION['bic']), 2, ',', ' '); ?>
                            </td>
                        </tr>
                    </table>
                    <?php
                    $som = ($som+$som * $_SESSION['tva'])+(($som+$som * $_SESSION['tva'])*$_SESSION['bic']);

                endif;
                ?> 

                <?php $mntt = $som; ?>
                <table cellspacing="0" style="width: 98%;background: #fff; text-align: center; font-size: 8pt;" align="center">
                    <tr>
                        <td style="width: 100%; text-align: left;">Arr&ecirc;t&eacute;e la pr&eacute;sente <span class="bold" >FACTURE</span> &Agrave; la somme de :<br/> <span class="bold"><?php echo strtoupper(ConvLetter($mntt, 0, 'fr')) . " (" . number_format($mntt, 2, ',', ' ') . ")"; ?>  FRANCS/CFA TTC</span></td>
                    </tr>
                </table>
                <br/>
                <br/> 
                <table cellspacing="0" style="width: 100%;background: #fff; text-align: center; font-size: 8pt;" align="center">
                    <tr>
                        <td class="signataire" style="width: 50%; text-align: left;">

                        </td>
                        <td class="signataire" style="width: 50%; text-align: center;">

                            <strong>
                                <?php
                                if ($_SESSION['userMag'] == 0)
                                    echo " <u>LE DIRECTEUR GENERAL</u>";
                                else
                                    echo "<u>" . strtoupper($titre_resp_mag) . "</u>";
                                ?>
                            </strong> 
                            <br/>
                            <br/> 
                            <br/>
                            <br/> 
                            <br/>  
                            <?php
                            if ($resp_mag)
                                echo "  " . $resp_mag;
                            else
                                echo DIR_GEN;
                            ?>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</page>

     <page backtop="45mm" backbottom="10mm" backleft="10mm" backright="10mm" backcolor="#fff" style="font: arial;">
        <page_footer>
            <?php //include "includes/footer.php";     ?>
        </page_footer>  
        <?php
        $condMag = "";
        $fact = intval($_GET['f']);

        $query = "SELECT f.id_fact,f.bl_tva,f.bl_bic,f.remise_vnt_fact,f.code_fact,
            COALESCE(c.nom_clt,'Client : _ _ _ _ _ _') as nom_clt,
            COALESCE(c.bp_clt,'-') as bp_clt,
        COALESCE(c.adr_clt,'-') as adr_clt,
        COALESCE(c.tel_clt,'-') as tel_clt,
        COALESCE(c.forme_juri_clt,'-') as forme_juri_clt,
        COALESCE(c.regime_clt,'-') as regime_clt,
        COALESCE(c.division_clt,'-') as division_clt,
        COALESCE(c.situation_clt,'-') as situation_clt,
        COALESCE(c.ifu_clt,'-') as ifu_clt,
        COALESCE(c.rccm_clt,'-') as rccm_clt,
            m.nom_mag,f.code_caissier_fact,DATE(f.date_fact) as date_fact,m.nom_mag
                           FROM 
                           t_facture_vente f
                           LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                           INNER JOIN t_magasin m ON f.mag_fact=m.id_mag 
                           WHERE f.id_fact=$fact  limit 1";


        $r = $Mysqli->query($query);
        $result = $r->fetch_assoc();
        $nom = $result['nom_clt'];
        $bp = $result['bp_clt'];
        $adr = $result['adr_clt'];
        $tel = $result['tel_clt'];
        $forme = $result['forme_juri_clt'];
        $regime = $result['regime_clt'];
        $division = $result['division_clt'];
        $situation = $result['situation_clt'];
        $ifu = $result['ifu_clt'];
        $rccm = $result['rccm_clt'];

        $num = $result['code_fact'];
        $date = $result['date_fact'];
        $magasin = $result['nom_mag'];
        $login = $result['code_caissier_fact'];
        $remise = intval($result['remise_vnt_fact']);
        $bltva = $result['bl_tva'];
        $blbic = $result['bl_bic'];
        ?>
        <?php
        // include "includes/header-fact-tva.php";
        ?>

        <table style="width: 100%;" cellspacing="4mm" cellpadding="0">
        <tr> 
            <td style="width: 100%;vertical-align: top;text-align: left;font-size:18px;">
                <strong>BORDEREAU DE LIVRAISON</strong>
            </td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td style="width: 100%">

                <?php
                $query = "SELECT a.code_art,a.nom_art,
                         v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt 
                          FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                          WHERE f.id_fact=$fact ORDER BY a.nom_art ASC";


                $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

                $result = array();
                $total = 0;
                $som = 0;
                ?>

                <table style="width:98%;border-collapse: collapse;" border="0" align='right'>
                    <tr>
                        <td style="width: 45%;text-align: center;padding:5px;vertical-align: middle;">
                            <table border="01" align="center" style="vertical-align: middle;">
                                <tr>
                                    <th>Num&eacute;ro</th>
                                    <th>Date</th>
                                </tr>
                                <tr>
                                    <td>
                                        <strong> BL<?php
                                             if ($bltva == 0) { 
                                            /*if ($blcrdt == 1 && $blgrt == 0)
                                                echo "C-";
                                            elseif ($blcrdt == 1 && $blgrt == 1)
                                                echo "G-";
                                            else
                                                echo "T-";*/
                                            echo $num_court;
                                             }
                                              else
                                              echo $num_court; 
                                            ?>/<?php
                                            $dt = explode('-', $date);
                                            echo $dt[0];
                                            ?>/<?php echo NOM_STRUCT_ABBR; ?>
                                         <?php if ($bltva == 0)  echo "/HT";?> 
                                        </strong>
                                    </td>
                                    <td>
                                        <strong><?php
                                            $dt = explode('-', $date);
                                            echo $dt[2] . "/" . $dt[1] . "/" . $dt[0];
                                            echo " " . $heure;
                                            ?> </strong>&nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td  style="width: 55%;text-align: left;padding:5px;vertical-align: top;">
                            <div style="text-align: center;border:4px double #111;padding:5px;font-weight: bold;"> <u>DOIT (ADRESSE DE LIVRAISON) :</u><br/>
                                <br><?php echo strtoupper($nom); ?><br>  
                                <?php if ($situation != "" && $situation != "-") echo strtoupper($situation) . "<br>"; ?>
                                <?php if ($bp != "" && $bp != "-") echo strtoupper($bp) . "<br>"; ?> 
                                <?php if ($tel != "" && $tel != "-") echo "TEL :  " . strtoupper($tel) . "<br>"; ?> 
                                <?php if ($forme != "" && $forme != "-") echo "FORME JURIDIQUE :  " . strtoupper($forme) . "<br>"; ?> 
                                <?php if ($ifu != "" && $ifu != "-") echo "No IFU :   " . strtoupper($ifu) . "<br>"; ?>  
                                <?php if ($regime != "" && $regime != "-") echo "REGIME IMPOSITION : " . strtoupper($regime) . "<br>"; ?> 
                                <?php if ($division != "" && $division != "-") echo "DOMICILIATION FISCALE :   " . strtoupper($division) . "<br>"; ?>   
                                <?php if ($rccm != "" && $rccm != "-") echo "No RCCM :   " . strtoupper($rccm) . "<br>"; ?>   
                            </div>  
                        </td> 
                    </tr> 
                </table>
 
                    <br/>
                    <table style="width:98%;border-collapse: initial;" align="center" border="01">
                        <tr style="color:black;">
                            <th style="width: 5%;">No</th>
                            <th style="width: 13%;">QUANTITE</th>
                            <th style="width: 55%;">DESIGNATION</th>
                            <th style="width: 27%;">OBSERVATIONS</th>
                        </tr> 

                        <?php
                        while ($row = $r->fetch_assoc()) {
                            $total +=1;
                            ?>
                            <tr >
                                <td class="items">-<?php echo $total; ?>-</td>
                                <td class="items" ><?php echo $row['qte_vnt']; ?></td>
                                <td class="items" style="border:0px;width: 53%; text-align: left;font-weight:normal;"><?php echo strtoupper($row['nom_art']); ?></td>
                                <td class="items" ><br> </td>
                            </tr>

                            <?php
                        }
                        ?>  
                    </table>
                
                    <br/> 
                    <table cellspacing="0" style="width: 100%;background: #fff; text-align: center; font-size: 8pt;" align="center">
                        <tr>
                            <td class="signataire" style="width: 50%; text-align: center;">
                                <strong><u>LE LIVREUR</u></strong> 
                            </td>
                            <td class="signataire" style="width: 50%; text-align: center;">

                                <strong> <u>LE RECEPTIONNISTE</u></strong> 
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
    </page> 
