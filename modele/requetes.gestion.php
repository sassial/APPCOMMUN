<?php
require_once(__DIR__ . '/connexion.php');

function listerDispositifs(PDO $bdd): array {
    return $bdd->query('SELECT * FROM dispositifs ORDER BY type, nom')->fetchAll(PDO::FETCH_ASSOC);
}

function ajouterDispositif(PDO $bdd, array $data): bool {
    $query = 'INSERT INTO dispositifs (nom, type, nom_table_bdd, unite) VALUES (:nom, :type, :nom_table_bdd, :unite)';
    $stmt = $bdd->prepare($query);
    return $stmt->execute($data);
}

function supprimerDispositif(PDO $bdd, int $id): bool {
    $stmt = $bdd->prepare('DELETE FROM dispositifs WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}