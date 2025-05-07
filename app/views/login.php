<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin – Connexion</title>
    <style>
      body { font-family: sans-serif; padding: 2rem; }
      form { max-width: 300px; margin: auto; }
      label, input { display: block; width: 100%; }
      input { margin-bottom: 1rem; padding: .5rem; }
      .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <h1>Connexion Admin</h1>
    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form action="index.php?controller=Admin&action=login" method="POST">
        <label for="username">Utilisateur</label>
        <input type="text" name="username" id="username" required autofocus>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
