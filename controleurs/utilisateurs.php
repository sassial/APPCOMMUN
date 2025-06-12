<?php
// Fichier : controleurs/utilisateurs.php

// 1) BDD UTILISATEURS (locale)
require_once __DIR__ . '/../modele/connexion.php';
// 2) Requêtes utilisteurs
require_once __DIR__ . '/../modele/requetes.utilisateurs.php';
// 3) BDD CAPTEURS (commune)
require_once __DIR__ . '/../modele/connexion_commune.php';
// 4) Requêtes capteurs (pour recupererDonneesDetaillees)
require_once __DIR__ . '/../modele/requetes.capteurs.php';

$function = $_GET['fonction'] ?? 'login';

// si déjà connecté, on ne ré-affiche pas login/inscription
if (isset($_SESSION['utilisateur']) && in_array($function, ['login','inscription'])) {
    header('Location: index.php?cible=utilisateurs&fonction=accueil');
    exit();
}

switch ($function) {
    case 'accueil':
        // protégé
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: index.php?cible=utilisateurs&fonction=login');
            exit();
        }
        // affiche le tableau détaillé du capteur SON
        $donneesSonDetaillees = recupererDonneesDetaillees(
          $bdd_commune,
          'CapteurSon' // ou le nom exact de votre table son
        );
        $vue = 'accueil';
        break;

    case 'login':
        $vue = 'login';
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email    = nettoyerDonnees($_POST['email']);
            $password = nettoyerDonnees($_POST['password']);
            $user     = rechercheParEmail($bdd, $email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['utilisateur'] = [
                  'id'     => $user['id'],
                  'prenom' => $user['prenom']
                ];
                header('Location: index.php?cible=utilisateurs&fonction=accueil');
                exit();
            } else {
                $alerte = "Adresse e-mail ou mot de passe incorrect.";
            }
        }
        break;

    case 'inscription':
        $vue = 'inscription';
        // … votre logique d’inscription existante …
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header('Location: index.php?cible=utilisateurs&fonction=login');
        exit();

    default:
        $vue = 'erreur404';
}

require_once __DIR__ . '/../vues/' . $vue . '.php';
