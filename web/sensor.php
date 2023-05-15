<?php
$id = $_GET['id'];

echo $id;
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Connected House</title>
        <link rel="shortcut icon" href="house.png" />
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>
        <div id="sidebar">
            <nav>
                <ul>
                    <li class="deroulant"><a href="#" >Menu</a>
                        <ul class="sousderoulant">
                            <li><a href="index.html">Home</a></li>
                            <li><a href="#">Caméra</a></li>
                            <div class="color"><li><a href="#">Température Intérieur</a></li><img src="pencil.png" class="pencil"/></div>
                            <div><li><a href="#">Temp Extérieur</a></li><img src="pencil.png" class="pencil"/></div>
                            <div><li><a href="#">Humidité</a></li><img src="pencil.png" class="pencil"/></div>
                            <div><li><a href="#">Luminosité</a></li><img src="pencil.png" class="pencil"/></div>
                        </ul>
                    </li>
                </ul>

            </nav>
        </div>
        <div id="content">
            <div class="grid">
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn1" class="on" onclick='OnOff("#mybtn1")'>On</button>
                    </div>
                    <p>Lumière : Salon</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn2" class="on" onclick='OnOff("#mybtn2")'>On</button>
                    </div>
                    <p>Lumière : Cuisine</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn3" class="on" onclick='OnOff("#mybtn3")'>On</button>
                    </div>
                    <p>Lumière : Garage</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn4" class="on" onclick='OnOff("#mybtn4")'>On</button>
                    </div>
                    <p>Lumière : Chambre 1</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn5" class="on" onclick='OnOff("#mybtn5")'>On</button>
                    </div>
                    <p>Lumière : Chambre 2</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn6" class="on" onclick='OnOff("#mybtn6")'>On</button>
                    </div>
                    <p>Volets : Salon</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn7" class="on" onclick='OnOff("#mybtn7")'>On</button>
                    </div>
                    <p>Volets : Cuisine</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn8" class="on" onclick='OnOff("#mybtn8")'>On</button>
                    </div>
                    <p>Volets : Garage</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn9" class="on" onclick='OnOff("#mybtn9")'>On</button>
                    </div>
                    <p>Volets : Chambre 1</p>
                </div>
                <div class="block">
                    <div class="buttons">
                        <button id="mybtn10" class="on" onclick='OnOff("#mybtn10")'>On</button>
                    </div>
                    <p>Volets : Chambre 2</p>
                </div>
            </div>
            <div class="stats">
            <div class="Graph1">
                <canvas id="myChart"></canvas>
            </div>
            <div class="Graph1">
                <canvas id="myChart2"></canvas>
            </div>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

          <script src="./script.js"></script>
          </div>
        </div>
    </body>
</html>