<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

require_once(__DIR__ . '/modele/connexion.php');

if (isset($_POST['id'], $_POST['etat'])) {
    $id = (int)$_POST['id'];
    $etat = (int)$_POST['etat'];

    $stmt = $bdd->prepare(
        'INSERT INTO etats_actionneurs (id_dispositif, etat) VALUES (:id, :etat)
         ON DUPLICATE KEY UPDATE etat = :etat'
    );
    $success = $stmt->execute(['id' => $id, 'etat' => $etat]);

    echo json_encode(['success' => $success]);
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
}