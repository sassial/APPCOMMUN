<?php

if (isset($_GET['decibels']) && is_numeric($_GET['decibels'])) {
    $valeur_db = (float)$_GET['decibels'];

    try {
        // --- CORRECTION IMPORTANTE ICI ---
        // On insère dans la table `Capteur Son` et dans les colonnes `valeur` et `id_capteur` (par exemple)
        // ADAPTEZ LES NOMS DE COLONNES !
        $query = 'INSERT INTO `Capteur Son` (valeur, id_capteur) VALUES (:valeur_db, :id_capteur)';

        $statement = $bdd_commune->prepare($query);
        $statement->bindValue(':valeur_db', $valeur_db);
        $statement->bindValue(':id_capteur', 1);
        $statement->execute();

        http_response_code(200);
        echo "Success";

    } catch (Exception $e) {
        // ... (fin du fichier inchangée)
    }
} 
?>