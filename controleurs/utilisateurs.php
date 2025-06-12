<?php
// Fichier : controleurs/utilisateurs.php

require_once(__DIR__ . '/../modele/requetes.utilisateurs.php');
require_once(__DIR__ . '/../modele/requetes.capteurs.php'); 
require_once(__DIR__ . '/../modele/connexion_commune.php'); 

$function = $_GET['fonction'] ?? 'login';

if (isset($_SESSION['utilisateur']) && ($function === 'login' || $function === 'inscription')) {
    header('Location: index.php?cible=utilisateurs&fonction=accueil');
    exit();
}

switch ($function) {
    case 'accueil':
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: index.php?cible=utilisateurs&fonction=login');
            exit();
        }
        $donneesSonDetaillees = recupererDonneesDetaillees($bdd_commune, 'Capteur Son');
        $vue = "accueil";
        break;

    case 'login':
        // ... (votre code de login reste inchangé, il est bon)
        $vue = "login";
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = nettoyerDonnees($_POST['email']);
            $password = nettoyerDonnees($_POST['password']);
            $utilisateur = rechercheParEmail($bdd, $email);
            if ($utilisateur && password_verify($password, $utilisateur['password'])) {
                $_SESSION['utilisateur'] = [
                    'id' => $utilisateur['id'],
                    'prenom' => $utilisateur['prenom'],
                    'role' => $utilisateur['role']
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

            // NOUVELLE VÉRIFICATION : La longueur du mot de passe
            if (strlen($password) < 6) {
                $alerte = "Le mot de passe doit contenir au moins 6 caractères.";
            
            } elseif ($password !== $confirm_password) {
                $alerte = "Les mots de passe ne correspondent pas.";
            
            } elseif (emailExiste($bdd, $email)) {
                $alerte = "Cette adresse e-mail est déjà utilisée.";

            } else {
                // Tout est bon, on peut créer l'utilisateur
                $values = [
                    'prenom'   => $prenom,
                    'nom'      => $nom,
                    'email'    => $email,
                    'password' => crypterMdp($password)
                ];
                
                $retour = ajouteUtilisateur($bdd, $values);
                
                if ($retour) {
                    $nouvelUtilisateur = rechercheParEmail($bdd, $email);
                    $_SESSION['utilisateur'] = [
                        'id' => $nouvelUtilisateur['id'],
                        'prenom' => $nouvelUtilisateur['prenom'],
                        'role' => 'utilisateur'
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
        session_unset();
        session_destroy();
        header('Location: index.php?cible=utilisateurs&fonction=login');
        exit();
        
    default:
        $vue = "erreur404";
}

require_once(__DIR__ . '/../vues/' . $vue . '.php');