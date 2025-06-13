<?php

require_once __DIR__ . '/../modele/connexion.php';
require_once __DIR__ . '/../modele/requetes.utilisateurs.php';
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
        session_unset();
        session_destroy();
        header('Location: index.php?cible=utilisateurs&fonction=login');
        exit();

    default:
        $vue = 'erreur404';
}

require_once __DIR__ . '/../vues/' . $vue . '.php';
