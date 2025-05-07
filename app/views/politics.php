<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>

        <style>
            body {
                margin: 0;
                text-align: center;
                background: #ebebeb;
            }

            .container:hover {
                -webkit-box-shadow: 5px 5px 10px 5px #000000;
                box-shadow: 5px 5px 10px 5px #000000;
                scale: 1.02;
                transition: all 0.3s ease-in-out;
            }

            .container {
                width: 800px;
                position: absolute;
                left: 50%;
                top: 60%;
                transform: translate(-50%, -50%);
                font-family: Arial, sans-serif;
                line-height: 2;
                text-align: justify;
                border: 1px solid #9b9b9b;
                border-radius: 10px;
                padding: 40px;
                background: #ffffff;
                margin-top: 90px
            }

            h2 {
                font-weight: bold;
                font-size: 1.4em;
                background-color: #c0ffc4;
                padding: 10px;
                width: fit-content;
                margin: 0 auto;
                padding: 10px 20px;
                border-style: solid;
                border-width: 1px;
                border-color: #000000;
                border-radius: 20px;
                margin-bottom: 10px;
                margin-top: 10px;
            }

            .bold {
                font-weight: bold;
                color: #333;
            }

            .italic {
                font-style: italic;
            }

            .blue {
                width: fit-content;
                margin: 0 auto;
                text-align: center;
            }

            h1 {
                text-align: center !important;
            }

            @media only screen and (max-width:1280px) {
                .container {
                    width: 90%;
                    padding: 20px;
                }

                h2 {
                    text-align: center;
                    font-size: 1.1em;
                    padding: 0px 10px;
                }

                .container:hover {
                    scale: 1;
                }
            }
        </style>

        <div class="container">
            <h1>Politique de confidentialité</h1>
            <h2>Données collectées</h2>
            <div class="blue">
                <span>Culture & Data collecte certaines informations sur ses clients, telles que leur nom, adresse
                    e-mail, téléphone, adresse postale et informations financières.</span>
            </div>

            <h2>Utilisation des données collectées</h2>
            <div class="blue">
                <span>Les données collectées sont utilisées pour fournir les produits et services achetés par les
                    clients, pour envoyer des communications commerciales et pour améliorer l'expérience de navigation
                    sur le site web.</span>
            </div>

            <h2>Partage de données</h2>
            <div class="blue">
                <span>Culture & Data peut partager les informations collectées avec d'autres entreprises ou personnes
                    pour fournir les produits et services achetés par les clients, mais seulement si cela est nécessaire
                    et conforme aux lois en vigueur.</span>
            </div>

            <h2>Sécurité des données</h2>
            <div class="blue">
                <span>Culture & Data tient à sécuriser les informations collectées sur ses clients et a mise en place
                    des mesures de sécurité appropriées pour les protéger contre toute perte, destruction ou intrusion
                    non autorisée.</span>
            </div>

            <h2>Accès aux données</h2>
            <div class="blue" style="font-size: 0.9em;">
                <span>Les clients peuvent accéder aux informations qu'ils ont fournies à Culture & Data et les modifier
                    en cas d'erreur ou de nécessité.</span>
            </div>

            <h2>Responsabilité de Culture & Data</h2>
            <div class="blue" style="font-size: 0.9em;">
                <span>Culture & Data n'est pas responsable des données collectées par les tiers et ne peut contrôler la
                    manière dont ces tiers utilisent les informations collectées.</span>
            </div>

            <h2>Enquêtes et statistiques</h2>
            <div class="blue" style="font-size: 0.9em;">
                <span>Culture & Data utilise les informations collectées pour réaliser des enquêtes et des statistiques,
                    mais sans révéler les informations personnelles des clients.</span>
            </div>
            <!-- Add more content here -->
        </div>
    </div>
</body>

</html>
