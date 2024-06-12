<?php
session_start();
require_once '../helper/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Deklarasi direktori tempat menyimpan file PDF yang diunggah
    $target_dir = "../attachement_pdf/";

    $folio = isset($_POST['folio']) ? $_POST['folio'] : ''; // Get folio from POST data

    // Extract file extension from uploaded file
    $extension = pathinfo($_FILES["pdfFile"]["name"], PATHINFO_EXTENSION);

    // Construct filename using folio and original file extension
    $fileName = "guestfolio_" . $folio . "_unsigned." . $extension;
    $targetFilePath = $target_dir . $fileName;

    if (!empty($_FILES["pdfFile"]["name"])) {
        if (move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $targetFilePath)) {
            $updateQuery = "UPDATE regform SET at_guestfolio = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $targetFilePath, $id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $_SESSION['info'] = [
                    'status' => 'success',
                    'message' => 'Guestbill successfully saved. klik <i class="fa-solid fa-paper-plane fa-xl"></i> untuk send to tablet & ditandatangani'
                ];
                header("Location: regform.php");
                exit();
            } else {
                $_SESSION['info'] = [
                    'status' => 'error',
                    'message' => 'Failed to update the database.'
                ];
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['info'] = [
                'status' => 'error',
                'message' => 'Sorry, there was an error uploading your file.'
            ];
        }
    } else {
        $_SESSION['info'] = [
            'status' => 'error',
            'message' => 'Please select a file to upload.'
        ];
    }
}

// If the script reaches here, it means there was an error.
header("Location: regform.php");
exit();
?>