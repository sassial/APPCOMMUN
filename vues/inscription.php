<?php
// vues/inscription.php
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Inscription – Gusteau’s</title>
    <link rel="stylesheet" href="/APPCOMMUN/vues/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <?php include __DIR__ . '/header.php'; ?>
    <div class="background">
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
                        <input type="password" id="password" name="password" placeholder="+ 12 caractères" required>
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
    </div>
    <?php include __DIR__ . '/footer.php'; ?>

    <script>
        const form = document.querySelector('.signup-form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const errorDiv = document.getElementById('password-errors');

        form.addEventListener('submit', function(event) {
            const value = password.value;
            const confirm = confirmPassword.value;
            const errors = [];

            if (value.length < 12) {
                errors.push("Le mot de passe doit contenir au moins 12 caractères.");
            }
            if (!/[A-Z]/.test(value)) {
                errors.push("Le mot de passe doit contenir au moins une majuscule.");
            }
            if (!/[a-z]/.test(value)) {
                errors.push("Le mot de passe doit contenir au moins une minuscule.");
            }
            if (value !== confirm) {
                errors.push("Les mots de passe ne correspondent pas.");
            }

            if (errors.length > 0) {
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = errors.map(e => `<p>${e}</p>`).join('');
                event.preventDefault();
            } else {
                errorDiv.style.display = 'none';
            }
        });
    </script>

</body>
</html>