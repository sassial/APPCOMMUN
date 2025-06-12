<?php
// Fichier : controleurs/utilisateurs.php

require_once(__DIR__ . '/../modele/requetes.utilisateurs.php');
require_once(__DIR__ . '/../modele/requetes.capteurs.php');
require_once(__DIR__ . '/../modele/connexion_commune.php'); 

if (!isset($_GET['fonction']) || empty($_GET['fonction'])) {
    $function = "login";
} else {
    $function = $_GET['fonction'];
}

switch ($function) {
    case 'accueil':
        // Ceci va maintenant fonctionner car la fonction existe
        $donneesSonDetaillees = recupererDonneesDetaillees($bdd_commune, 'Capteur Son');
        $vue = "accueil";
        break;

    case 'login':
        $vue = "login";
        if (isset($_POST['email'], $_POST['password'])) {
            $email = nettoyerDonnees($_POST['email']);
            $password = nettoyerDonnees($_POST['password']);
            $utilisateur = rechercheParEmail($bdd, $email);

            if ($utilisateur && password_verify($password, $utilisateur['password'])) {
                $_SESSION['utilisateur'] = [
                    'id' => $utilisateur['id'],
                    'prenom' => $utilisateur['prenom'],
                    'nom' => $utilisateur['nom'],
                    'email' => $utilisateur['email']
                ];
                // CORRECTION: Redirection vers l'accueil après connexion
                header('Location: index.php?cible=utilisateurs&fonction=accueil');
                exit();
            } else {
                $alerte = "Adresse e-mail ou mot de passe incorrect.";
            }
        }
        break;

    case 'inscription':
        $vue = "inscription";
        if (isset($_POST['prenom'], $_POST['nom'], $_POST['password'], $_POST['email'])) {
            $values = [
                'prenom'   => nettoyerDonnees($_POST['prenom']),
                'nom'      => nettoyerDonnees($_POST['nom']),
                'email'    => nettoyerDonnees($_POST['email']),
                'password' => crypterMdp($_POST['password'])
            ];
            $retour = ajouteUtilisateur($bdd, $values);
            $alerte = $retour ? "Inscription réussie" : "L'inscription a échoué.";
        }
        break;
        
    default:
        $vue = "erreur404";
}

require_once(__DIR__ . '/../vues/' . $vue . '.php');