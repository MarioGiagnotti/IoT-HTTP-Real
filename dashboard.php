<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iot_database";

// Connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controllo connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Query per ottenere gli ultimi 10 dati
$sql = "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 10";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Ottieni lo stato più recente del LED
$sql_led_status = "SELECT led_status FROM sensor_data ORDER BY id DESC LIMIT 1";
$result_led_status = $conn->query($sql_led_status);
$led_status = ($result_led_status->num_rows > 0) ? $result_led_status->fetch_assoc()['led_status'] : 'spento';

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard IoT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function refreshPage() {
            location.reload();
        }
        setInterval(refreshPage, 1000);
    </script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Dashboard IoT</h2>
        
        <div class="card p-3 mt-4">
        <div class="row">
    <!-- Sezione Controllo LED -->
    <div class="col-6 text-center">
        <h4>Controllo LED</h4>
        <form method="get" action="control_led.php" class="d-inline">
            <input type="hidden" name="comando" value="1">
            <button type="submit" class="btn btn-success mb-2 w-100">Accendi</button>
        </form>
        <form method="get" action="control_led.php" class="d-inline w-100">
            <input type="hidden" name="comando" value="0">
            <button type="submit" class="btn btn-danger w-100">Spegni</button>
        </form>
    </div>

    <!-- Sezione Stato LED -->
    <div class="col-6 text-center">
        <h4>Stato LED</h4>
        <div class="d-flex justify-content-center align-items-center mt-2">
            <div id="ledCircle" class="rounded-circle" 
                 style="width: 50px; height: 50px; background-color: <?= $led_status == 'acceso' ? 'yellow' : '#ccc'; ?>;">
            </div>
        </div>
    </div>
</div>

</div>
        
        <div class="row">
            <!-- Tabella -->
            <div class="col-md-4">
                <div class="card p-3">
                    <h4 class="text-center">Valori e stato del LED</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Potenziometro</th>
                                <th>LED</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <td><?= $row['timestamp'] ?></td>
                                    <td><?= $row['valore'] ?></td>
                                    <td><?= $row['led_status'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Grafico -->
            <div class="col-md-8">
                <div class="card p-3">
                    <h4 class="text-center">Trend dei valori</h4>
                    <canvas id="iotChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let labels = <?= json_encode(array_column($data, 'timestamp')) ?>.reverse();
        let values = <?= json_encode(array_column($data, 'valore')) ?>.reverse();
        
        const ctx = document.getElementById('iotChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Valore Potenziometro',
                    data: values,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Tempo' } },
                    y: { title: { display: true, text: 'Valore' } }
                }
            }
        });
    </script>
</body>
</html>
