<?php
// Fichier de connexion pour la base de données COMMUNE (alwaysdata)

$host_commun = 'mysql-gusto.alwaysdata.net';

// Le nom de la base est 'gusto_g5' et non 'gusto_appfinale'
$dbname_commun = 'gusto_g5'; // <-- CORRECTION IMPORTANTE

$user_commun = 'gusto';
$password_commun = 'RestoGustoG5';

try {
    // ... reste du code ...
    $bdd_commune = new PDO(
        "mysql:host=$host_commun;dbname=$dbname_commun;charset=utf8",
        $user_commun,
        $password_commun,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(Exception $e) {
    die('Erreur de connexion à la base de données commune : '.$e->getMessage());
}
?>