<?php
// Fichier : controleurs/fonctions.php (CORRIGÉ ET NETTOYÉ)

// On importe les classes de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// On charge la configuration (pour les identifiants SMTP)
require_once(__DIR__ . '/../config.php');
// On charge l'autoloader de Composer pour que "new PHPMailer()" fonctionne
require_once(__DIR__ . '/../vendor/autoload.php');

// PAS DE CONNEXION À LA BDD ICI ! C'EST UN FICHIER DE FONCTIONS.

/**
 * Crypte un mot de passe en utilisant l'algorithme BCRYPT.
 * @param string $password Le mot de passe en clair.
 * @return string Le mot de passe haché.
 */
function crypterMdp(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Nettoie une chaîne de caractères pour éviter les injections XSS.
 * @param mixed $data La donnée à nettoyer.
 * @return mixed La donnée nettoyée.
 */
function nettoyerDonnees($data) {
    if (is_array($data) || is_object($data)) {
        return $data;
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Envoie un email d'alerte en utilisant PHPMailer et les identifiants de config.php.
 * @param string $sujet Le sujet de l'email.
 * @param string $message Le contenu HTML de l'email.
 * @return bool True si l'email a été envoyé, false sinon.
 */
function envoyerAlerteEmail(string $sujet, string $message): bool {
    $mail = new PHPMailer(true);
    try {
        // Paramètres du serveur SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPDebug = 2; 

        // Destinataires
        $mail->setFrom(SMTP_USER, 'Alertes Gusteau\'s');
        $mail->addAddress(SMTP_USER); // S'envoie l'email à soi-même pour le test

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // En cas d'erreur, on l'écrit dans les logs PHP pour le débogage
        error_log("Le message n'a pas pu être envoyé. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}