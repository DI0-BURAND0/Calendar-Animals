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
                top: 40%;
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
                text-align: center!important;
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
            <h1>Mentions légales</h1>
            <h2>Nom et siège social</h2>
            <div class="blue">
                <span class="bold">Culture & Data</span> est dénommée. Son siège social est situé à <span
                    class="italic">48 rue Anatole France, 79400 Saint-Maixent-l'École, France.</span>
            </div>

            <h2>Directeur(s) de l'entreprise</h2>
            <div class="blue">
                Le directeur unique de l'entreprise est <span class="bold">Romain Jiacconi.</span>
            </div>

            <h2>Objet social</h2>
            <div class="blue">
                L'objet social de l'entreprise <span class="bold">Culture & Data</span> est la fourniture de services
                et
                produits dans les domaines de la culture, de la communication numérique et des données.
            </div>

            <h2>Capitaux propres et participations</h2>
            <div class="blue">
                Les capitaux propres de l'entreprise <span class="bold">Culture & Data</span> ne sont pas précisés.
                L'entreprise ne détient aucune participation dans d'autres sociétés.
            </div>

            <h2>Réserves légales</h2>
            <div class="blue" style="font-size: 0.9em;">
                Conformément aux dispositions des lois et règlements français, la présente information légale est
                précisée
                de manière exhaustive <br> pour donner aux utilisateurs des informations complètes sur l'entreprise
                <span class="bold">Culture & Data</span>. Il est conseillé de consulter cette page à chaque fois pour
                avoir
                les informations les plus récentes sur l'entreprise <span class="bold">Culture & Data</span>.
            </div>
            <!-- Add more content here -->
        </div>
    </div>
</body>

</html>
