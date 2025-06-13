<?php
// Fichier : test_login.php

// Affiche toutes les erreurs pour un débogage maximal
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Début du test de connexion</h1>";

// --- 1. Test de la connexion à la BDD ---
$host = 'localhost';
$dbname = 'APPFINALE';
$user = 'root';
$pass = ''; // ou '' sur WAMP

try {
    $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green;'>✅ Connexion à la base de données APPFINALE réussie.</p>";
} catch (Exception $e) {
    die("<p style='color:red;'>❌ ÉCHEC de la connexion à la BDD : " . $e->getMessage() . "</p>");
}

// --- 2. Test de la récupération de l'utilisateur ---
$email_a_tester = 'legrandjacques.ruben@gmail.com';
$password_a_tester = 'GUSTO';

echo "<h2>Test pour l'utilisateur : " . htmlspecialchars($email_a_tester) . "</h2>";

try {
    $stmt = $bdd->prepare('SELECT * FROM utilisateurs WHERE email = :email');
    $stmt->execute(['email' => $email_a_tester]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur) {
        echo "<p style='color:green;'>✅ Utilisateur trouvé dans la base de données.</p>";
        echo "<pre>";
        print_r($utilisateur); // Affiche tout le contenu de l'utilisateur
        echo "</pre>";

        // --- 3. Test de la vérification du mot de passe ---
        $hash_en_bdd = $utilisateur['password'];
        echo "<p>Hash en BDD : " . htmlspecialchars($hash_en_bdd) . "</p>";
        echo "<p>Mot de passe saisi : " . htmlspecialchars($password_a_tester) . "</p>";

        if (password_verify($password_a_tester, $hash_en_bdd)) {
            echo "<h2 style='color:green;'>✅ SUCCÈS : password_verify() a fonctionné ! La connexion devrait être possible.</h2>";
        } else {
            echo "<h2 style='color:red;'>❌ ÉCHEC : password_verify() a échoué.</h2>";
            echo "<p>Raisons possibles : Le hash en BDD est corrompu/tronqué, ou il y a un problème avec la version de PHP.</p>";
        }

    } else {
        echo "<p style='color:red;'>❌ ÉCHEC : Aucun utilisateur trouvé pour cet email.</p>";
    }

} catch (Exception $e) {
    die("<p style='color:red;'>❌ ERREUR SQL lors de la recherche : " . $e->getMessage() . "</p>");
}
?>