<?php
// Koneksi ke database dan ambil data
require_once 'helper/connection.php';

// Set header untuk SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Ambil device_token dari data GET
$deviceToken = $_GET['device_token'] ?? 'default_token'; // Gunakan default token jika tidak ada yang dikirim

// Ambil last_id dari data GET
$lastId = $_GET['last_id'] ?? 0; // Gunakan 0 sebagai default jika tidak ada yang dikirim

// Looping untuk mengirimkan pembaruan berkala
while (true) {
    $query = mysqli_query($connection, "SELECT * 
    FROM regform 
    WHERE gf_device_token = '$deviceToken'
    AND id = '$lastId'");
    $row = mysqli_fetch_array($query);

    // Kirim data ke klien
    echo "data: " . json_encode($row) . "\n\n";
    flush(); // Pastikan data terkirim

    // Tunggu beberapa waktu sebelum mengirimkan pembaruan berikutnya
    sleep(1); // Anda bisa sesuaikan waktu sesuai kebutuhan
}
?>