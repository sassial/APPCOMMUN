<?php
// Fichier : controleurs/utilisateurs.php (VERSION FINALE ET CORRIGÉE)

require_once __DIR__ . '/../models/requetes.utilisateurs.php';
require_once __DIR__ . '/../models/requetes.capteurs.php' ;

$function = $_GET['fonction'] ?? 'login';

// Si un utilisateur est déjà connecté, on le redirige vers l'accueil au lieu de lui montrer les pages de connexion ou d'inscription.
if (isset($_SESSION['utilisateur']) && ($function === 'login' || $function === 'inscription')) {
    header('Location: index.php?cible=capteurs&fonction=accueil');
    exit();
}

switch ($function) {
    case 'login':
        $vue = "utilisateurs/login";
        // On vérifie que les champs ne sont pas vides
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            
            // On ne nettoie QUE l'email pour la recherche.
            $email = nettoyerDonnees($_POST['email']);
            
            // On garde le mot de passe TEL QUEL, brut de formulaire.
            $password_saisi = $_POST['password'];
            
            // On recherche l'utilisateur dans la base de données.
            $utilisateur = rechercheParEmail($bdd, $email);

            // La vérification la plus importante :
            // 1. Est-ce que l'utilisateur a été trouvé ($utilisateur n'est pas false) ?
            // 2. Est-ce que le mot de passe saisi, une fois haché, correspond au hash stocké ?
            if ($utilisateur && password_verify($password_saisi, $utilisateur['password'])) {
                
                // Si les deux sont vrais, la connexion est un succès.
                $_SESSION['utilisateur'] = [
                    'id' => $utilisateur['id'],
                    'prenom' => $utilisateur['prenom'],
                    'role' => $utilisateur['role']
                ];
                header('Location: index.php?cible=capteurs&fonction=accueil');
                exit();

            } else {
                // Si l'une des deux conditions est fausse, c'est un échec.
                $alerte = "Adresse e-mail ou mot de passe incorrect.";
            }
        }
        break;

    
    case 'inscription':
    $vue = "utilisateurs/inscription";
    if (!empty($_POST)) {
        $prenom = nettoyerDonnees($_POST['prenom'] ?? '');
        $nom = nettoyerDonnees($_POST['nom'] ?? '');
        $email = nettoyerDonnees($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $passwordValid = strlen($password) >= 8 &&
                         preg_match('/[A-Z]/', $password) && 
                         preg_match('/[a-z]/', $password) && 
                         preg_match('/\d/', $password) && 
                         preg_match('/[\W_]/', $password);
        if (!$passwordValid) {
            $alerte = "Le mot de passe doit contenir au moins 8 caractères, avec une majuscule, une minuscule, un chiffre et un caractère spécial.";
        } elseif ($password !== $confirm_password) {
            $alerte = "Les mots de passe ne correspondent pas.";
        } elseif (emailExiste($bdd, $email)) {
            $alerte = "Cette adresse e-mail est déjà utilisée.";
        } else {
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
                header('Location: index.php?cible=capteurs&fonction=accueil');
                exit();
            } else {
                $alerte = "Une erreur est survenue. L'inscription a échoué.";
            }
        }
    }
    break;

    case 'forgot_password':
        $vue = "utilisateurs/mdpoublie";
        if (!empty($_POST['email'])) {
            $email = nettoyerDonnees($_POST['email']);
            if (emailExiste($bdd, $email)) {
                $token = genererTokenReset($email);
                envoyerEmailReset($email, $token);
            }
            $alerte = "Si un compte est associé à cette adresse, un lien de réinitialisation a été envoyé.";
        }
        break;

    case 'reset_password':
        if (!empty($_POST['token']) && !empty($_POST['password'])) {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $data_token = verifierTokenReset($token);

            if (!$data_token) {
                $alerte = "Le lien de réinitialisation est invalide ou a expiré.";
                $vue = "utilisateurs/mdpoublie";
            } elseif (strlen($password) < 6) {
                $alerte = "Le mot de passe doit faire au moins 6 caractères.";
                $vue = "utilisateurs/mdpreinitialise";
            } elseif ($password !== $confirm_password) {
                $alerte = "Les mots de passe ne correspondent pas.";
                $vue = "utilisateurs/mdpreinitialise";
            } else {
                $email = $data_token['email'];
                mettreAJourMotDePasse($bdd, $email, crypterMdp($password));
               
                $alerte = "Votre mot de passe a été réinitialisé avec succès !";
                $vue = "utilisateurs/login";
            }
        } elseif (!empty($_GET['token'])) {
            $token = $_GET['token'];
            $data_token = verifierTokenReset($token);
            if ($data_token) {
                $vue = "utilisateurs/mdpreinitialise";
            } else {
                $alerte = "Le lien de réinitialisation est invalide ou a expiré.";
                $vue = "utilisateurs/mdpoublie";
            }
        } else {
            header('Location: index.php?cible=utilisateurs&fonction=login');
            exit();
        }
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header('Location: index.php?cible=utilisateurs&fonction=login');
        exit();
        
    default:
        $vue = "erreur404";
        break;
}

require_once __DIR__ . '/../views/' . $vue . '.php';