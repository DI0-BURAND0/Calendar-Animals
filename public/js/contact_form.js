// public/js/contact_form.js

function closeModals() {
    document.getElementById('hourModal').style.display = 'none';
    document.getElementById('contactModal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

function fetchHours(date) {
    // 1️⃣ formater la date en FR complet
    const dt = new Date(date);
    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    let dateFr = dt.toLocaleDateString('fr-FR', options);
    // capitaliser la première lettre
    dateFr = dateFr.charAt(0).toUpperCase() + dateFr.slice(1);

    // 2️⃣ afficher overlay et titre de la modale
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('modalDate').innerText = dateFr;

    // 3️⃣ récupérer les heures
    fetch(`${window.location.pathname}?action=hours&date=${date}`)
        .then(res => res.json())
        .then(data => {
            const ul = document.getElementById('hourList');
            ul.innerHTML = '';
            if (data.length) {
                data.forEach(h => {
                    const li = document.createElement('li');
                    li.textContent = h.hour_of_day;
                    li.style.cursor = 'pointer';
                    li.onclick = () => openContactForm(date, h.hour_of_day, h.id);
                    ul.appendChild(li);
                });
            } else {
                ul.innerHTML = '<li>Aucune heure disponible</li>';
            }
            document.getElementById('hourModal').style.display = 'block';
        })
        .catch(() => alert('Erreur chargement heures'));
}

function openContactForm(date, hour, hourId) {
    closeModals();

    // 1) on formate la date en FR
    const dt = new Date(date);
    const opts = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    let dateFr = dt.toLocaleDateString('fr-FR', opts);
    dateFr = dateFr.charAt(0).toUpperCase() + dateFr.slice(1);

    // 2) on cible explicitement les spans DANS la modale contactModal
    //    ça évite les confusions si vous aviez plusieurs éléments avec le même id ailleurs
    const modal = document.getElementById('contactModal');
    const spanDate = modal.querySelector('#selectedDate');
    const spanHour = modal.querySelector('#selectedHour');

    spanDate.innerText = dateFr;
    spanHour.innerText = hour;

    // 3) on prépare les inputs cachés
    document.getElementById('dayInput').value = date;
    document.getElementById('timeInput').value = hour;
    document.getElementById('hourIdInput').value = hourId;

    // 4) on ouvre la modale
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('contactModal').style.display = 'block';
}


// Optionnel : fermer la modale en appuyant sur Échap
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModals();
});
