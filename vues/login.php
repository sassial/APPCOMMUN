<?php
// vues/connexion.php
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Connexion – Gusteau’s</title>
  <!-- Lien vers le CSS commun (remonte d’un dossier) -->
  <link rel="stylesheet" href="/APPCOMMUN/vues/style.css">
</head>
<body>

  <?php include __DIR__ . '/header.php'; ?>

  <main class="container">
    <section class="signup-card">
      <h1>Se connecter</h1>
      <form action="../controleurs/utilisateurs.php?action=login" method="post" class="signup-form">
        <div class="form-group">
          <label for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-submit">Connexion</button>
      </form>
      <p class="login-link">
        Pas encore de compte ? <a href="index.php?cible=utilisateurs&fonction=inscription">Inscrivez-vous</a>
      </p>
    </section>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
