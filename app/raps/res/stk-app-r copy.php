<?php
session_name('SessSngS');
session_start();
?>
<?php
include "includes/const.php";

    $search = $_GET;
    
 // Liste des articles  approvisionnes
        
    if ($_SESSION['userMag'] > 0)/**/ {
        $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($_SESSION['userMag']) . " Limit 1";
        $rmag = $Mysqli->query($querymag);
        $rowmag = $rmag->fetch_assoc();
      // $rowmag['nom_mag']; 
       
        $query = "SELECT app.bon_liv_appro,date_format(app.date_appro,'%d %b %Y') as date_appro,
                a.nom_art,m.code_mag,c.nom_cat,f.nom_frns,
                apa.qte_appro_art,apa.prix_appro_art
                FROM t_approvisionnement app
                INNER JOIN t_approvisionnement_article apa ON app.id_appro=apa.appro_appro_art
                INNER JOIN t_magasin m on m.id_mag=apa.mag_appro_art
                INNER JOIN t_article a ON apa.art_appro_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                INNER JOIN t_fournisseur f ON app.frns_appro=f.id_frns
                WHERE apa.mag_appro_art=" . intval($_SESSION['userMag']);

        if (!empty($search['mg']))
            $query.=" AND apa.mag_appro_art=" . intval($search['mg']);

        if (!empty($search['frns']))
            $query.=" AND f.id_frns=" . intval($search['frns']);

        if (!empty($search['art']))
            $query.=" AND apa.art_appro_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (isset($search['bc']) && $search['bc'] != "")
            $query.=" AND app.bl_bon_dette=" . intval($search['bc']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(app.date_appro)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(app.date_appro) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query .= " Order by app.date_appro DESC,c.nom_cat,a.nom_art";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            $totalqte = 0;
            $totalmont = 0;
            
                    // <!-- <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                    // <th style="width: 30%;border:1px solid black;text-align: left">Bon </th> 
                    // <th style="width: 32%;border:1px solid black;text-align: left">Designation</th>
                    // <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
                    // <th style="width: 8%;border:1px solid black;text-align: left">Prix</th> 
               
                while ($row = $r->fetch_assoc()) {
                    $total +=1;
                    $totalqte +=$row['qte_appro_art'];
                    $totalmont +=$row['qte_appro_art'] * $row['prix_appro_art'];
                    
                    // <tr >
                    //     <td style="width: 13%; text-align: left;"> echo $row['date_appro']; </td>
                    //     <td style="width: 30%; text-align: left;"> echo $row['bon_liv_appro']; </td>
                    //     <td style="width: 32%; text-align: left"> echo ucfirst(strtolower($row['nom_art'])); </td>
                    //     <td style="width: 5%;text-align: right"> echo $row['qte_appro_art']; </td> 
                    //     <td style="width: 8%;text-align: right"> echo $row['prix_appro_art']; </td> 
                    //     <td style="width: 12%;text-align: right"> echo $row['qte_appro_art'] * $row['prix_appro_art']; </td> 
                    // </tr>

                }
                
        }
    } elseif ($_SESSION['userMag'] < 1 && !empty($search['mg']))/**/ {
        $querymag = "SELECT nom_mag FROM t_magasin WHERE id_mag=" . intval($search['mg']) . " Limit 1";
        $rmag = $Mysqli->query($querymag);
        $rowmag = $rmag->fetch_assoc();
        
            //  echo $rowmag['nom_mag'];    
        

        
        $query = "SELECT app.bon_liv_appro,app.date_appro,
                a.nom_art,m.code_mag,c.nom_cat,f.nom_frns,
                apa.qte_appro_art,apa.prix_appro_art
                FROM t_approvisionnement app
                INNER JOIN t_approvisionnement_article apa ON app.id_appro=apa.appro_appro_art
                INNER JOIN t_magasin m on m.id_mag=apa.mag_appro_art
                INNER JOIN t_article a ON apa.art_appro_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                INNER JOIN t_fournisseur f ON app.frns_appro=f.id_frns
                WHERE apa.mag_appro_art=" . intval($search['mg']);


        if (!empty($search['mg']))
            $query.=" AND apa.mag_appro_art=" . intval($search['mg']);

        if (!empty($search['frns']))
            $query.=" AND f.id_frns=" . intval($search['frns']);

        if (!empty($search['art']))
            $query.=" AND apa.art_appro_art=" . intval($search['art']);

        if (!empty($search['cat']))
            $query.=" AND id_cat=" . intval($search['cat']);

        if (isset($search['bc']) && $search['bc'] != "")
            $query.=" AND app.bl_bon_dette=" . intval($search['bc']);

        if (!empty($search['d']) && empty($search['f']))
            $query.=" AND date(app.date_appro)='" . isoToMysqldate($search['d']) . "'";

        if (!empty($search['f']))
            $query.=" AND date(app.date_appro) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

        $query .= " Order by app.date_appro DESC,c.nom_cat,a.nom_art";


        $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            $total = 0;
            $totalqte = 0;
            $totalmont = 0;
            


                // <tr style="">
                //     <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                //     <th style="width: 30%;border:1px solid black;text-align: left">Bon </th> 
                //     <th style="width: 32%;border:1px solid black;text-align: left">Designation</th>
                //     <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
                //     <th style="width: 8%;border:1px solid black;text-align: left">Prix</th> 
                //     <th style="width: 12%;border:1px solid black;text-align: left">Mnt</th> 
                // </tr>


                while ($row = $r->fetch_assoc()) {
                    $total +=1;
                    $totalqte +=$row['qte_appro_art'];
                    $totalmont +=$row['qte_appro_art'] * $row['prix_appro_art'];
                    
                    // <tr >
                    //     <td style="width: 13%; text-align: left;"> echo $row['date_appro']; </td>
                    //     <td style="width: 30%; text-align: left;"> echo $row['bon_liv_appro']; </td>
                    //     <td style="width: 32%; text-align: left"> echo ucfirst(strtolower($row['nom_art'])); </td>
                    //     <td style="width: 5%;text-align: right"> echo $row['qte_appro_art']; </td> 
                    //     <td style="width: 8%;text-align: right"> echo $row['prix_appro_art']; </td> 
                    //     <td style="width: 12%;text-align: right"> echo $row['qte_appro_art'] * $row['prix_appro_art']; </td> 
                    // </tr>

                }
                

        }
    } else {
        
        $querymag = "SELECT id_mag,nom_mag FROM t_magasin ";
        $rmag = $Mysqli->query($querymag);

        $totalmontGenerale = 0;

        while ($rowmag = $rmag->fetch_assoc()) {
           
            $query = "SELECT app.bon_liv_appro,app.date_appro,
                a.nom_art,m.code_mag,c.nom_cat,f.nom_frns,
                apa.qte_appro_art,apa.prix_appro_art
                FROM t_approvisionnement app
                INNER JOIN t_approvisionnement_article apa ON app.id_appro=apa.appro_appro_art
                INNER JOIN t_magasin m on m.id_mag=apa.mag_appro_art
                INNER JOIN t_article a ON apa.art_appro_art=a.id_art 
                INNER JOIN t_categorie_article c ON c.id_cat=a.cat_art
                INNER JOIN t_fournisseur f ON app.frns_appro=f.id_frns
                WHERE apa.mag_appro_art=" . intval($rowmag['id_mag']);


            if (!empty($search['mg']))
                $query.=" AND apa.mag_appro_art=" . intval($search['mg']);

            if (!empty($search['frns']))
                $query.=" AND f.id_frns=" . intval($search['frns']);

            if (!empty($search['art']))
                $query.=" AND apa.art_appro_art=" . intval($search['art']);

            if (!empty($search['cat']))
                $query.=" AND id_cat=" . intval($search['cat']);

            if (!empty($search['d']) && empty($search['f']))
                $query.=" AND date(app.date_appro)='" . isoToMysqldate($search['d']) . "'";

            if (!empty($search['f']))
                $query.=" AND date(app.date_appro) between '" . isoToMysqldate($search['d']) . "' 
                AND '" . isoToMysqldate($search['f']) . "'";

            $query .= " Order by app.date_appro DESC,c.nom_cat,a.nom_art";


            $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $result = array();
                $total = 0;
                $totalqte = 0;
                $totalmont = 0;
                
                    // <?php echo $rowmag['nom_mag'];   
               
                    // <tr style="">
                    //     <th style="width: 13%;border:1px solid black;text-align: left">Date</th> 
                    //     <th style="width: 30%;border:1px solid black;text-align: left">Bon </th> 
                    //     <th style="width: 32%;border:1px solid black;text-align: left">Designation</th>
                    //     <th style="width: 5%;border:1px solid black;text-align: left">Qte</th> 
                    //     <th style="width: 8%;border:1px solid black;text-align: left">Prix</th> 
                    //     <th style="width: 12%;border:1px solid black;text-align: left">Mnt</th> 
                    // </tr>


                    while ($row = $r->fetch_assoc()) {
                        $total +=1;
                        $totalqte +=$row['qte_appro_art'];
                        $totalmont +=$row['qte_appro_art'] * $row['prix_appro_art'];
                        $totalmontGenerale += $row['qte_appro_art'] * $row['prix_appro_art'];
                        
                        // <tr >
                        //     <td style="width: 13%; text-align: left;"> echo $row['date_appro'];</td>
                        //     <td style="width: 30%; text-align: left;"> echo $row['bon_liv_appro'];</td>
                        //     <td style="width: 32%; text-align: left"> echo ucfirst(strtolower($row['nom_art']));</td>
                        //     <td style="width: 5%;text-align: right"> echo $row['qte_appro_art'];</td> 
                        //     <td style="width: 8%;text-align: right"> echo $row['prix_appro_art'];</td> 
                        //     <td style="width: 12%;text-align: right"> echo $row['qte_appro_art'] * $row['prix_appro_art'];</td> 
                        // </tr>

                    }

            }
        }
        
    }
    