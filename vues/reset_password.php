<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe – Gusteau’s</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="container">
        <section class="signup-card">
            <h1>Réinitialiser votre mot de passe</h1>
            
            <?php if (!empty($alerte)): ?>
                <div class="alert-message"><?= htmlspecialchars($alerte) ?></div>
            <?php endif; ?>

            <form action="index.php?cible=utilisateurs&fonction=reset_password" method="post" class="signup-form">
                <!-- On passe le jeton dans un champ caché pour ne pas le perdre -->
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" required>
                        <i class="fas fa-lock icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                    <div class="input-with-icon">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                         <i class="fas fa-lock icon"></i>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Réinitialiser</button>
            </form>
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