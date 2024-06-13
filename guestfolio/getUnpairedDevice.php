<?php
// Koneksi ke database dan ambil data
require_once 'helper/connection.php';

// Query untuk mengambil token_id dari device yang statusnya 'unpaired'
$query = mysqli_query($connection, "SELECT token_id FROM token_device WHERE status = '0' LIMIT 1");

if (mysqli_num_rows($query) > 0) {
    // output data dari setiap baris
    while($row = mysqli_fetch_assoc($query)) {
        echo $row["token_id"];
    }
} else {
    echo "No unpaired devices found";
}
$connection->close();
?>