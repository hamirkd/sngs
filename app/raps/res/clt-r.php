 <?php
 
session_name('SessSngS');
session_start();
?>
<?php
         include "includes/const.php"; 
 ?>
<style type="text/css">
    table tr td{
        border:1px solid #aaa;
        padding:3px;
    }
    table tr th{
        padding:3px;
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
                LISTE DES CLIENTS [<?php   echo $_SESSION['nomMag'];  ?> ]
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
        <u>Liste complete des clients
        </u>   
    </div> 
    <br>


    <?php
    
   if($_SESSION['userMag']>0)
        $query = "SELECT c.*  FROM t_client c 
             WHERE c.user_clt in (SELECT id_user from t_user where mag_user=".$_SESSION['userMag'].")
                 order by c.nom_clt,tel_clt";
        else 
             $query = "SELECT c.*  FROM t_client c order by c.nom_clt,tel_clt"; 
 
     
    $r = $Mysqli->query($query) or die($this->mysqli->error . __LINE__);

    if ($r->num_rows > 0) {
        $result = array();
        $total = 0;
        ?>
        <table class="data" cellspacing="0" cellpadding="5mm" style="width: 100%; border: solid 1px black;text-align: center; font-size: 10pt;">
            <tr style="">
                <th style="width: 10%;border:1px solid #aaa;text-align: left">Code</th> 
                <th style="width: 38%;border:1px solid #aaa;text-align: left">Nom/Raison Sociale</th>
                <th style="width: 17%;border:1px solid #aaa;text-align: left">Tel</th>
                <th style="width: 20%;border:1px solid #aaa;text-align: left">Mail</th>
                <th style="width: 15%;border:1px solid #aaa;text-align: left">Adresse/Rue</th> 
            </tr>


    <?php
    while ($row = $r->fetch_assoc()) {
        $total +=1
        ?>
                <tr >
                     <td style="width: 10%;text-align: left"><?php echo ucwords(strtoupper($row['code_clt'])); ?></td>
                    <td style="width: 38%; text-align: left"><?php echo ucfirst(strtoupper($row['nom_clt'])); ?></td>
                    <td style="width: 17%; text-align: left"><?php echo ucfirst(strtolower($row['tel_clt'])); ?></td>
                    <td style="width: 20%; text-align: left"><em><?php echo ucfirst(strtolower($row['mail_clt'])); ?></em></td>
                    <td style="width: 15%; text-align: left"><?php echo ucfirst(strtolower($row['adr_clt'])); ?></td>
                  </tr>

        <?php
    }
    ?> 
        </table>

        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 8pt;">
            <tr>
                <th style="width: 100%; text-align: left;"><em>Total : <?php echo $total; ?> client(s)</em></th>
            </tr>
        </table>
    <?php
} else {
    ?> 
        <table cellspacing="0" style="width: 100%; border: solid 1px black; background: #fff; text-align: center; font-size: 10pt;">
            <tr>
                <th style="width: 100%; text-align: left;">Aucun client ... </th> 
            </tr>
        </table>
    <?php
}
?> 
</page> 