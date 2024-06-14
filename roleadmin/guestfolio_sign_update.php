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

// Query untuk update gf_device_token di regform
$queryRegform = "UPDATE regform SET gf_device_token = ? WHERE id = ?";

// Query untuk update regform_id di token_device
$queryTokenDevice = "UPDATE token_device SET regform_id = ? WHERE token_id = ?";

// Persiapan statement untuk regform
if ($stmtRegform = mysqli_prepare($connection, $queryRegform)) {
    mysqli_stmt_bind_param($stmtRegform, "ii", $tokenId, $guestfolioId);
    mysqli_stmt_execute($stmtRegform);

    if (mysqli_stmt_affected_rows($stmtRegform) > 0) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Token device berhasil dikirim'
        ];
    } else {
        // Jika tidak ada perubahan data di regform, tetap set status ke success tapi ubah pesannya
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Token device berhasil dikirim'
        ];
    }

    mysqli_stmt_close($stmtRegform);
} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Kesalahan saat menyiapkan query regform: ' . mysqli_error($connection)
    ];
}

// Persiapan statement untuk token_device
if ($stmtTokenDevice = mysqli_prepare($connection, $queryTokenDevice)) {
    mysqli_stmt_bind_param($stmtTokenDevice, "ii", $guestfolioId, $tokenId);
    mysqli_stmt_execute($stmtTokenDevice);

    if (mysqli_stmt_affected_rows($stmtTokenDevice) > 0) {
        $_SESSION['info']['message'] .= ' dan siap untuk di tandatangani';
    } else {
        $_SESSION['info']['message'] .= ' Tidak ada perubahan data';
    }

    mysqli_stmt_close($stmtTokenDevice);
} else {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Kesalahan saat menyiapkan query token_device: ' . mysqli_error($connection)
    ];
}

header('Location: regform.php');
exit;