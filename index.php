<?php
// Fichier : index.php
session_start();

define('BASE_PATH', '/APPDEUX/APPCOMMUN');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// fonctions globales, notamment nettoyerDonnees() & crypterMdp()
include_once __DIR__ . '/controleurs/fonctions.php';

// choix du contrôleur
$url = $_GET['cible'] ?? 'utilisateurs';
$controller = __DIR__ . '/controleurs/' . $url . '.php';

if (file_exists($controller)) {
    include $controller;
} else {
    include __DIR__ . '/vues/erreur404.php';
}
