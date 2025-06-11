<?php
// ON RETIRE L'INCLUDE D'ICI. CE FICHIER NE CONTIENT QUE LA FONCTION.

/**
 * Récupère la dernière mesure de son enregistrée depuis la table `Capteur Son`.
 * @param PDO $bdd_commune L'objet de connexion à la BDD commune
 * @return array|null Les données de la mesure ou null si aucune
 */
function recupererDerniereMesure(PDO $bdd_commune): ?array {

    // On utilise les noms de colonnes exacts : `valeur` et `temps`
    $query = 'SELECT `valeur` AS valeur_db, `temps` AS horodatage FROM `Capteur Son` ORDER BY `temps` DESC LIMIT 1';

    try {
        $statement = $bdd_commune->query($query);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;

    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
}
?>