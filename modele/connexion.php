<?php
// modele/connexion.php

$host_local     = 'localhost';
$dbname_local   = 'APPFINALE';
$user_local     = 'root';
$password_local = '';

try {
    // connexion à la BDD locale
    $bdd = new PDO(
        "mysql:host=$host_local;dbname=$dbname_local;charset=utf8",
        $user_local,
        $password_local,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    die('Erreur de connexion à la BDD locale : ' . $e->getMessage());
}
