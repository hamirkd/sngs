<table style="width: 100%;" cellspacing="2mm" cellpadding="0" border='0'>
    <tr> 
            <td style="width: 58%;vertical-align: top;text-align: left;border-bottom:1px dashed #444">
                         <strong> <?php echo NOM_STRUCT_FULL. " (".NOM_STRUCT_ABBR.")";?></strong> <br/>
                          <strong>TEL :</strong> <?php echo TEL; ?> / <?php echo CEL;?>
            </td>
             <td style="width: 2%;border-bottom:1px dashed #444"> 
                
            </td>
            <td style="width: 40%;text-align: right;border-bottom:1px dashed #444">
             </td>
        </tr>    
    <tr> 
            <td style="width: 58%;vertical-align: top;text-align: left;">
                &nbsp;
            </td>
             <td style="width: 2%"> 
                
            </td>
            <td style="width: 40%;text-align: right;">
                <strong><em>Ouagadougou, le <?php $dt=explode('-',$date);echo $dt[2]."/".$dt[1]."/".$dt[0]; echo " ".$heure; ?></em></strong><br>
            </td>
        </tr>
</table>