<?php
// vues/inscription.php
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Inscription – Gusteau’s</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <?php include __DIR__ . '/header.php'; ?>

    <main class="container">
        <section class="signup-card card">
            <h1>Créer votre compte</h1>

            <?php if (!empty($alerte)): ?>
                <div class="alert-message"><?= htmlspecialchars($alerte) ?></div>
            <?php endif; ?>

            <form action="index.php?cible=utilisateurs&fonction=inscription" method="post" class="signup-form">

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <div class="input-wrapper">
                        <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required>
                        <i class="fas fa-user icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <div class="input-wrapper">
                        <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
                        <i class="fas fa-user icon"></i>
                    </div>
                </div>

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
                        <input type="password" id="password" name="password" placeholder="6+ caractères" required>
                        <i class="fas fa-lock icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmez" required>
                        <i class="fas fa-lock icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit">S’inscrire</button>
            </form>
            
            <p class="login-link">Vous avez déjà un compte ? <a href="index.php?cible=utilisateurs&fonction=login">Connectez-vous</a></p>
        </section>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
        const form = document.querySelector('.signup-form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        form.addEventListener('submit', function(event) {
            if (password.value.length < 6) {
                event.preventDefault(); 
                alert("Le mot de passe doit contenir au moins 6 caractères !");
            } else if (password.value !== confirmPassword.value) {
                event.preventDefault(); 
                alert("Les mots de passe ne correspondent pas !");
            }
        });
    </script>

</body>
</html>