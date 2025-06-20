<?php
// vues/login.php
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Connexion – Gusteau’s</title>
    <link rel="stylesheet" href="/APPCOMMUN/public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <?php include __DIR__ . '/../layouts/header.php'; ?>
    <div class="background">
    <main class="container">
        <section class="signup-card card">
            <h1>Se connecter</h1>

            <?php if (isset($alerte)): ?>
                <div class="alert-message"><?= htmlspecialchars($alerte) ?></div>
            <?php endif; ?>

            <form action="index.php?cible=utilisateurs&fonction=login" method="post" class="signup-form">
                
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="exemple@domaine.com" required>
                        <i class="fas fa-envelope icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
                        <i class="fas fa-lock icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit">Connexion</button>
            </form>

            <p class="login-link"><a href="index.php?cible=utilisateurs&fonction=forgot_password">Mot de passe oublié ?</a></p>
            <p class="login-link">Pas encore de compte ? <a href="index.php?cible=utilisateurs&fonction=inscription">Inscrivez-vous</a></p>
        </section>
    </main>
    </div>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

</body>
</html>