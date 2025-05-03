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

// Ricezione del valore dal dispositivo IoT in formato JSON
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Legge il JSON inviato dal client
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    // Controlla se il valore del potenziometro e lo stato del LED sono presenti
    if (isset($data["valore"]) && isset($data["led_status"])) {
        $statoPot = $data["valore"];
        $led_status = $data["led_status"];

        // Query per inserire il valore del potenziometro e lo stato del LED nel database
        $sql = "INSERT INTO sensor_data (valore, led_status) VALUES ('$statoPot', '$led_status')";

        if ($conn->query($sql) === TRUE) {
            echo "Dati salvati con successo";
        } else {
            echo "Errore: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Errore: valore o stato del LED non ricevuti";
    }
}

// Chiudi connessione
$conn->close();
?>
