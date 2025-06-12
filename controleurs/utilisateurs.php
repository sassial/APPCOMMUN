<?php
// Fichier : controleurs/utilisateurs.php

require_once(__DIR__ . '/../modele/requetes.utilisateurs.php');
require_once(__DIR__ . '/../modele/requetes.capteurs.php'); // Toujours nécessaire pour la page d'accueil

$function = $_GET['fonction'] ?? 'login';

// Si l'utilisateur est déjà connecté, on ne lui montre pas les pages de login/inscription
if (isset($_SESSION['utilisateur']) && ($function === 'login' || $function === 'inscription')) {
    header('Location: index.php?cible=utilisateurs&fonction=accueil');
    exit();
}

switch ($function) {
    case 'accueil':
        // La page d'accueil est protégée : si personne n'est connecté, on renvoie au login
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: index.php?cible=utilisateurs&fonction=login');
            exit();
        }
        $donneesSonDetaillees = recupererDonneesDetaillees($bdd_commune, 'Capteur Son');
        $vue = "accueil";
        break;

    case 'login':
        $vue = "login";
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = nettoyerDonnees($_POST['email']);
            $password = nettoyerDonnees($_POST['password']);
            
            // On utilise notre BDD locale via $bdd (et non $bdd_commune)
            $utilisateur = rechercheParEmail($bdd, $email);

            if ($utilisateur && password_verify($password, $utilisateur['password'])) {
                // Le mot de passe est correct, on enregistre l'utilisateur en session
                $_SESSION['utilisateur'] = [
                    'id' => $utilisateur['id'],
                    'prenom' => $utilisateur['prenom']
                ];
                header('Location: index.php?cible=utilisateurs&fonction=accueil');
                exit();
            } else {
                $alerte = "Adresse e-mail ou mot de passe incorrect.";
            }
        }
        break;

    case 'inscription':
        $vue = "inscription";
        if (!empty($_POST)) {
            $prenom = nettoyerDonnees($_POST['prenom'] ?? '');
            $nom = nettoyerDonnees($_POST['nom'] ?? '');
            $email = nettoyerDonnees($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if ($password !== $confirm_password) {
                $alerte = "Les mots de passe ne correspondent pas.";
            } elseif (emailExiste($bdd, $email)) {
                $alerte = "Cette adresse e-mail est déjà utilisée.";
            } else {
                // Tout est bon, on peut créer l'utilisateur
                $values = [
                    'prenom'   => $prenom,
                    'nom'      => $nom,
                    'email'    => $email,
                    'password' => crypterMdp($password) // crypterMdp utilise password_hash
                ];
                
                $retour = ajouteUtilisateur($bdd, $values);
                
                if ($retour) {
                    // On connecte directement l'utilisateur après son inscription
                    $nouvelUtilisateur = rechercheParEmail($bdd, $email);
                    $_SESSION['utilisateur'] = [
                        'id' => $nouvelUtilisateur['id'],
                        'prenom' => $nouvelUtilisateur['prenom']
                    ];
                    header('Location: index.php?cible=utilisateurs&fonction=accueil');
                    exit();
                } else {
                    $alerte = "Une erreur est survenue. L'inscription a échoué.";
                }
            }
        }
        break;

    case 'logout':
        // **NOUVELLE FONCTION DE DÉCONNEXION**
        session_unset();  // Supprime toutes les variables de session
        session_destroy(); // Détruit la session
        header('Location: index.php?cible=utilisateurs&fonction=login');
        exit();
        
    default:
        $vue = "erreur404";
}

require_once(__DIR__ . '/../vues/' . $vue . '.php');