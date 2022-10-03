<?php

$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$production = $hostname!='ISSPPW10-HD-071'?'DEVELOPPEMENT':'ANCIEN';
$variable = 'ANCIEN';// NOUVEAU,ANCIEN,DEVELOPPEMENT
$file="c:\SAUVEGARDE_BD_GESTION_STOCK\bkp_gestion_stock_".date("Y-m-d_H\hi").".sql";

switch ($variable) {
    case 'NOUVEAU':
        shell_exec("C:/wamp64/bin/mysql/mysql5.7.31/bin/mysqldump -u root --password=adminroot -f --databases bd_songo_sgns > $file");
        break;
    case 'ANCIEN':
        shell_exec("C:/wamp/bin/mysql/mysql5.6.17/bin/mysqldump -u root --password=adminroot -f --databases bd_songo_sgns > $file");
        break;
    case 'DEVELOPPEMENT':
        shell_exec("C:/xampp/mysql/bin/mysqldump -u root --password=adminroot -f --databases bd_songo_sgns > $file");
        break;
    default:
        break;
}

 ?>