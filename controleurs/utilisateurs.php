<?php

// on inclut le fichier modèle contenant les appels à la BDD
include('./modele/requetes.utilisateurs.php');

// si la fonction n'est pas définie, on choisit d'afficher l'accueil
if (!isset($_GET['fonction']) || empty($_GET['fonction'])) {
    $function = "login";
} else {
    $function = $_GET['fonction'];
}

switch ($function) {
    
    case 'accueil':
        //affichage de l'accueil
        $vue = "accueil";
        break;
    
    case 'login':
        //affichage de l'inscription
        $vue = "login";
        break;

    case 'inscription':
        //affichage de l'inscription
        $vue = "inscription";

        // Cette partie du code est appelée si le formulaire a été posté
        if (isset($_POST['prenom'], $_POST['nom'], $_POST['password'], $_POST['email'])) {
            $values = [
                'prenom' => $_POST['prenom'],
                'nom' => $_POST['nom'],
                'email' => $_POST['email'],
                'password' => crypterMdp($_POST['password'])
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