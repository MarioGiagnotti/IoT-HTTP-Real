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

// Gestione del comando per accendere/spegnere il motore
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Ricezione del valore dell'interruttore (1 per accendere, 0 per spegnere)
    if (isset($_GET['comando'])) {
        $comando = $_GET['comando'];  // Il comando può essere 1 o 0
        // Invio del comando al dispositivo IoT (MCU)
        // Invio di una richiesta HTTP all'MCU 
        $url = "http://127.0.0.1:8765/control_led?comando=$comando";  // Cambiare l'IP se necessario
        file_get_contents($url);  // Invio della richiesta GET al dispositivo MCU
        header("Location: dashboard.php");  // Reindirizzamento alla dashboard
    }
}

// Chiudi connessione
$conn->close();
?>
