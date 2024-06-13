<?php
// Koneksi ke database dan ambil data
require_once 'helper/connection.php';

// Query untuk mengambil token_id dari device yang statusnya 'unpaired'
$query = mysqli_query($connection, "SELECT token_id FROM token_device WHERE status = '0' LIMIT 1");
$row = mysqli_fetch_array($query);

if ($row->num_rows > 0) {
    // output data dari setiap baris
    while($row = $result->fetch_assoc()) {
        echo $row["token_id"];
    }
} else {
    echo "No unpaired devices found";
}
$conn->close();
?>