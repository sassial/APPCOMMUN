<?php
// vues/login.php (VERSION CORRIGÉE AVEC LE LIEN)
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Connexion – Gusteau’s</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
    <!-- Lien vers Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <?php include __DIR__ . '/header.php'; ?>

    <main class="container">
        <section class="signup-card">
            <h1>Se connecter</h1>

            <?php if (isset($alerte)): ?>
                <div class="alert-message"><?= htmlspecialchars($alerte) ?></div>
            <?php endif; ?>

            <form action="index.php?cible=utilisateurs&fonction=login" method="post" class="signup-form">
                
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <div class="input-with-icon">
                        <input type="email" id="email" name="email" required>
                        <i class="fas fa-envelope icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" required>
                        <i class="fas fa-lock icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Connexion</button>
            </form>

            <!-- ============================================= -->
            <!--     LIGNE AJOUTÉE POUR LE MOT DE PASSE OUBLIÉ   -->
            <!-- ============================================= -->
            <p class="login-link">
                <a href="index.php?cible=utilisateurs&fonction=forgot_password">Mot de passe oublié ?</a>
            </p>
            
            <p class="login-link">
                Pas encore de compte ?
                <a href="index.php?cible=utilisateurs&fonction=inscription">Inscrivez-vous</a>
            </p>
        </section>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>