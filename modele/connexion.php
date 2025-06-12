<?php
// Fichier : modele/connexion.php (CORRIGÉ)

// 1. On charge la configuration d'abord
require_once(__DIR__ . '/../config.php');

// 2. On utilise les constantes de config.php DANS le try...catch
try {
    // On se connecte à la BDD locale en utilisant les constantes
    $bdd = new PDO(
        "mysql:host=" . DB_HOST_LOCAL . ";dbname=" . DB_NAME_LOCAL . ";charset=utf8",
        DB_USER_LOCAL,
        DB_PASS_LOCAL,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(Exception $e) {
    // Si la connexion échoue, on affiche une erreur claire
    die('Erreur de connexion à la base de données locale : '.$e->getMessage());
}