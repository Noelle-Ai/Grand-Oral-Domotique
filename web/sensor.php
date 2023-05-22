<?php
$id = $_GET['id'];

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
                            <li><a href="camera.php">Caméra</a></li>
                            <?php
                                foreach($json["data"] as $key => $value) {
                            ?>
                                <div class="menu-item">
                                    <li>
                                        <a href="sensor.php?id=<? echo $value['id']; ?>" class="<? if($value['id'] == $id) { echo "actualpage"; } ?>"><? echo $value["name"] ?></a>
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
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

          <script>
    $(document).ready(() => {
    Chart.defaults.color = '#FFF';
    const ctx = document.getElementById("chart_id");

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: "title",
                        data: [],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                pointStyle: false,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                },
                plugins: {
                    decimation: {
                        enabled: true
                    }
                }
            }
        });
    
        fetchData = () => {
            $.ajax({
                url: "http://172.21.184.53:5000/data",
                type: "POST",
                data: JSON.stringify({
                    "name": "<? echo $id; ?>",
                    "duration": "7d"
                }),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: (json) => {
                    values = json.data.map(data => data.value.toFixed(2));
    
                    labels = []
                    for(let i in json.data) {
                        if(i % 3 != 0) {
                            labels[i] = "";
                        }
                        else {
                            const date = new Date(json.data[i].time);
                            labels[i] = date.getHours() + ":" + date.getMinutes()
                        }
                    }
    
                    chart.data.datasets[0].data = values;
                    chart.data.labels = labels;
                    chart.update()
                },
                error: err => console.error(err)
            })
        }
    
        fetchData();
        setInterval(fetchData, 5000)
})
    </script>
        </div>
    </body>
</html>