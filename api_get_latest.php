<?php
// Fichier : api_get_latest.php (Maintenant aussi le gardien des alertes)

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

// INCLUSIONS NÉCESSAIRES
require_once(__DIR__ . '/modele/connexion.php'); // BDD locale (pour les seuils)
require_once(__DIR__ . '/modele/connexion_commune.php'); // BDD distante (pour les données)
require_once(__DIR__ . '/modele/requetes.capteurs.php');
require_once(__DIR__ . '/modele/requetes.gestion.php');
require_once(__DIR__ . '/controleurs/fonctions.php'); // Pour envoyer les e-mails

// 1. Récupérer TOUS les capteurs et leurs configurations (seuils, etc.)
$tous_les_capteurs = listerTousLesCapteurs($bdd);

$donnees_a_envoyer = [];
$dispositifs_actifs = listerDispositifsParEtat($bdd, 'capteur', true);
$ids_actifs = array_column($dispositifs_actifs, 'id');

foreach ($tous_les_capteurs as $capteur) {
    $id_capteur = $capteur['id'];
    $nom_table = $capteur['nom_table_bdd'];
    
    // 2. Récupérer la dernière donnée pour chaque capteur
    $derniere_donnee = null;
    if ($nom_table === 'capteur_temp_hum') {
        $data = recupererDonneesTempHum($bdd_commune);
        $derniere_donnee = $data['latest'];
        $valeur_actuelle = $derniere_donnee['temperature'] ?? null;
    } else {
        $data = recupererDonneesCapteur($bdd_commune, $nom_table);
        $derniere_donnee = $data['latest'];
        $valeur_actuelle = $derniere_donnee['valeur'] ?? null;
    }

    // 3. Si le capteur est actif, on ajoute ses données pour le front-end
    if (in_array($id_capteur, $ids_actifs)) {
        $donnees_a_envoyer[$nom_table] = $derniere_donnee;
    }
    
    // 4. LOGIQUE D'ALERTE : On vérifie si un seuil est défini ET dépassé
    $seuil = $capteur['seuil'];
    if ($seuil !== null && $valeur_actuelle !== null) {
        $type_alerte = $capteur['type_alerte'] ?? 'superieur';
        
        $alerte_declenchee = false;
        if ($type_alerte === 'superieur' && $valeur_actuelle > $seuil) {
            $alerte_declenchee = true;
        } elseif ($type_alerte === 'inferieur' && $valeur_actuelle < $seuil) {
            $alerte_declenchee = true;
        }

        if ($alerte_declenchee) {
            // Pour éviter d'envoyer un email toutes les 5 secondes (spam), on vérifie quand la dernière alerte a été envoyée
            if (verifierDelaiAlerte($id_capteur)) {
                // Préparation du message
                $sujet = "Alerte : " . $capteur['nom'];
                $message = "Le capteur '" . htmlspecialchars($capteur['nom']) . "' a dépassé le seuil. <br>Valeur détectée : <strong>" . $valeur_actuelle . "</strong><br>Seuil configuré : <strong>" . $seuil . "</strong>";
                
                envoyerAlerteEmail($sujet, $message);
                
                // On enregistre le moment de l'envoi pour ne pas spammer
                enregistrerTimestampAlerte($id_capteur);
            }
        }
    }
}

// On envoie les données pour les capteurs actifs au front-end
echo json_encode($donnees_a_envoyer);


// --- NOUVELLES FONCTIONS UTILITAIRES À LA FIN DU FICHIER ---

/**
 * Vérifie si un délai minimum (ex: 5 minutes) s'est écoulé depuis la dernière alerte pour ce capteur.
 * @return bool True si on peut envoyer une nouvelle alerte.
 */
function verifierDelaiAlerte(int $id_capteur): bool {
    if (!isset($_SESSION['derniere_alerte'][$id_capteur])) {
        return true; // Aucune alerte précédente dans cette session
    }
    $delai_minimum = 5 * 60; // 5 minutes en secondes
    $temps_ecoule = time() - $_SESSION['derniere_alerte'][$id_capteur];
    
    return $temps_ecoule > $delai_minimum;
}

/**
 * Enregistre le moment où une alerte a été envoyée dans la session.
 */
function enregistrerTimestampAlerte(int $id_capteur): void {
    if (!isset($_SESSION['derniere_alerte'])) {
        $_SESSION['derniere_alerte'] = [];
    }
    $_SESSION['derniere_alerte'][$id_capteur] = time();
}