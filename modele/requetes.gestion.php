<?php

const TYPE_CAPTEUR = 'capteur';

/**
 * Liste les dispositifs d'un certain type selon leur état (actif ou inactif).
 *
 * @param PDO $bdd Instance PDO de la base de données.
 * @param string $type Type de dispositif (ex: 'capteur', 'actionneur').
 * @param bool $estActif Filtre sur l'état (true = actifs, false = inactifs).
 * @return array Liste des dispositifs.
 */
function listerDispositifsParEtat(PDO $bdd, string $type, bool $estActif): array {
    try {
        $sql = "SELECT d.id, d.nom
                FROM dispositifs d
                LEFT JOIN etats_actionneurs e ON e.id_dispositif = d.id
                WHERE d.type = :type AND IFNULL(e.etat, 0) = :etat
                ORDER BY d.nom";

        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->bindValue(':etat', $estActif ? 1 : 0, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur listerDispositifsParEtat : " . $e->getMessage());
        return [];
    }
}

/**
 * Bascule l'état (actif/inactif) d'un dispositif actionneur.
 *
 * @param PDO $bdd Instance PDO de la base de données.
 * @param int $id Identifiant du dispositif.
 * @return void
 */
function basculerEtat(PDO $bdd, int $id): void {
    try {
        $sql = "INSERT INTO etats_actionneurs (id_dispositif, etat)
                VALUES (:id, 1)
                ON DUPLICATE KEY UPDATE etat = IF(etat = 0, 1, 0)";

        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            error_log("Aucune modification d'état effectuée pour le dispositif ID $id.");
        }
    } catch (PDOException $e) {
        error_log("Erreur basculerEtat : " . $e->getMessage());
    }
}

/**
 * Récupère les seuils des capteurs.
 *
 * @param PDO $bdd Instance PDO de la base de données.
 * @return array Liste des seuils par capteur.
 */
function recupererSeuils(PDO $bdd): array {
    try {
        $sql = "SELECT d.id, d.nom, IFNULL(e.etat, 0) AS seuil, d.nom_table_bdd
                FROM dispositifs d
                LEFT JOIN etats_actionneurs e ON d.id = e.id_dispositif
                WHERE d.type = :type
                ORDER BY d.nom";

        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':type', TYPE_CAPTEUR, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur recupererSeuils : " . $e->getMessage());
        return [];
    }
}

/**
 * Ajuste le seuil d'un capteur en ajoutant un delta (positif ou négatif).
 *
 * @param PDO $bdd Instance PDO de la base de données.
 * @param int $id Identifiant du capteur.
 * @param int $delta Variation à appliquer au seuil.
 * @return void
 */
function ajusterSeuil(PDO $bdd, int $id, int $delta): void {
    if ($delta === 0) {
        return; // Aucune modification nécessaire
    }

    try {
        $sql = "INSERT INTO etats_actionneurs (id_dispositif, etat)
                VALUES (:id, :delta)
                ON DUPLICATE KEY UPDATE etat = GREATEST(0, etat + :delta)";

        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':delta', $delta, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur ajusterSeuil : " . $e->getMessage());
    }
}

/**
 * Retourne l'unité ou les unités associées à une table de capteur.
 *
 * @param string $nomTable Nom de la table du capteur.
 * @return string|array Unité(s) correspondante(s).
 */
function getNomTable(string $nom): ?string {
    $map = [
        'Gaz' => 'CapteurGaz',
        'Lumière' => 'CapteurLumiere',
        'Proximité' => 'CapteurProximite',
        'Son ambiant' => 'CapteurSon',
        'Température & Humidité' => 'capteur_temp_hum'
    ];
    return $map[$nom] ?? null;
}

/**
 * Retourne l'unité ou les unités associées à une table de capteur.
 *
 * @param string $nomTable Nom de la table du capteur.
 * @return string|array Unité(s) correspondante(s).
 */
function getUnite(string $nomTable): string|array {
    static $unites = [
        'capteur_temp_hum'   => ['temperature' => '°C', 'humidite' => '%'],
        'CapteurLumiere'   => 'lux',
        'CapteurProximite' => 'cm',
        'CapteurGaz'       => 'ppm',
        'CapteurSon'       => 'dB',
    ];

    return $unites[$nomTable] ?? '';
}