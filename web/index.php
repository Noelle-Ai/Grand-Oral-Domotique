<?php
$json = json_decode(file_get_contents("http://172.21.184.53:5000/sensors"), true);
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
        <?php
            if($json["success"]) {
                echo '<img src="https://http.cat/503" id="cat503"></img>';
                return;
            } else {
        ?>
        <div id="sidebar">
            <nav>
                <ul>
                    <li class="deroulant"><a>Menu</a>
                        <ul class="sousderoulant">
                            <li><a class="actualpage">Home</a></li>
                            <li><a href="camera.php">Caméra</a></li>
                            <?php
                                foreach($json["data"] as $key => $value) {
                            ?>
                                <div class="menu-item">
                                    <li>
                                        <a href="sensor.php?id=<? echo $value['id']; ?>"><? echo $value["name"] ?></a>
                                    </li>
                                    
                                    <button id="btn-<? echo $value["id"]; ?>" class="menuBtn">
                                        <img src="pencil.png" class="pencil TempInt"/>
                                    </button>
                                </div>
                            <?php
                                }
                            ?>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="content">
            <div class="grid">
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn1" class="on buttons" onclick='OnOff("#mybtn1")'>On</button>
                    </div>
                    <p>Lumière : Salon</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn2" class="on buttons" onclick='OnOff("#mybtn2")'>On</button>
                    </div>
                    <p>Lumière : Cuisine</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn3" class="on buttons" onclick='OnOff("#mybtn3")'>On</button>
                    </div>
                    <p>Lumière : Garage</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn4" class="on buttons" onclick='OnOff("#mybtn4")'>On</button>
                    </div>
                    <p>Lumière : Chambre 1</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn5" class="on buttons" onclick='OnOff("#mybtn5")'>On</button>
                    </div>
                    <p>Lumière : Chambre 2</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn6" class="on buttons" onclick='OnOff("#mybtn6")'>On</button>
                    </div>
                    <p>Volets : Salon</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn7" class="on buttons" onclick='OnOff("#mybtn7")'>On</button>
                    </div>
                    <p>Volets : Cuisine</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn8" class="on buttons" onclick='OnOff("#mybtn8")'>On</button>
                    </div>
                    <p>Volets : Garage</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn9" class="on buttons" onclick='OnOff("#mybtn9")'>On</button>
                    </div>
                    <p>Volets : Chambre 1</p>
                </div>
                <div class="block">
                    <div class="gridbuttons">
                        <button id="mybtn10" class="on buttons" onclick='OnOff("#mybtn10")'>On</button>
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
        <?php
            }
        ?>
    </body>
</html>