<?php
require_once(__DIR__ . '/../config/config.php');

function getLocalPDO(): PDO {
    try {
        return new PDO(
            "mysql:host=" . DB_HOST_LOCAL . ";dbname=" . DB_NAME_LOCAL . ";charset=utf8",
            DB_USER_LOCAL,
            DB_PASS_LOCAL,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (Exception $e) {
        error_log('Erreur de connexion à la base de données locale : ' . $e->getMessage());
        throw new Exception('Erreur de connexion à la base locale.');
    }
}

function getCommunPDO(): PDO {
    try {
        return new PDO(
            "mysql:host=" . DB_HOST_COMMUN . ";dbname=" . DB_NAME_COMMUN . ";charset=utf8",
            DB_USER_COMMUN,
            DB_PASS_COMMUN,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (Exception $e) {
        error_log('Erreur de connexion à la base de données commune : ' . $e->getMessage());
        throw new Exception('Erreur de connexion à la base commune.');
    }
}
