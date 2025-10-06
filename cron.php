<?php
$file="c:/SAUVEGARDE_BD_GESTION_STOCK/bkp_gestion_stock_".date("Y-m-d_H\hi").".sql";
$zipFile="c:/SAUVEGARDE_BD_GESTION_STOCK/bkp_gestion_stock_".date("Y-m-d_H\hi").".zip";

// Export de la base
shell_exec("C:/wamp64/bin/mysql/mysql5.7.31/bin/mysqldump -u adminroot --password=adminroot -f --databases bd_songo_sgns > $file");

// Compression du fichier SQL en ZIP
shell_exec("powershell Compress-Archive -Path $file -DestinationPath $zipFile");

// Optionnel : supprimer le .sql après compression
// unlink($file);
?>
