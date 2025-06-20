<?php
// Fichier : APPCOMMUN/index.php

session_start(); 

define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/');

require_once __DIR__ . '/../app/config/config.php';

if (defined('APP_ENV') && APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

require_once __DIR__ . '/../app/models/connexion.php';

try {
    $bdd = getLocalPDO();
    $bdd_commune = getCommunPDO();
} catch (Exception $e) {
    die('Impossible de se connecter à la base de données : ' . $e->getMessage());
}

require_once __DIR__ . '/../app/controllers/fonctions.php';

$defaultController = 'utilisateurs';
$controller = $defaultController;

if (!empty($_GET['cible'])) {
    $requested = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['cible']);
    if ($requested !== '') {
        $controller = $requested;
    }
}

$controllerFile = __DIR__ . '/../app/controllers/' . $controller . '.php';

if (is_file($controllerFile) && is_readable($controllerFile)) {
    include $controllerFile;
} else {
    http_response_code(404);
    include __DIR__ . '/../app/views/erreur404.php';
}
?>