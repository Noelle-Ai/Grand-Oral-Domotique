<?php
$json = json_decode(file_get_contents("http://172.21.184.53:5000/sensors"), true);

if(!$json["success"]) {
    echo '<img src="https://http.cat/503"></img>';
    return;
} 
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
                            <li><a href="index.php">Home</a></li>
                            <li><a class="actualpage">Caméra</a></li>
                            <?php
                                foreach($json["data"] as $key => $value) {
                            ?>
                                <div class="menu-item">
                                    <li>
                                        <a href="sensor.php?id=<? echo $value[id]; ?>"><? echo $value["name"] ?></a>
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

            <img src="http://172.21.184.53:5000/stream" id="cam"/>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

          <script src="./script.js"></script>
        </div>
    </body>
</html>