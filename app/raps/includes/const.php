<?php
 
$querystruct = "SELECT abbr_nom_struct,sigle_struct,
    nom_struct,reg_imp_struct,div_fisc_struct,situa_geo_struct,bp_struct,
    directeur_struct,mob_struct,tel_struct
    FROM t_structure WHERE 1=1 Limit 1";
        $rstruct = $Mysqli->query($querystruct);
        $rowstruct = $rstruct->fetch_assoc();
define("NOM_STRUCT_ABBR",$rowstruct['abbr_nom_struct']);
define("NOM_STRUCT_COURANT", $rowstruct['sigle_struct']);
define("NOM_STRUCT_FULL", $rowstruct['nom_struct']);

define("REG_IMP", $rowstruct['reg_imp_struct']);
define("DIV_FISC", $rowstruct['div_fisc_struct']);
define("SIT_GEO", $rowstruct['situa_geo_struct']);


define("DIR_GEN", $rowstruct['directeur_struct']);

define("BP", $rowstruct['bp_struct']);
define("TEL", $rowstruct['tel_struct']);
define("CEL", $rowstruct['mob_struct']); 
 
?> 