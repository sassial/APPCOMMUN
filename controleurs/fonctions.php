<?php
// Fichier : controleurs/fonctions.php (CORRIGÉ ET FINALISÉ)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../vendor/autoload.php');

function crypterMdp(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT);
}

function nettoyerDonnees($data) {
    if (is_array($data) || is_object($data)) {
        return $data;
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function envoyerAlerteEmail(string $sujet, string $message): bool {
    $mail = new PHPMailer(true);
    try {
        
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_USER, 'Alertes Gusteau\'s');
        $mail->addAddress(SMTP_USER);

        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Alerte Email Erreur: {$mail->ErrorInfo}");
        return false;
    }
}

function genererTokenReset(string $email): string {
    $expiration = time() + (15 * 60);
    $payload = base64_encode(json_encode(['email' => $email, 'exp' => $expiration]));
    $signature = hash_hmac('sha256', $payload, SECRET_KEY);
    return $payload . '.' . $signature;
}

function verifierTokenReset(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 2) { return null; }

    $payload = $parts[0];
    $signature_recue = $parts[1];
    $signature_attendue = hash_hmac('sha256', $payload, SECRET_KEY);

    if (!hash_equals($signature_attendue, $signature_recue)) { return null; }

    $data = json_decode(base64_decode($payload), true);
    if ($data['exp'] < time()) { return null; }

    return $data;
}

function envoyerEmailReset(string $email_destinataire, string $token): bool {
    $mail = new PHPMailer(true);
   // LIGNE CORRIGÉE
// DANS : controleurs/fonctions.php

// LA MEILLEURE SOLUTION (plus robuste)
$host = $_SERVER['HTTP_HOST']; // Récupère 'localhost' ou votre domaine
$base_path = BASE_PATH;       // Utilise la constante globale que nous avons déjà corrigée
$reset_link = "http://{$host}{$base_path}/index.php?cible=utilisateurs&fonction=reset_password&token=" . urlencode($token);

    try {
        // --- Activation du mode débogage pour trouver le problème ---
        // $mail->SMTPDebug = 2; // Dé-commentez cette ligne si les emails ne partent toujours pas

        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_USER, 'Support Gusteau\'s');
        $mail->addAddress($email_destinataire);

        $mail->isHTML(true);
        $mail->Subject = 'Réinitialisation de votre mot de passe';
        $mail->Body    = "<h1>Réinitialisation de Mot de Passe</h1>
                          <p>Bonjour,</p>
                          <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous (valide 15 minutes) :</p>
                          <p><a href='{$reset_link}'>{$reset_link}</a></p>
                          <p>Si vous n'avez pas fait cette demande, veuillez ignorer cet email.</p>";
        $mail->AltBody = "Pour réinitialiser votre mot de passe, copiez-collez ce lien dans votre navigateur : {$reset_link}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Reset Erreur: {$mail->ErrorInfo}");
        return false;
    }
} // <--- C'EST CETTE ACCOLADE QUI MANQUAIT !