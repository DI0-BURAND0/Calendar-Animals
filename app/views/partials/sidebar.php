<?php
// app/views/partials/sidebar.php
?>
<aside class="sidebar">
  <h2>Admin</h2>
  <nav>
    <ul>
      <li><a href="index.php?controller=Admin&action=dashboard">Tableau de bord</a></li>
      <li><a href="index.php?controller=Admin&action=archived">Archivés &amp; Annulés</a></li>
      <li><a href="index.php?controller=Admin&action=availability">Gestion des disponibilités</a></li>
    </ul>
  </nav>
  <a href="index.php?controller=Admin&action=logout" class="logout">Déconnexion</a>
</aside>
