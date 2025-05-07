<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Tableau de bord</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/admin_dashboard.css">
  <style>
    :root {
      --primary: #4a90e2;
      --stripe: #f9f9f9;
      --text: #333;
    }
    .main h1, .main h2 { text-align: center; }

    /* Calendrier RDV */
    .appt-calendar-nav { text-align:center; margin-bottom:1rem; }
    .appt-calendar {
      display:grid;
      grid-template-columns:repeat(7,1fr);
      gap:2px;
      text-align:center;
      margin-bottom:1.5rem;
    }
    .appt-calendar-day {
      position:relative;
      padding:8px;
      border:1px solid #ccc;
      min-height:40px;
    }
    .appt-calendar-header {
      font-weight:bold;
      background: #fbe17b;
    }
    .has-appt {
      background:#d4fcd4;
      cursor:pointer;
    }
    .no-appt {
      background:#ffe0e0;
      color:#999;
    }
    .appt-calendar-day .badge {
      position:absolute;
      top:4px;
      right:4px;
      background:#4a90e2;
      color:#fff;
      border-radius:50%;
      padding:2px 6px;
      font-size:0.75rem;
    }

    /* Modal jour */
    .modal {
      display:none;
      position:fixed;
      top:10%; left:50%;
      transform:translateX(-50%);
      width:90%; max-width:600px;
      background:#fff;
      padding:1rem;
      border:1px solid #ccc;
      box-shadow:0 0 10px rgba(0,0,0,.5);
      z-index:1000;
    }
    .overlay {
      display:none;
      position:fixed;
      top:0;left:0;right:0;bottom:0;
      background:rgba(0,0,0,0.4);
      z-index:900;
    }
    .modal-header {
      display:flex;
      justify-content:space-between;
      align-items:center;
    }
    .modal-header h2 {
      flex:1;
      text-align:center;
      margin:0;
    }
    .modal-close {
      cursor:pointer;
      font-size:1.2rem;
    }

    .appt-list {
      list-style:none;
      padding:0;
      margin:1rem 0 0;
      max-height:400px;
      overflow-y:auto;
      text-align:center;
    }
    .appt-item {
      border-bottom:1px solid #ddd;
      padding:.5rem 0;
    }
    .appt-item h4 {
      margin:0;
      font-size:1rem;
      display:inline-block;
    }
    .details {
      display:none;
      margin-top:.5rem;
      padding-left:1rem;
    }
    .toggle-detail {
      margin-left:1rem;
      font-size:.9rem;
      color:var(--primary);
      cursor:pointer;
      background:none;
      border:none;
    }
  </style>
</head>
<body>

  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <div class="main">
    <h1>Tableau de bord</h1>

    <?php
      // navigation mois
      $month = (int)($_GET['month'] ?? date('m'));
      $year  = (int)($_GET['year']  ?? date('Y'));
      $current = (new DateTime())->setDate($year, $month, 1);
      $prev    = (clone $current)->modify('-1 month');
      $next    = (clone $current)->modify('+1 month');
      $fmtNav = new IntlDateFormatter('fr_FR', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'MMMM yyyy');

      // extraction des dates de RDV à venir
      $dates = array_map(fn($r) => substr($r['date'],0,10), $upcoming);
      $counts = array_count_values($dates);
      $unique = array_unique($dates);
      $calendarDays = array_filter($unique, fn($d) => DateTime::createFromFormat('Y-m-d',$d)
        && (int)(new DateTime($d))->format('m') === $month
        && (int)(new DateTime($d))->format('Y') === $year
      );

      $jours   = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
      $fmtDate = new IntlDateFormatter('fr_FR', IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
    ?>

    <!-- Calendrier des RDV -->
    <div class="appt-calendar-nav">
      <a href="?controller=Admin&action=dashboard&month=<?= $prev->format('m') ?>&year=<?= $prev->format('Y') ?>">⬅️</a>
      <span style="margin:0 1em; font-weight:bold;"><?= ucfirst($fmtNav->format($current)) ?></span>
      <a href="?controller=Admin&action=dashboard&month=<?= $next->format('m') ?>&year=<?= $next->format('Y') ?>">➡️</a>
    </div>
    <div class="appt-calendar">
      <?php foreach ($jours as $j): ?>
        <div class="appt-calendar-day appt-calendar-header"><?= $j ?></div>
      <?php endforeach; ?>
      <?php
        $startDow    = (int)$current->format('N');
        for ($i = 1; $i < $startDow; $i++) {
          echo '<div class="appt-calendar-day"></div>';
        }
        $daysInMonth = (int)$current->format('t');
        for ($d = 1; $d <= $daysInMonth; $d++):
          $dayStr = $current->format('Y-m-') . str_pad($d,2,'0',STR_PAD_LEFT);
          $has    = in_array($dayStr, $calendarDays, true);
          $cls    = $has ? 'has-appt' : 'no-appt';
      ?>
        <div class="appt-calendar-day <?= $cls ?>" 
             <?= $has ? "onclick=\"openDayModal('{$dayStr}')\"" : '' ?>
             data-date="<?= $dayStr ?>">
          <?= $d ?>
          <?php if ($has): ?>
            <span class="badge"><?= $counts[$dayStr] ?></span>
          <?php endif; ?>
        </div>
      <?php endfor; ?>
    </div>

    <!-- Modal liste RDV du jour -->
    <div id="overlay" class="overlay" onclick="closeModal()"></div>
    <div id="dayModal" class="modal">
      <div class="modal-header">
        <h2 id="modalDateTitle"></h2>
        <span class="modal-close" onclick="closeModal()">&times;</span>
      </div>
      <ul id="apptList" class="appt-list"></ul>
    </div>

    <!-- Réservations à venir -->
    <h2 id="upcoming" class="section">Réservations à venir</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Date</th><th>Heure</th><th>Sujet</th><th class="action">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($upcoming)): ?>
            <tr><td colspan="4">Aucune réservation à venir</td></tr>
          <?php else: foreach ($upcoming as $r):
            $disp = $r['date'] ? $fmtDate->format(new DateTime($r['date'])) : '-';
          ?>
            <tr>
              <td><?= htmlspecialchars($disp) ?></td>
              <td><?= htmlspecialchars($r['hour']) ?></td>
              <td><?= htmlspecialchars($r['subject']) ?></td>
              <td class="action">
                <button class="toggle-details">Lire plus</button>
                <a href="index.php?controller=Admin&action=cancel&id=<?= $r['id'] ?>&hour_id=<?= $r['hour_id'] ?>"
                   onclick="return confirm('Confirmer l’annulation ?')"
                   style="margin-left:.5rem;color:#c00;text-decoration:none;">
                  Annuler
                </a>
              </td>
            </tr>
            <tr class="details-row" style="display:none;">
              <td colspan="4">
                <strong>Email :</strong> <?= htmlspecialchars($r['email']) ?><br><br>
                <strong>Message :</strong><br>
                <div style="margin:.5rem 0;"><?= nl2br(htmlspecialchars($r['message'])) ?></div>
                <strong>Plateforme :</strong> <?= htmlspecialchars($r['platform']) ?><br>
                <strong>Nom plateforme :</strong> <?= htmlspecialchars($r['platform_name']) ?>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Historique des rendez-vous passés -->
    <h2 id="past" class="section">Historique des rendez-vous passés</h2>
    <div style="text-align:center; margin:0.5rem 0;">
      <a href="index.php?controller=Admin&action=archivePast"
         onclick="return confirm('Archiver tous les rendez-vous passés ?')"
         style="display:inline-block;background:var(--primary);color:#fff;
                padding:0.5rem 1rem;border-radius:4px;text-decoration:none;">
        Archiver les passés
      </a>
      <a href="index.php?controller=Admin&action=deletePast"
         onclick="return confirm('Supprimer définitivement tous les rendez-vous passés ?')"
         style="display:inline-block;background:#c00;color:#fff;
                padding:0.5rem 1rem;border-radius:4px;text-decoration:none;margin-left:0.5rem;">
        Supprimer les passés
      </a>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Date</th><th>Heure</th><th>Sujet</th><th class="action">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($past)): ?>
            <tr><td colspan="4">Aucun rendez-vous passé</td></tr>
          <?php else: foreach ($past as $r):
            $disp = $r['date'] ? $fmtDate->format(new DateTime($r['date'])) : '-';
          ?>
            <tr>
              <td><?= htmlspecialchars($disp) ?></td>
              <td><?= htmlspecialchars($r['hour']) ?></td>
              <td><?= htmlspecialchars($r['subject']) ?></td>
              <td class="action">
                <button class="toggle-details">Lire plus</button>
                <a href="index.php?controller=Admin&action=archive&id=<?= $r['id'] ?>"
                   onclick="return confirm('Archiver ce rendez-vous ?')"
                   style="margin-left:.5rem;color:#08c;text-decoration:none;">
                  Archiver
                </a>
                <a href="index.php?controller=Admin&action=deleteDashboard&id=<?= $r['id'] ?>"
                   onclick="return confirm('Supprimer définitivement ce rendez-vous ?')"
                   style="margin-left:.5rem;color:#c00;text-decoration:none;">
                  Supprimer
                </a>
              </td>
            </tr>
            <tr class="details-row" style="display:none;">
              <td colspan="4">
                <strong>Email :</strong> <?= htmlspecialchars($r['email']) ?><br><br>
                <strong>Message :</strong><br>
                <div style="margin:.5rem 0;"><?= nl2br(htmlspecialchars($r['message'])) ?></div>
                <strong>Plateforme :</strong> <?= htmlspecialchars($r['platform']) ?><br>
                <strong>Nom plateforme :</strong> <?= htmlspecialchars($r['platform_name']) ?>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

  </div>

  <script>
    // Modal jours
    function openDayModal(date) {
      const fr = new Date(date).toLocaleDateString('fr-FR', {
        weekday:'long', day:'numeric', month:'long', year:'numeric'
      });
      document.getElementById('modalDateTitle').textContent =
        fr.charAt(0).toUpperCase() + fr.slice(1);

      fetch(`?controller=Admin&action=getAppointmentsForDate&date=${date}`)
        .then(r => r.json())
        .then(list => {
          const ul = document.getElementById('apptList');
          ul.innerHTML = '';
          list.forEach(a => {
            const li = document.createElement('li');
            li.className = 'appt-item';
            li.innerHTML = `
              <h4>${a.hour} – ${a.subject}</h4>
              <button class="toggle-detail">Lire plus</button>
              <a
                href="index.php?controller=Admin&action=cancel&id=${a.id}&hour_id=${a.hour_id}"
                onclick="return confirm('Confirmer l’annulation ?')"
                style="margin-left:1rem;color:#c00;text-decoration:none;"
              >Annuler</a>
              <div class="details">
                <strong>Email :</strong> ${a.email}<br>
                <strong>Message :</strong><br>${a.message.replace(/\n/g,'<br>')}<br>
                <strong>Plateforme :</strong> ${a.platform}<br>
                <strong>Nom plateforme :</strong> ${a.platform_name}
              </div>`;
            ul.appendChild(li);
          });
          ul.querySelectorAll('.toggle-detail').forEach(btn => {
            btn.addEventListener('click', () => {
              const det = btn.nextElementSibling.nextElementSibling;
              det.style.display = det.style.display === 'block' ? 'none' : 'block';
              btn.textContent = det.style.display === 'block' ? 'Cacher' : 'Lire plus';
            });
          });
          document.getElementById('overlay').style.display = 'block';
          document.getElementById('dayModal').style.display = 'block';
        });
    }
    function closeModal() {
      document.getElementById('dayModal').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }
    // Toggle détails tableau
    document.querySelectorAll('button.toggle-details').forEach(btn => {
      btn.addEventListener('click', () => {
        const tr = btn.closest('tr'),
              det = tr.nextElementSibling;
        det.style.display = det.style.display === 'table-row' ? 'none' : 'table-row';
        btn.textContent = det.style.display === 'table-row' ? 'Cacher' : 'Lire plus';
      });
    });
  </script>
</body>
</html>
