<?php
// Fichier : config.php (CORRIGÉ ET COMPLET)

// --- Base de données LOCALE ---
define('DB_HOST_LOCAL', 'localhost');
define('DB_NAME_LOCAL', 'APPFINALE');
define('DB_USER_LOCAL', 'root');
define('DB_PASS_LOCAL', PHP_OS_FAMILY === 'Windows' ? 'root' : '');

// --- Base de données COMMUNE ---
define('DB_HOST_COMMUN', 'mysql-gusto.alwaysdata.net');
define('DB_NAME_COMMUN', 'gusto_g5');
define('DB_USER_COMMUN', 'gusto');
define('DB_PASS_COMMUN', 'RestoGustoG5');

// --- Paramètres SMTP (Email) ---
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'legrandjacques.ruben@gmail.com'); // **À CHANGER**
define('SMTP_PASS', 'ebwc bgag reay cpti'); // **À CHANGER** pour votre mot de passe d'application

// --- CLÉ SECRÈTE POUR LA SÉCURITÉ DES JETONS ---
// IMPORTANT : Gardez cette clé secrète ! Changez-la pour votre projet.
define('SECRET_KEY', 'v0tr3_ChA1n3_S3cr3t3_Tr3s_L0ngu3_Et_C0mpl3x3_!@#$');

// --- Environment: 'development' ou 'production' ---
define('APP_ENV', 'development'); // ou 'production'