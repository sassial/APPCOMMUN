<?php
// Fichier de connexion pour la base de données LOCALE (utilisateurs)

$host_local = 'localhost';
$dbname_local = 'APPFINALE'; // La base avec votre table 'utilisateurs'
$user_local = 'root';
$password_local = ''; // ou vide "" selon votre configuration MAMP/WAMP

try {
    // On garde le nom de variable $bdd pour la compatibilité avec le code existant
    $bdd = new PDO(
        "mysql:host=$host_local;dbname=$dbname_local;charset=utf8",
        $user_local,
        $password_local,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(Exception $e) {
    die('Erreur de connexion à la base de données locale : '.$e->getMessage());
}
?>