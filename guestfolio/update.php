<?php
// Koneksi ke database dan ambil data
require_once 'helper/connection.php';

// Set header untuk SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Ambil device_token dari POST data
$deviceToken = $_POST['device_token'] ?? 'default_token'; // Gunakan default token jika tidak ada yang dikirim

// Looping untuk mengirimkan pembaruan berkala
while (true) {
    // Ambil data dari database
    $query = mysqli_query($connection, "SELECT *
    FROM regform
    WHERE device_token = '$deviceToken'");
    $row = mysqli_fetch_array($query);

    // Kirim data ke klien
    echo "data: " . json_encode($row) . "\n\n";
    flush(); // Pastikan data terkirim

    // Tunggu beberapa waktu sebelum mengirimkan pembaruan berikutnya
    sleep(1); // Anda bisa sesuaikan waktu sesuai kebutuhan
}
?>