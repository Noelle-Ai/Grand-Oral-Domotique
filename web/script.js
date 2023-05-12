$(document).ready(() => {
    Chart.defaults.color = '#FFF';
    function setupGraph(chart_id, data_name, title) {
        const ctx = document.getElementById(chart_id);

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: title,
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
    
        my_func = () => {
            $.ajax({
                url: "http://172.21.184.53:5000/data",
                type: "POST",
                data: JSON.stringify({
                    "name": data_name,
                    "duration": "7d"
                }),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: (json) => {
                    values = json.data.map(data => data.value.toFixed(2));
    
                    labels = []
                    for(let i in json.data) {
                        console.log(i)
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
    
        my_func();
        setInterval(my_func, 5000)
    }

    setupGraph("myChart", "temp_int", "Temperature Intérieur (°C)")
    setupGraph("myChart2", "temp_ext", "Temperature Exterieur (°C)")
})


function OnOff(id) {
    const btn = document.querySelector(id);
        if (btn.className == "on") {
            btn.className = "off";
            btn.textContent = "Off";
        } else if (btn.className == "off") {
            btn.className = "on";
            btn.textContent = "On";
        }
}