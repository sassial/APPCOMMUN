<?php
// vues/inscription.php
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Inscription – Gusteau’s</title>
  <!-- Lien vers le CSS commun -->
  <link rel="stylesheet" href="/APPCOMMUN/vues/style.css">
</head>
<body>

  <?php include __DIR__ . '/header.php'; ?>

  <main class="container">
    <section class="signup-card">
      <h1>Créer votre compte</h1>

      <?php if (!empty($alerte)): ?>
        <div class="alert-message"><?= htmlspecialchars($alerte) ?></div>
      <?php endif; ?>

      <form action="index.php?cible=utilisateurs&fonction=inscription" method="post" class="signup-form">

        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" required>
        </div>

        <div class="form-group">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom" required>
        </div>

        <div class="form-group">
          <label for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirmer le mot de passe</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn-submit">S’inscrire</button>
      </form>
      
      <p class="login-link">
        Vous avez déjà un compte ? <a href="index.php?cible=utilisateurs&fonction=login">Connectez-vous</a>
      </p>
    </section>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
