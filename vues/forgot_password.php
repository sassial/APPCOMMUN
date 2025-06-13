<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié – Gusteau’s</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="container">
        <section class="signup-card">
            <h1>Mot de passe oublié</h1>
            <p>Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
            
            <?php if (!empty($alerte)): ?>
                <div class="alert-message"><?= htmlspecialchars($alerte) ?></div>
            <?php endif; ?>

            <form action="index.php?cible=utilisateurs&fonction=forgot_password" method="post" class="signup-form">
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                   <!-- LIGNE CORRIGÉE -->
<div class="input-wrapper">
                        <input type="email" id="email" name="email" required>
                        <i class="fas fa-envelope icon"></i>
                    </div>
                </div>
                <!-- LIGNE CORRIGÉE -->
<button type="submit" class="btn btn-submit">Envoyer le lien</button>
            </form>
        </section>
    </main>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>