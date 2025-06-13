<?php
// Fichier: modele/requetes.gestion.php (Version finale propre)

function listerDispositifsParEtat(PDO $bdd, string $type, bool $estActif): array {
    $sql = "SELECT d.id, d.nom, d.nom_table_bdd FROM dispositifs d
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

function listerTousLesCapteurs(PDO $bdd): array {
    // On sélectionne maintenant toutes les colonnes utiles
    $sql = "SELECT id, nom, nom_table_bdd, seuil, unite, type_alerte FROM dispositifs WHERE type = 'capteur' ORDER BY id";
    return $bdd->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function mettreAJourSeuil(PDO $bdd, int $id, ?float $seuil): bool {
    $sql = "UPDATE dispositifs SET seuil = :seuil WHERE id = :id";
    $stmt = $bdd->prepare($sql);
    return $stmt->execute(['seuil' => $seuil, 'id' => $id]);
}