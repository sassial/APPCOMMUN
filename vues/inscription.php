<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inscription – Restaurant Gusteau’s</title>
  <link rel="stylesheet" href="/APPCOMMUN/vues/style.css"/>
</head>
<body>
  <header class="site-header">
    <img src="/APPCOMMUN/logo.jpg" alt="Logo Gusteau’s" class="logo"/>
  </header>

  <main class="container">
    <section class="signup-card">
      <h1>Créer votre compte</h1>
      <form action="/inscription" method="post" class="signup-form">
        <div class="form-group">
          <label for="fullname">Nom complet</label>
          <input type="text" id="fullname" name="fullname" required />
        </div>
        <div class="form-group">
          <label for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" required />
        </div>
        <div class="form-group">
          <label for="phone">Téléphone</label>
          <input type="tel" id="phone" name="phone" />
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required />
        </div>
        <div class="form-group">
          <label for="confirm-password">Confirmer le mot de passe</label>
          <input type="password" id="confirm-password" name="confirm_password" required />
        </div>
        <button type="submit" class="btn-submit">S’inscrire</button>
      </form>
      <p class="login-link">
        Vous avez déjà un compte ? <a href="/connexion">Connectez-vous</a>
      </p>
    </section>
  </main>

  <footer class="site-footer">
    <p>© 2025 Gusteau’s – Tous droits réservés</p>
  </footer>
</body>
</html>
