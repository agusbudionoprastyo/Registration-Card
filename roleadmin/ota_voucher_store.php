<?php
session_start();
require_once '../helper/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    $targetDir = "../attachment_voucher/";
    $folio = isset($_POST['folio']) ? $_POST['folio'] : ''; // Get folio from POST data

    // Extract file extension from uploaded file
    $extension = pathinfo($_FILES["voucher"]["name"], PATHINFO_EXTENSION);

    // Construct filename using folio and original file extension
    $fileName = "voucher_" . $folio . "." . $extension;
    $targetFilePath = $targetDir . $fileName;

    if (!empty($_FILES["voucher"]["name"])) {
        if (move_uploaded_file($_FILES["voucher"]["tmp_name"], $targetFilePath)) {
            $updateQuery = "UPDATE regform SET at_ota_voucher = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $targetFilePath, $id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $_SESSION['info'] = [
                    'status' => 'success',
                    'message' => 'Voucher successfully saved.'
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