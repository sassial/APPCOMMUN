<?php
// vues/inscription.php
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Inscription – Gusteau’s</title>
  <!-- Lien vers le CSS commun -->
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include __DIR__ . '/header.php'; ?>

  <main class="container">
    <section class="signup-card">
      <h1>Créer votre compte</h1>
      <form action="../controleurs/utilisateurs.php?action=register" method="post" class="signup-form">
        <div class="form-group">
          <label for="fullname">Nom complet</label>
          <input type="text" id="fullname" name="fullname" required>
        </div>
        <div class="form-group">
          <label for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="phone">Téléphone</label>
          <input type="tel" id="phone" name="phone">
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
<<<<<<< HEAD
        Vous avez déjà un compte ? <a href="connexion.php">Connectez-vous</a>
=======
        Vous avez déjà un compte ? <a href="index.php?cible=utilisateurs&fonction=login">Connectez-vous</a>
>>>>>>> c31e38e3cfc098eb8ebbbbc0d2b288f9a2f36ef1
      </p>
    </section>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
