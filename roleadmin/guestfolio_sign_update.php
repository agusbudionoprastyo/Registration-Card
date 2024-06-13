<?php
session_start();
require_once '../helper/connection.php';

$tokenId = isset($_GET['token_id']) ? $_GET['token_id'] : null;
$guestfolioId = isset($_GET['guestfolio_id']) ? $_GET['guestfolio_id'] : null;

if ($tokenId === null || $guestfolioId === null || !is_numeric($tokenId) || !is_numeric($guestfolioId)) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'ID tidak valid atau tidak lengkap'
    ];
    header('Location: regform.php');
    exit;
}

// Query untuk update
$query = "UPDATE regform SET gf_device_token = ? WHERE id = ?";

// Persiapan statement untuk keamanan
if ($stmt = mysqli_prepare($connection, $query)) {
    mysqli_stmt_bind_param($stmt, "ii", $tokenId, $guestfolioId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Token perangkat berhasil diperbarui.'
        ];
    } else {
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'Tidak ada perubahan data atau update gagal.'
        ];
    }

    mysqli_stmt_close($stmt);
} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Kesalahan saat menyiapkan query: ' . mysqli_error($connection)
    ];
}

header('Location: regform.php');
exit;
?>