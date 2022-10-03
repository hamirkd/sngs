<table style="width: 98%;" cellspacing="4mm" cellpadding="0">
        <tr>
           
            <td style="width: 43%;vertical-align: top;text-align: left;font-size:18px;">
                        <table>
                           <tr>
                               <td style="font-size:18px;"><b>Client </b></td>
                                <td style="font-size:18px;">: <?php echo $nom; ?></td>
                           </tr>
                           <tr>
                                <td style="font-size:18px;"><b>Boutique </b></td>
                               <td style="font-size:18px;">: <?php echo $magasin; ?></td>
                           </tr>
                           <tr>
                                <td style="font-size:18px;"><b>Caissier &nbsp;</b></td>
                               <td style="font-size:18px;">: <?php echo $login; ?></td>
                           </tr>
                       </table>
            </td>
             <td style="width: 14%"> 
            </td>
            <td style="width: 43%;text-align: right;">
                <br><br><br><br><br><br>
                <strong>Ouagadougou, le </strong><?php $dt=explode('-',$date);echo $dt[2]."/".$dt[1]."/".$dt[0]; ?><br>
            </td>
        </tr>
        </table>