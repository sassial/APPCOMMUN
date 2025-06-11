<!-- htdocs/gusteaus/login.html -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion – Gusteau’s</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <header class="site-header">
    <img src="../logo.jpg" alt="Logo Gusteau’s" class="logo"/>
  </header>

  <main class="container">
    <section class="signup-card">
      <h1>Se connecter</h1>
      <form action="login.php" method="post" class="signup-form">
        <div class="form-group">
          <label for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" required />
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required />
        </div>
        <button type="submit" class="btn-submit">Connexion</button>
      </form>
      <p class="login-link">
        Pas encore de compte ? <a href="index.html">Inscrivez-vous</a>
      </p>
    </section>
  </main>

  <footer class="site-footer">
    <p>© 2025 Gusteau’s – Tous droits réservés</p>
  </footer>
</body>
</html>
