<?php
// URL à appeler
$url = "http://127.0.0.1/lina/app/core/paiementFromMobile.class.php?x=validationOrangeMoneyPaiementAutomatiqueTout";

// Nom du fichier log
$logFile = __DIR__ . '/paiement_auto_tout.log';

// Récupérer le contenu de l'URL
$response = file_get_contents($url);

// Ajouter la réponse au log avec date et heure
file_put_contents(
    $logFile,
    "===============================\n" .
    date('Y-m-d H:i:s') . "\n" .
    "-------------------------------\n" .
    $response . "\n\n",
    FILE_APPEND
);

echo "Execution terminee. Voir $logFile\n";
