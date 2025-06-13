<?php
// modele/connexion.php

$host_local     = 'localhost';
$dbname_local   = 'APPFINALE';
$user_local     = 'root';
$password_local = '';

$host_commun = 'mysql-gusto.alwaysdata.net';
$dbname_commun = 'gusto_g5';
$user_commun = 'gusto';
$password_commun = 'RestoGustoG5';

try {
    $bdd = new PDO(
        "mysql:host=$host_local;dbname=$dbname_local;charset=utf8",
        $user_local,
        $password_local,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    die('Erreur de connexion à la BDD locale : ' . $e->getMessage());
}

try {
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