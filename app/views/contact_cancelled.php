<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Annulation confirmée</title>
  <link rel="stylesheet" href="css/contact_form.css">
</head>
<body>
  <div class="main">
    <h1>Réservation annulée</h1>
    <p>
      Votre rendez-vous du <?php 
        $dt = new DateTime($_GET['date'] ?? '');
        echo $dt ? $dt->format('d/m/Y') : ''; 
      ?> 
      à <?= htmlspecialchars($_GET['time'] ?? '') ?> 
      a bien été annulé.
    </p>
    <p><a href="index.php?controller=Contact&action=form">Retour au calendrier</a></p>
  </div>
</body>
</html>
