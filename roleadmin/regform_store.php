<?php
session_start();
require_once '../helper/connection.php';

// Validate the form inputs
$nama = trim($_POST['nama'] ?? '');
$tempat_tanggal_lahir = trim($_POST['tempat_tanggal_lahir'] ?? '');
$jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$no_telp = trim($_POST['no_telp'] ?? '');
$folio = trim($_POST['folio'] ?? '');
$room = trim($_POST['room'] ?? '');
$dateci = trim($_POST['dateci'] ?? '');
$dateco = trim($_POST['dateco'] ?? '');
$roomtype = trim($_POST['roomtype'] ?? '');

// Deklarasi direktori tempat menyimpan file PDF yang diunggah
$target_dir = "../attachement_pdf/";

// Extract file extension from uploaded file
$extension = pathinfo($_FILES["pdfFile"]["name"], PATHINFO_EXTENSION);

// Construct filename using folio and original file extension
$fileName = "regcard_" . $folio . "_unsigned." . $extension;
$targetFilePath = $target_dir . $fileName;

// Pindahkan file PDF yang diunggah ke lokasi yang ditentukan
if (move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $targetFilePath)) {
    // File berhasil diunggah, simpan informasi ke database
    $sql = "INSERT INTO regform (nama, tempat_tanggal_lahir, jenis_kelamin, alamat, no_telp, folio, room, dateci, dateco, roomtype, at_regform) VALUES ('$nama', '$tempat_tanggal_lahir', '$jenis_kelamin', '$alamat', '$no_telp', '$folio', '$room', '$dateci', '$dateco', '$roomtype', '$targetFilePath')";

    if (mysqli_query($connection, $sql)) {
        // Redirect kembali ke halaman regform.php jika penyimpanan berhasil
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Regcard successfully saved. klik <i class="fa-solid fa-paper-plane fa-xl"></i> untuk send to tablet & ditandatangani'
        ];
        header("Location: regform.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }
} else {
    echo "Maaf, terjadi kesalahan saat mengunggah file.";
}
?>