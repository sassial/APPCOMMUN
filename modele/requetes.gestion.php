<?php
// Fichier: modele/requetes.gestion.php

/**
 * Liste les dispositifs d'un certain type selon leur état (actif ou inactif).
 */
// Dans modele/requetes.gestion.php


// Fichier: modele/requetes.gestion.php
function listerDispositifsParEtat(PDO $bdd, string $type, bool $estActif): array {
    $sql = "SELECT d.id, d.nom FROM dispositifs d
            LEFT JOIN etats_actionneurs e ON e.id_dispositif = d.id
            WHERE d.type = :type AND IFNULL(e.etat, 0) " . ($estActif ? '!= 0' : '= 0');
    $stmt = $bdd->prepare($sql);
    $stmt->execute(['type' => $type]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function basculerEtat(PDO $bdd, int $id): void {
    $sql = "INSERT INTO etats_actionneurs (id_dispositif, etat) VALUES (:id, 1) ON DUPLICATE KEY UPDATE etat = IF(etat = 0, 1, 0)";
    $stmt = $bdd->prepare($sql);
    $stmt->execute(['id' => $id]);
}

/**
 * Récupère tous les seuils des capteurs.
 */
function recupererSeuils(PDO $bdd): array {
    $sql = "SELECT d.id, d.nom, IFNULL(e.etat, 0) AS seuil, d.nom_table_bdd
            FROM dispositifs d
            LEFT JOIN etats_actionneurs e ON d.id = e.id_dispositif
            WHERE d.type = 'capteur'";
    return $bdd->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Ajuste le seuil d'un capteur.
 */
function ajusterSeuil(PDO $bdd, int $id, int $delta): void {
    // On s'assure que le seuil ne devienne pas négatif
    $sql = "
        INSERT INTO etats_actionneurs (id_dispositif, etat) VALUES (:id, :delta)
        ON DUPLICATE KEY UPDATE etat = GREATEST(0, etat + :delta)";
    $stmt = $bdd->prepare($sql);
    $stmt->execute(['id' => $id, 'delta' => $delta]);
}

/**
 * Donne l'unité pour un nom de table de capteur.
 */
function getUnite(string $nomTable): string {
    return match($nomTable) {
        'CapteurTempHum'   => '°C',
        'CapteurLumiere'   => 'lux',
        'CapteurProximite' => 'cm',
        'CapteurGaz'       => 'ppm',
        'CapteurSon'       => 'dB',
        default            => '',
    };
}