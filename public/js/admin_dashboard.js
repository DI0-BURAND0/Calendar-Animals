// public/js/admin_dashboard.js
document.addEventListener('DOMContentLoaded', () => {
    // Toggle des lignes de détails
    document.querySelectorAll('button.toggle-details').forEach(btn => {
      btn.addEventListener('click', () => {
        const tr      = btn.closest('tr');
        const details = tr.nextElementSibling;
        if (details.style.display === 'table-row') {
          details.style.display = 'none';
          btn.textContent       = 'Lire plus';
        } else {
          details.style.display = 'table-row';
          btn.textContent       = 'Cacher';
        }
      });
    });
  
    // Copy to clipboard (email & nom plateforme)
    document.querySelectorAll('.copyable').forEach(el => {
      el.addEventListener('click', () => {
        navigator.clipboard.writeText(el.dataset.copy)
          .then(() => {
            const fb = document.createElement('span');
            fb.className   = 'copied-feedback';
            fb.textContent = 'copié';
            el.appendChild(fb);
            setTimeout(() => el.removeChild(fb), 1000);
          });
      });
    });
  });
  