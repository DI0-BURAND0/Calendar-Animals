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
                top: 58%;
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
            <h1>Conditions Générales de Ventes</h1>
            <h2>Vérification des informations</h2>
            <div class="blue">
                <span>Les informations fournies par les clients sont exactes, completes et actualisées. Les clients se
                    responsabilisent de toute conséquence qui résulterait d'une inexactitude dans les informations
                    fournies.
                </span>
            </div>

            <h2>Paiement </h2>
            <div class="blue">
                <span>Les paiements doivent être effectués avant la livraison des produits ou des services. La méthode
                    de paiement est déterminée par Culture & Data et peut évoluer en fonction du cas particulier.
                </span>
            </div>

            <h2>Livraison </h2>
            <div class="blue">
                <span>La livraison des produits ou des services se fera dans les délais spécifiés sur le site web, mais
                    il peut s'écouler jusqu'à 7 jours supplémentaires en fonction de la disponibilité du produit ou du
                    service.
                </span>
            </div>

            <h2>Garantie </h2>
            <div class="blue">
                <span>Les produits et services sont garantis pour une durée déterminée après leur livraison aux clients.
                    En cas de défaut, les clients doivent contacter Culture & Data pour obtenir une réparation ou un
                    remplacement gratuit.
                </span>
            </div>

            <h2>Responsabilité des clients</h2>
            <div class="blue" style="font-size: 0.9em;">
                <span>Les clients sont responsables du maintien et de la préservation des produits et services achetés
                    et doivent se conformer aux instructions spécifiées dans les manuels d'utilisation ou sur le site
                    web.</span>
            </div>

            <h2>Renvoi de produit ou de service</h2>
            <div class="blue" style="font-size: 0.9em;">
                <span>En cas de renvoi de produit ou de service, les clients doivent contacter Culture & Data pour
                    obtenir des instructions sur la procédure à suivre et pour savoir s'ils sont éligibles à un
                    remboursement.
                </span>
            </div>

            <h2>Droits d'auteur</h2>
            <div class="blue" style="font-size: 0.9em;">
                <span>Tous les droits d'auteur et de propriété intellectuelle concernant les produits ou services
                    achetés appartiennent exclusivement à Culture & Data et ne peuvent pas être utilisés sans son
                    autorisation.</span>
            </div>
            <!-- Add more content here -->
        </div>
    </div>
</body>

</html>
