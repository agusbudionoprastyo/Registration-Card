<?php
session_start();
require_once '../helper/connection.php';

// Fungsi untuk memeriksa apakah tabel 'device' kosong
function isDeviceTableEmpty($connection) {
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM device");
    $row = mysqli_fetch_assoc($result);
    return $row['count'] == 0;
}

// Fungsi untuk menambahkan data ke tabel 'device'
function addDataToDeviceTable($connection, $id) {
    $id = mysqli_real_escape_string($connection, $id);
    $query = "INSERT INTO device (device_id) VALUES ('$id')";
    return mysqli_query($connection, $query);
}

// Fungsi untuk menghapus semua entri dari tabel 'device'
function truncateDeviceTable($connection) {
    $query = "TRUNCATE TABLE device";
    return mysqli_query($connection, $query);
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id === null || !is_numeric($id)) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Invalid ID'
    ];
    header('Location: regform.php');
    exit;
}

if (!isDeviceTableEmpty($connection)) {
    // Jika tabel tidak kosong, hapus semua entri
    if (truncateDeviceTable($connection)) {
        echo "'device' UnSynch.";
    } else {
        echo "Gagal mengosongkan tabel 'device'.";
        exit;
    }
}

// Tambahkan data ke tabel 'device'
if (addDataToDeviceTable($connection, $id)) {
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'Registration card terkirim ke tablet'
    ];
} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Gagal menambahkan registration card ke tablet'
    ];
}

header('Location: regform.php');
exit;
?>