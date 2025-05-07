<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Archivés &amp; Annulés</title>

  <!-- CSS de la sidebar -->
  <link rel="stylesheet" href="css/sidebar.css">
  <!-- CSS du panneau admin -->
  <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>

  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <div class="main">
    <h1 style="text-align:center;">Archivés &amp; Annulés</h1>

    <?php
      $fmtDate = new IntlDateFormatter(
        'fr_FR',
        IntlDateFormatter::SHORT,
        IntlDateFormatter::NONE
      );
    ?>

    <!-- RÉSERVATIONS ARCHIVÉES -->
    <h2 class="section" style="text-align:center;">Rendez-vous archivés</h2>
    <div style="text-align:center; margin:0.5rem 0;">
      <a
        href="index.php?controller=Admin&action=deleteArchived"
        onclick="return confirm('Supprimer définitivement tous les rendez-vous archivés ?')"
        style="
          display:inline-block;
          background:#c00;
          color:#fff;
          padding:0.5rem 1rem;
          border-radius:4px;
          text-decoration:none;
          margin-right:0.5rem;
        "
      >Supprimer les archivés</a>
      <a
        href="index.php?controller=Admin&action=unarchiveAll"
        onclick="return confirm('Désarchiver tous les rendez-vous archivés ?')"
        style="
          display:inline-block;
          background:var(--primary);
          color:#fff;
          padding:0.5rem 1rem;
          border-radius:4px;
          text-decoration:none;
        "
      >Désarchiver les archivés</a>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Heure</th>
            <th>Sujet</th>
            <th class="action">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($archived)): ?>
          <tr><td colspan="4" style="text-align:center;">Aucun rendez-vous archivé</td></tr>
        <?php else: foreach ($archived as $r):
          $d = $r['date'] ? $fmtDate->format(new DateTime($r['date'])) : '-';
        ?>
          <tr>
            <td><?= htmlspecialchars($d) ?></td>
            <td><?= htmlspecialchars($r['hour']) ?></td>
            <td><?= htmlspecialchars($r['subject']) ?></td>
            <td class="action">
              <button class="toggle-details">Lire plus</button>
              <a
                href="index.php?controller=Admin&action=unarchive&id=<?= $r['id'] ?>"
                onclick="return confirm('Désarchiver ce rendez-vous ?')"
                style="margin-left:0.5rem;color:#28a745;text-decoration:none;"
              >Désarchiver</a>
              <a
                href="index.php?controller=Admin&action=delete&id=<?= $r['id'] ?>"
                onclick="return confirm('Supprimer définitivement ce rendez-vous ?')"
                style="margin-left:0.5rem;color:#c00;text-decoration:none;"
              >Supprimer</a>
            </td>
          </tr>
          <tr class="details-row">
            <td colspan="4">
              <strong>Email :</strong>
                <span class="copyable" data-copy="<?= htmlspecialchars($r['email']) ?>">
                  <?= htmlspecialchars($r['email']) ?>
                </span><br><br>
              <strong>Message :</strong><br>
              <div style="margin:0.5rem 0;">
                <?= nl2br(htmlspecialchars($r['message'])) ?>
              </div>
              <strong>Plateforme :</strong> <?= htmlspecialchars($r['platform']) ?><br>
              <strong>Nom plateforme :</strong>
                <span class="copyable" data-copy="<?= htmlspecialchars($r['platform_name']) ?>">
                  <?= htmlspecialchars($r['platform_name']) ?>
                </span>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- RÉSERVATIONS ANNULÉES -->
    <h2 class="section" style="text-align:center;">Rendez-vous annulés</h2>
    <div style="text-align:center; margin:0.5rem 0;">
      <a
        href="index.php?controller=Admin&action=deleteCancelled"
        onclick="return confirm('Supprimer définitivement tous les rendez-vous annulés ?')"
        style="
          display:inline-block;
          background:#c00;
          color:#fff;
          padding:0.5rem 1rem;
          border-radius:4px;
          text-decoration:none;
        "
      >Supprimer les annulés</a>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Heure</th>
            <th>Sujet</th>
            <th class="action">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($cancelled)): ?>
          <tr><td colspan="4" style="text-align:center;">Aucun rendez-vous annulé</td></tr>
        <?php else: foreach ($cancelled as $r):
          $d = $r['date'] ? $fmtDate->format(new DateTime($r['date'])) : '-';
        ?>
          <tr>
            <td><?= htmlspecialchars($d) ?></td>
            <td><?= htmlspecialchars($r['hour']) ?></td>
            <td><?= htmlspecialchars($r['subject']) ?></td>
            <td class="action">
              <button class="toggle-details">Lire plus</button>
              <a
                href="index.php?controller=Admin&action=delete&id=<?= $r['id'] ?>"
                onclick="return confirm('Supprimer définitivement ce rendez-vous ?')"
                style="margin-left:0.5rem;color:#c00;text-decoration:none;"
              >Supprimer</a>
            </td>
          </tr>
          <tr class="details-row">
            <td colspan="4">
              <strong>Email :</strong>
                <span class="copyable" data-copy="<?= htmlspecialchars($r['email']) ?>">
                  <?= htmlspecialchars($r['email']) ?>
                </span><br><br>
              <strong>Message :</strong><br>
              <div style="margin:0.5rem 0;"><?= nl2br(htmlspecialchars($r['message'])) ?></div>
              <strong>Plateforme :</strong> <?= htmlspecialchars($r['platform']) ?><br>
              <strong>Nom plateforme :</strong>
                <span class="copyable" data-copy="<?= htmlspecialchars($r['platform_name']) ?>">
                  <?= htmlspecialchars($r['platform_name']) ?>
                </span>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="js/admin_dashboard.js"></script>
</body>
</html>
