<?php

// on inclut le fichier modèle contenant les appels à la BDD LOCALE
include('./modele/requetes.utilisateurs.php');

// si la fonction n'est pas définie, on choisit d'afficher l'accueil
if (!isset($_GET['fonction']) || empty($_GET['fonction'])) {
    $function = "login";
} else {
    $function = $_GET['fonction'];
}

switch ($function) {
    
        case 'accueil':
        // ================================================================
        //      NOUVELLE LOGIQUE POUR LA PAGE D'ACCUEIL
        // ================================================================
        // 1. Inclure la connexion et les requêtes pour la BDD COMMUNE
        include_once('./modele/connexion_commune.php');
        include_once('./modele/requetes.capteurs.php');

        // 2. Récupérer les données pour le dashboard personnel
        $donneesSonDetaillees = recupererDonneesDetaillees($bdd_commune, 'Capteur Son');
        
        // 3. Définir la vue à afficher
        $vue = "accueil";
        break;

    
    case 'login':
        $vue = "login";

        // Traitement du formulaire de connexion
        if (isset($_POST['email'], $_POST['password'])) {
            $email = nettoyerDonnees($_POST['email']);
            $password = nettoyerDonnees($_POST['password']);

            // Recherche de l'utilisateur dans la BDD
            $utilisateur = rechercheParEmail($bdd, $email);

            if ($utilisateur && password_verify($password, $utilisateur['password'])) {
                $_SESSION['utilisateur'] = [
                    'id' => $utilisateur['id'],
                    'prenom' => $utilisateur['prenom'],
                    'nom' => $utilisateur['nom'],
                    'email' => $utilisateur['email']
                ];
                $alerte = "Connexion réussie. Bienvenue " . $utilisateur['prenom'] . " !";
                $vue = "accueil"; // Redirection vers accueil
            } else {
                $alerte = "Adresse e-mail ou mot de passe incorrect.";
            }
        }
        break;

    case 'inscription':
        //affichage de l'inscription
        $vue = "inscription";

        // Cette partie du code est appelée si le formulaire a été posté
        if (isset($_POST['prenom'], $_POST['nom'], $_POST['password'], $_POST['email'])) {
            $values = [
                'prenom'   => nettoyerDonnees($_POST['prenom']),
                'nom'      => nettoyerDonnees($_POST['nom']),
                'email'    => nettoyerDonnees($_POST['email']),
                'password' => nettoyerDonnees(crypterMdp($_POST['password']))
            ];

            // Appel à la BDD à travers une fonction du modèle.
            $retour = ajouteUtilisateur($bdd, $values);
            if ($retour) {
                $alerte = "Inscription réussie";
            } else {
                $alerte = "L'inscription dans la BDD n'a pas fonctionné";
            }
            }
        break;
        
    default:
        // si aucune fonction ne correspond au paramètre function passé en GET
        $vue = "erreur404";
}

include ('vues/' . $vue . '.php');