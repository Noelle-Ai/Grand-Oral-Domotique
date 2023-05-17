<?php
$id = $_GET['id'];

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
                    <li class="deroulant"><a>Menu</a>
                        <ul class="sousderoulant">
                            <li><a href="index.html">Home</a></li>
                            <li><a href="#">Caméra</a></li>
                            <div class="menu-item"><li><a href="sensor.php">Température Intérieur</a></li><img src="pencil.png" class="pencil"/></div>
                            <div class="menu-item"><li><a href="#">Temp Extérieur</a></li><img src="pencil.png" class="pencil"/></div>
                            <div class="menu-item"><li><a href="#">Humidité</a></li><img src="pencil.png" class="pencil"/></div>
                            <div class="menu-item"><li><a href="#">Luminosité</a></li><img src="pencil.png" class="pencil"/></div>
                        </ul>
                    </li>
                </ul>

            </nav>
        </div>
        <div id="content">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

          <script src="./script.js"></script>
        </div>
    </body>
</html>