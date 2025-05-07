<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Gestion des disponibilités</title>

  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/admin_dashboard.css">
  <link rel="stylesheet" href="css/admin_availability.css">
  <style>
    /* Spécifique calendrier disponibilités */
    .calendar { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; text-align:center; }
    .calendar-day { padding:8px; border:1px solid #ccc; min-height:60px; cursor:pointer; position:relative; }
    .calendar-header { font-weight:bold; background:#eee; cursor:default; }
    .available-day   { background:#d4fcd4; }
    .unavailable-day { background:#fcd4d4; }

    /* Modale heures */
    #hourTable { width:100%; border-collapse:collapse; margin-top:1em; }
    #hourTable th, #hourTable td { padding:8px; border:1px solid #ccc; }
    #hourTable button { padding:4px 8px; border:none; border-radius:4px; cursor:pointer; }
    .btn-enable  { background:#4CAF50; color:#fff; }
    .btn-disable { background:#f44336; color:#fff; }

    /* Boutons jours */
    #dayControls { text-align:center; margin-bottom:1em; }
    #dayControls button {
      margin:0 .5em;
      padding:.5em 1em;
      border:none;
      border-radius:4px;
      color:#fff;
      cursor:pointer;
    }
    #btnMarkAvail   { background:#4CAF50; }
    #btnMarkUnavail { background:#f44336; }

    /* Bulk controls */
    #bulkControls { text-align:center; margin-bottom:1em; }
    #bulkControls button {
      margin:0 .5em;
      padding:.5em 1em;
      border:none;
      border-radius:4px;
      color:#fff;
      cursor:pointer;
    }
    #bulkEnable   { background:#4CAF50; }
    #bulkDisable  { background:#f44336; }
    .bulk-checkbox {
      position:absolute;
      top:4px; left:4px;
      z-index:10;
      width:16px; height:16px;
    }
  </style>
</head>
<body>

  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <div class="main">
    <h1 style="text-align:center;">Gestion des disponibilités</h1>

    <?php
      $m = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
      $y = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
      $current = new DateTime("$y-$m-01");
      $prev    = (clone $current)->modify('-1 month');
      $next    = (clone $current)->modify('+1 month');
      $fmt = new IntlDateFormatter(
        'fr_FR',
        IntlDateFormatter::NONE,
        IntlDateFormatter::NONE,
        null,
        null,
        'MMMM yyyy'
      );
      // $availableDays : array de 'YYYY-MM-DD', fourni par le controller
    ?>
    <div style="text-align:center; margin-bottom:1rem;">
      <a href="?controller=Admin&action=availability&month=<?= $prev->format('m') ?>&year=<?= $prev->format('Y') ?>">⬅️</a>
      <span style="margin:0 1em; font-weight:bold;">
        <?= ucfirst($fmt->format($current)) ?>
      </span>
      <a href="?controller=Admin&action=availability&month=<?= $next->format('m') ?>&year=<?= $next->format('Y') ?>">➡️</a>
    </div>

    <!-- Bulk toggle form -->
    <form id="bulkForm" method="POST" action="?controller=Admin&action=bulkToggleDays">
      <div id="bulkControls">
        <button type="submit" name="toggle" value="1" id="bulkEnable">Activer sélection</button>
        <button type="submit" name="toggle" value="0" id="bulkDisable">Désactiver sélection</button>
      </div>

      <div class="calendar">
        <?php foreach (['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $d): ?>
          <div class="calendar-day calendar-header"><?= $d ?></div>
        <?php endforeach; ?>

        <?php
          $startDow    = (int)$current->format('N');
          $daysInMonth = (int)$current->format('t');
          for ($i = 1; $i < $startDow; $i++) {
            echo '<div class="calendar-day"></div>';
          }
          for ($day = 1; $day <= $daysInMonth; $day++):
            $date    = $current->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
            $isAvail = in_array($date, $availableDays, true);
            $cls     = $isAvail ? 'available-day' : 'unavailable-day';
        ?>
          <div class="calendar-day <?= $cls ?>"
               data-date="<?= $date ?>"
               onclick="openDayModal('<?= $date ?>', <?= $isAvail ? 'true' : 'false' ?>)">
            <input type="checkbox"
                   class="bulk-checkbox"
                   name="dates[]"
                   value="<?= $date ?>"
                   onclick="event.stopPropagation()">
            <span><?= $day ?></span>
          </div>
        <?php endfor; ?>
      </div>
    </form>
  </div>

  <!-- Overlay -->
  <div id="overlay" class="overlay" onclick="closeModals()"></div>

  <!-- Modale jour -->
  <div id="dayModal" class="modal">
    <div class="modal-header">
      <span id="dayModalTitle"></span>
      <span class="modal-close" onclick="closeModals()">&times;</span>
    </div>

    <!-- Boutons jour -->
    <div id="dayControls">
      <button id="btnMarkAvail">Marquer disponible</button>
      <button id="btnMarkUnavail">Marquer indisponible</button>
    </div>

    <!-- Heures -->
    <h4 style="text-align:center;">
      Heures du <span id="hourModalDate"></span> :
    </h4>
    <table id="hourTable">
      <thead>
        <tr><th>Heure</th><th>Statut</th><th>Action</th></tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script>
    // Bulk form validation
    document.getElementById('bulkForm').addEventListener('submit', function(e) {
      const anyChecked = this.querySelector('input[name="dates[]"]:checked');
      if (!anyChecked) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins un jour.');
      }
    });

    let currentDate = '';

    function openDayModal(date, isAvail) {
      currentDate = date;
      document.getElementById('overlay').style.display = 'block';
      document.getElementById('dayModal').style.display = 'block';

      const frFull = new Date(date).toLocaleDateString('fr-FR', {
        weekday: 'long',
        day:     'numeric',
        month:   'long',
        year:    'numeric'
      });
      const frCap = frFull.charAt(0).toUpperCase() + frFull.slice(1);

      document.getElementById('dayModalTitle').textContent  = frCap;
      document.getElementById('hourModalDate').textContent = frCap;

      document.getElementById('btnMarkAvail').onclick   = () => setDay(date, 1);
      document.getElementById('btnMarkUnavail').onclick = () => setDay(date, 0);

      loadHours(date);
    }

    function loadHours(date) {
      fetch(`?controller=Admin&action=getAvailability&date=${date}`)
        .then(r => r.json())
        .then(hours => {
          const tbody = document.querySelector('#hourTable tbody');
          tbody.innerHTML = '';
          hours.forEach(h => {
            const tr = document.createElement('tr');
            const td1 = document.createElement('td');
            td1.textContent = new Date(`1970-01-01T${h.hour_of_day}`)
              .toLocaleTimeString('fr-FR', { hour:'2-digit', minute:'2-digit' });
            tr.appendChild(td1);

            const td2 = document.createElement('td');
            td2.textContent = h.available ? '✔︎' : '✘';
            tr.appendChild(td2);

            const td3 = document.createElement('td');
            const btn = document.createElement('button');
            btn.textContent = h.available ? 'Désactiver' : 'Activer';
            btn.className = h.available ? 'btn-disable' : 'btn-enable';
            btn.onclick = () => {
              fetch(`?controller=Admin&action=toggleHour&hour_id=${h.id}`)
                .then(() => loadHours(date));
            };
            td3.appendChild(btn);
            tr.appendChild(td3);

            tbody.appendChild(tr);
          });
        });
    }

    function setDay(date, avail) {
      fetch(`?controller=Admin&action=setDayAvailability&date=${date}&available=${avail}`)
        .then(r => r.json())
        .then(json => {
          if (json.success) {
            const cell = document.querySelector(`.calendar-day[data-date="${date}"]`);
            if (cell) {
              cell.classList.toggle('available-day', avail === 1);
              cell.classList.toggle('unavailable-day', avail === 0);
            }
            loadHours(date);
          }
        });
    }

    function closeModals() {
      document.getElementById('dayModal').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }
  </script>

</body>
</html>
