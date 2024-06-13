<?php
// Koneksi ke database
$host = "localhost"; // atau alamat server lain
$username = "username_db";
$password = "password_db";
$database = "nama_database";

$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query untuk mengambil token_id dari device yang statusnya 'unpaired'
$sql = "SELECT token_id FROM device WHERE status = 'unpaired' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data dari setiap baris
    while($row = $result->fetch_assoc()) {
        echo $row["token_id"];
    }
} else {
    echo "No unpaired devices found";
}
$conn->close();
?>