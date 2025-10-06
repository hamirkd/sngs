<?php

$file='G:\"Mon Drive"\SAUVEGARDE_BD_GESTION_STOCK\bkp_gestion_stock_'.date("Y-m-d_H\hi").".sql";

shell_exec("C:/wamp64/bin/mysql/mysql5.7.31/bin/mysqldump -u adminroot --password=adminroot -f --databases bd_songo_sgns > $file");

 ?>