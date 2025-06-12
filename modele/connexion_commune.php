<?php
// Fichier : modele/connexion_commune.php (CORRIGÉ)

// 1. On charge la configuration
require_once(__DIR__ . '/../config.php');

// 2. On se connecte à la BDD commune avec les bonnes constantes
try {
    $bdd_commune = new PDO(
        "mysql:host=" . DB_HOST_COMMUN . ";dbname=" . DB_NAME_COMMUN . ";charset=utf8",
        DB_USER_COMMUN,
        DB_PASS_COMMUN,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(Exception $e) {
    die('Erreur de connexion à la base de données commune : '.$e->getMessage());
}