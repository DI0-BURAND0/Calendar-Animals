<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Calendrier & Formulaire</title>
    <link rel="stylesheet" href="css/contact_form.css">
    <style>
        /* Bouton toggle */
        #toggleCalendarBtn {
            display: block;
            margin: 1rem auto;
            padding: 0.5rem 1rem;
            background:rgb(35 157 45);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #toggleCalendarBtn:hover {
            background: rgb(38, 110, 44)
        }
    </style>
    <style>
        .process-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            /* Ajouté pour permettre aux éléments de passer à la ligne */
        }

        .process-item {
            cursor: pointer;
            font-weight: bold;
            padding: 10px;
            transition: color 0.3s;
            font-size: 16px
        }

        @media (max-width: 720px) {
            .process-steps {
                flex-direction: column;
                align-items: center;
            }
        }

        .calendar-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .calendar-nav a {
            font-size: 1.5rem;
            text-decoration: none;
        }

        .calendar-nav h2 {
            margin: 0;
            font-size: 1.5rem;
            color: #fff;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
        }

        .process-item:hover {
            color: #9fcd90;
            margin-top: 10px;
            transform: scale(1.02);
            transition: all 0.2s ease-in-out;
        }

        @media (max-width: 420px) {
            .container-footer {
                justify-content: end;
            }
        }


        .process-steps {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .process-steps span {
            cursor: pointer;
            font-weight: bold;
            color: white;
        }

        @media (max-width: 720px) {
            .process-steps {
                flex-direction: column;
                align-items: center;
            }
        }

        .description {
            margin-top: 20px;
            font-size: 18px;
            display: none;
        }

        #cookie-consent {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            display: none;
            z-index: 2;
        }

        #cookie-consent button,
        #manage-consent {
            margin: 10px;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .accept {
            background: #4CAF50;
            color: white;
        }

        .decline {
            background: #FF5733;
            color: white;
        }

        .manage {
            background: #007BFF;
            color: white;
            position: fixed;
            bottom: 10px;
            right: 10px;
        }

        #cookie-details {
            display: none;
            background: #444;
            color: white;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Quill Styles -->
    <!-- Quill CSS (Required) -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
</head>

<body>
    <script>
        function toggleDetails(event) {
            event.preventDefault();
            var details = document.getElementById("cookie-details");
            details.style.display = details.style.display === "block" ? "none" : "block";
        }

        function acceptCookies() {
            localStorage.setItem("cookieConsent", "accepted");
            document.getElementById("cookie-consent").style.display = "none";
            document.getElementById("manage-consent").style.display = "block";
        }

        function declineCookies() {
            localStorage.setItem("cookieConsent", "declined");
            document.getElementById("cookie-consent").style.display = "none";
            document.getElementById("manage-consent").style.display = "block";
        }

        function resetConsent() {
            localStorage.removeItem("cookieConsent");
            document.getElementById("cookie-consent").style.display = "block";
            document.getElementById("manage-consent").style.display = "none";
        }

        window.onload = function() {
            if (!localStorage.getItem("cookieConsent")) {
                document.getElementById("cookie-consent").style.display = "block";
            } else {
                document.getElementById("manage-consent").style.display = "block";
            }
        }
    </script>
    <video id="backgroundVideo" autoplay muted playsinline class="videoBG">
        <source src="videos/bg-a.mp4" type="video/mp4">
    </video>
    <script>
        const video = document.getElementById('backgroundVideo');

        video.addEventListener('ended', () => {
            video.currentTime = 0;
            video.play();
        });
    </script>
    <section class="grid spaceAround rsCol">
        <div class="left-panel ">
            <img class="about-photo-left center" src="img/Culture&data10.png" alt="">
            <img class="about-photo center" src="img/Culture&data11.png" alt="">
            <h1 class="left text-panel s-title">Le digital au service de l'humain.
            </h1>
            <div class="socials"> <a href="https://www.instagram.com/"><img class="socials-left-panel"
                        src="img/inst.png" alt=""></a>
                <a href="https://www.youtube.com/"><img class="socials-left-panel" src="img/yt.png"
                        alt=""></a>
                <a href="https://www.linkedin.com/"><img class="socials-left-panel" src="img/linkedin.png"
                        alt=""></a>
            </div>

        </div>
        <div class="right-panel center">
            <div class="my-container-videos">
                <iframe src="https://www.youtube.com/embed/JcZ2O4MDkn0?si=MwD2WBRaBMjgKJLM" title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>

            <div class="my-container-videos">

                <iframe src="https://www.youtube.com/embed/JcZ2O4MDkn0?si=MwD2WBRaBMjgKJLM" title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <div class="my-container-videos">

                <iframe src="https://www.youtube.com/embed/JcZ2O4MDkn0?si=MwD2WBRaBMjgKJLM" title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <div class="my-container fill ">
                <h2 class="center">Tout a commencé avec une conviction simple :</h2>
                <p><br><br>Les producteurs, artisans et entrepreneurs locaux méritent d’être vus et reconnus à leur
                    juste valeur. Dans un monde où les géants du commerce écrasent les circuits courts, il était temps
                    d’agir autrement.
                    <br>

                    <br>
                    Nous, c’est Culture&Data, une agence née d’un double savoir-faire : la communication visuelle et la
                    data. À travers nos vidéos, nos stratégies digitales et nos analyses, nous aidons ceux qui façonnent
                    l’économie locale à se démarquer, toucher leur public et maximiser leur impact.
                    <br>
                    <br>
                    🎥 Raconter votre histoire à travers des images percutantes.
                    <br>
                    📢 Attirer votre audience avec des campagnes digitales ciblées.
                    <br>
                    📊 Optimiser votre visibilité grâce aux données et aux chiffres qui comptent.
                    <br>
                    <br>
                    Nous ne sommes pas une simple agence. Nous sommes des passeurs d’histoires, des facilitateurs de
                    croissance, des alliés du local. Chaque projet que nous accompagnons a un sens : mettre en lumière
                    votre savoir-faire et le connecter aux bonnes personnes.
                    <br>
                    <br>
                    Parce que votre réussite, c’est aussi celle d’un modèle plus juste, plus humain, plus durable.
                </p>
            </div>
            <div class="my-container fill">
                <h2 class="center">Equipements :</h2> <br><br>
                <p><img class="equipment-image" src="img/equipment.JPG" alt=""> <br>
                    Derrière chaque image percutante, il y a la technologie et le savoir-faire. <br><br> Nos drones,
                    caméras et équipements de pointe nous permettent de capturer des plans immersifs, des détails
                    saisissants et des perspectives inédites.


                </p>
            </div>

            <div class="my-container fill">
                <div class="process-container">
                    <div> <img src="img/1.png" alt=""><span class="process-item"
                            onclick="showDescription('consultation')">Consultation</span></div>
                    <div> <img src="img/2.png" alt=""><span class="process-item"
                            onclick="showDescription('conception')">Conception</span></div>
                    <div> <img src="img/3.png" alt=""><span class="process-item"
                            onclick="showDescription('realisation')">Réalisation</span></div>
                    <div> <img src="img/4.png" alt=""><span class="process-item"
                            onclick="showDescription('livraison')">Livraison</span></div>
                </div>

                <div id="description" class="description"></div>
            </div>
        </div>

        <div class="right-panel center">
            <div class="my-container fill-2 calendar-container">

                <?php if (!empty($error)): ?>
                    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <!-- Bouton pour ouvrir/fermer le calendrier -->
                <button id="toggleCalendarBtn">Prendre un rendez-vous</button>

                <?php
                // calcul du mois/année...
                $currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
                $currentYear  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
                $current = new DateTime();
                $current->setDate($currentYear, $currentMonth, 1);

                // formateur FR pour "MMMM yyyy"
                $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'MMMM yyyy');
                $monthLabel = ucfirst($formatter->format($current));

                $prev = (clone $current)->modify('-1 month');
                $next = (clone $current)->modify('+1 month');
                ?>

                <!-- Conteneur du calendrier, fermé au chargement -->
                <div id="calendarContainer" style="display: none;">
                    <div class="calendar-nav center-text">
                        <a href="?month=<?= $prev->format('m') ?>&year=<?= $prev->format('Y') ?>">⬅️</a>
                        <h2><?= $monthLabel ?></h2>
                        <a href="?month=<?= $next->format('m') ?>&year=<?= $next->format('Y') ?>">➡️</a>
                    </div>


                    <div class="calendar">
                        <?php foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $j): ?>
                            <div class="calendar-day calendar-header"><?= $j ?></div>
                        <?php endforeach; ?>

                        <?php
                        $startDow    = (int)$current->format('N');
                        for ($i = 1; $i < $startDow; $i++) {
                            echo '<div class="calendar-day"></div>';
                        }
                        $daysInMonth = (int)$current->format('t');
                        $today       = date('Y-m-d');
                        $available   = array_map(fn($d) => (new DateTime($d))->format('Y-m-d'), $calendarDays);
                        for ($d = 1; $d <= $daysInMonth; $d++):
                            $dayStr = $current->format('Y-m-') . str_pad($d, 2, '0', STR_PAD_LEFT);
                            $isIn   = in_array($dayStr, $available, true);
                            $isPast = $dayStr < $today;
                            $cls    = 'calendar-day'
                                . ($isIn   ? ' dispo' : '')
                                . ($dayStr === $today ? ' today' : '')
                                . ((!$isIn || $isPast) ? ' disabled' : '');
                            $click  = ($isIn && !$isPast) ? "onclick=\"fetchHours('$dayStr')\"" : '';
                        ?>
                            <div class="<?= $cls ?>" <?= $click ?>><?= $d ?></div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Overlay et modales inchangés -->
                <div id="overlay" class="overlay" onclick="closeModals()"></div>

                <div id="hourModal" class="modal">
                    <span class="modal-close" onclick="closeModals()">&times;</span>
                    <div class="modal-header">
                        Heures disponibles pour <span id="modalDate"></span>
                    </div>
                    <ul id="hourList" style="list-style:none;text-align:center;"></ul>
                </div>

                <div id="contactModal" class="modal">
                    <span class="modal-close" onclick="closeModals()">&times;</span>
                    <div class="modal-header">
                        Réserver le <span id="selectedDate"></span> à <span id="selectedHour"></span>
                    </div>
                    <form class="form-book" action="index.php?action=submit" method="POST">
                        <input type="hidden" name="day" id="dayInput">
                        <input type="hidden" name="time" id="timeInput">
                        <input type="hidden" name="hour_id" id="hourIdInput">

                        <!-- champs du formulaire -->
                        <label for="email">Email :</label><br>
                        <input type="email" name="email" id="email" required style="width:80%;"><br><br>
                        <label for="subject">Sujet :</label><br>
                        <input type="text" name="subject" id="subject" required style="width:80%;"><br><br>
                        <label for="message">Message :</label><br>
                        <textarea name="message" id="message" rows="4" required style="width:80%;"></textarea><br><br>
                        <label for="platform">Plateforme :</label><br>
                        <select name="platform" id="platform" required style="width:80%;">
                            <option value="">-- Choisir --</option>
                            <option value="Zoom">Zoom</option>
                            <option value="Google Meet">Google Meet</option>
                            <option value="Teams">Teams</option>
                            <option value="Whatsapp">Whatsapp</option>
                            <option value="Téléphone">Téléphone</option>
                        </select><br><br>
                        <label for="platform_name">Nom d'utilisateur / téléphone :</label><br>
                        <input type="text" name="platform_name" id="platform_name" required style="width:80%;"><br><br>
                            <p>En soumettant ce formulaire, vous acceptez nos conditions générales et politiques de confidentialité</p> <br>
                        <button class="sbt-btn" type="submit">Envoyer</button>
                    </form>
                </div>

                <script src="js/contact_form.js"></script>
                <script>
                    // Toggle calendrier
                    const toggleBtn = document.getElementById('toggleCalendarBtn');
                    const cal = document.getElementById('calendarContainer');
                    toggleBtn.addEventListener('click', () => {
                        const hidden = cal.style.display === 'none';
                        cal.style.display = hidden ? 'block' : 'none';
                        toggleBtn.textContent = hidden ? 'Masquer le calendrier' : 'Prendre RDV';
                    });

                    // Navigation Ajax des flèches
                    document.getElementById('calendarContainer')
                        .addEventListener('click', e => {
                            const link = e.target.closest('.calendar-nav a');
                            if (!link) return;
                            e.preventDefault();
                            fetch(link.href)
                                .then(r => r.text())
                                .then(html => {
                                    const doc = new DOMParser().parseFromString(html, 'text/html');
                                    const newCal = doc.getElementById('calendarContainer');
                                    cal.innerHTML = newCal.innerHTML;
                                });
                            history.pushState(null, '', link.href);
                        });
                    window.addEventListener('popstate', () => {
                        fetch(location.href)
                            .then(r => r.text())
                            .then(html => {
                                const doc = new DOMParser().parseFromString(html, 'text/html');
                                const newCal = doc.getElementById('calendarContainer');
                                cal.innerHTML = newCal.innerHTML;
                            });
                    });

                    // Fermeture modales existantes
                    function closeModals() {
                        document.getElementById('hourModal').style.display = 'none';
                        document.getElementById('contactModal').style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    }
                </script>






            </div>
        </div>

        <script>
            function showDescription(step) {
                const descriptions = {
                    "consultation": "La consultation est l'étape où nous discutons de vos besoins et attentes.",
                    "conception": "La conception consiste à créer les maquettes et les plans du projet.",
                    "realisation": "La réalisation est la phase où le projet prend vie concrètement.",
                    "livraison": "La livraison est la dernière étape où nous vous remettons le produit final."
                };

                const descriptionDiv = document.getElementById("description");
                descriptionDiv.innerText = descriptions[step];
                descriptionDiv.style.display = "block";
            }
        </script>

        </div>
        </div>
    </section>
    <footer> <!-- mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 -->
        <div class="mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="footer-block">
            <div class="footer-item">
                <h2 class="font-semibold mb-2">Culture & Data</h2>
                <p class="mb-4">Nous mettons en lumière les producteurs et entrepreneurs locaux grâce à la
                    communication visuelle et la data. À travers vidéos, stratégies digitales et analyses, nous les
                    aidons à se démarquer et à maximiser leur impact.</p>
            </div>
            <div class="footer-item">
                <h2 class="font-semibold mb-2">Infos & Politique</h2>
                <a href="/index.php?controller=Contact&action=politics">
                    <p>Politique de confidentialité</p>
                </a>
                <a href="/index.php?controller=Contact&action=tos">
                    <p>Conditions générales de vente</p>
                </a>
                <a href="/index.php?controller=Contact&action=mentions">
                    <p>Mentions légales</p>
                </a>
            </div>
            <div class="footer-item">
                <h2 class="font-semibold mb-2">Contact</h2>
                <p>Mail: Culture_Data@proton.me</p>
                <!-- <p>Téléphone: 06.xx.xx.xx.xx</p> -->
            </div>

        </div>
    </footer>


</body>

</html>