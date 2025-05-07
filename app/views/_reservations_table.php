<div class="table-responsive">
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Heure</th>
        <th>Sujet</th>
        <th class="action">Action</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($reservations)): ?>
      <tr><td colspan="4">Aucune réservation</td></tr>
    <?php else: ?>
      <?php foreach ($reservations as $r): 
        $displayDate = (!empty($r['date']))
                     ? $fmtDate->format(new DateTime($r['date']))
                     : '-';
      ?>
      <tr>
        <td><?= htmlspecialchars($displayDate) ?></td>
        <td><?= htmlspecialchars($r['hour']) ?></td>
        <td><?= htmlspecialchars($r['subject']) ?></td>
        <td class="action">
          <button class="toggle-details">Lire plus</button>
        </td>
      </tr>
      <tr class="details-row" style="display:none;">
        <td colspan="4">
          <strong>Email :</strong>
          <span class="copyable" data-copy="<?= htmlspecialchars($r['email']) ?>">
            <?= htmlspecialchars($r['email']) ?>
          </span><br><br>

          <strong>Message :</strong><br>
          <div style="margin:0.5rem 0;"><?= nl2br(htmlspecialchars($r['message'])) ?></div>

          <strong>Plateforme :</strong>
            <?= htmlspecialchars($r['platform']) ?><br><br>

          <strong>Nom plateforme :</strong>
          <span class="copyable" data-copy="<?= htmlspecialchars($r['platform_name']) ?>">
            <?= htmlspecialchars($r['platform_name']) ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>
