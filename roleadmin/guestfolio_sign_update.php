<?php
session_start();
require_once '../helper/connection.php';

// Fungsi untuk memeriksa apakah tabel 'guestfolio_token' kosong
function isguestfolio_tokenTableEmpty($connection) {
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM guestfolio_token");
    $row = mysqli_fetch_assoc($result);
    return $row['count'] == 0;
}

// Fungsi untuk menambahkan data ke tabel 'guestfolio_token'
function addDataToguestfolio_tokenTable($connection, $id) {
    $id = mysqli_real_escape_string($connection, $id);
    $query = "INSERT INTO guestfolio_token (device_id) VALUES ('$id')";
    return mysqli_query($connection, $query);
}

// Fungsi untuk menghapus semua entri dari tabel 'guestfolio_token'
function truncateguestfolio_tokenTable($connection) {
    $query = "TRUNCATE TABLE guestfolio_token";
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

if (!isguestfolio_tokenTableEmpty($connection)) {
    // Jika tabel tidak kosong, hapus semua entri
    if (truncateguestfolio_tokenTable($connection)) {
        echo "'guestfolio_token' UnSynch.";
    } else {
        echo "Gagal mengosongkan tabel 'guestfolio_token'.";
        exit;
    }
}

// Tambahkan data ke tabel 'guestfolio_token'
if (addDataToguestfolio_tokenTable($connection, $id)) {
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'Guestfolio terkirim ke tablet'
    ];
} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Gagal menambahkan Guestfolio ke tablet'
    ];
}

header('Location: regform.php');
exit;
?>