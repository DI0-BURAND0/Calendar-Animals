<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Confirmation</title>
  <link rel="stylesheet" href="css/contact_form.css">
  <style>
    /* Conteneur central */
    .confirmation-container {
      max-width: 500px;
      margin: 2rem auto;
      padding: 1.5rem;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      font-family: Arial, sans-serif;
      color: #333;
    }
    /* Titre */
    .confirmation-container h1 {
      font-size: 1.8rem;
      margin-bottom: 1rem;
      text-align: center;
      color: #0dff00;
    }
    /* Paragraphes */
    .confirmation-container p {
      margin: 0.75rem 0;
      line-height: 1.4;
    }
    .confirmation-container strong {
      color: #000;
    }
    /* Bouton de retour */
    .confirmation-container .btn-home {
      display: inline-block;
      margin: 1.5rem auto 0;
      padding: 0.75rem 1.5rem;
      background: #0dff00;
      color: #fff !important;
      text-decoration: none;
      border-radius: 4px;
      text-align: center;
      font-weight: bold;
    }
    .confirmation-container .btn-home:hover {
      background: rgb(8, 161, 0);
    }
    /* Centrage */
    .center {
      text-align: center;
    }
  </style>
</head>
<?php
  // formater la date en FR
  $dateRaw = $_POST['day'] ?? '';
  try {
    $dt     = new DateTime($dateRaw);
    $dateFr = $dt->format('d/m/Y');
  } catch (Exception $e) {
    $dateFr = htmlspecialchars($dateRaw);
  }
?>
<body>
  <div class="confirmation-container">
    <h1>Merci, votre réservation est confirmée !</h1>
    <p class="center">
      Vous avez choisi le 
      <strong><?= $dateFr ?></strong> 
      à 
      <strong><?= htmlspecialchars($_POST['time']) ?></strong>.
    </p>
    <p>
      <strong>Plateforme :</strong> <?= htmlspecialchars($_POST['platform']) ?>
    </p>
    <p>
      <strong>Nom / Téléphone :</strong> <?= htmlspecialchars($_POST['platform_name']) ?>
    </p>
    <p>
      <strong>Votre message :</strong><br>
      <?= nl2br(htmlspecialchars($_POST['message'])) ?>
    </p>
    <div class="center">
      <a href="index.php" class="btn-home">Retour à l’accueil</a>
    </div>
  </div>
</body>
</html>
