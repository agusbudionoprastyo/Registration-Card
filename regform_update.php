<?php
session_start();
require_once 'helper/connection.php';
require_once 'vendor/autoload.php'; // Load FPDI library

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate the form inputs
    $id = trim($_POST['id'] ?? '');
    $room = trim($_POST['room'] ?? '');

    // Prepare the SQL statement for updating the room in regform table
    $sql = "UPDATE regform SET room = ? WHERE id = ?";

    // Prepare and bind parameters
    if ($stmt = mysqli_prepare($connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $room, $id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Fetch path file PDF from database
            $pdfFilePath = getPdfFilePath($id);

            if ($pdfFilePath) {
                // Update room in PDF file
                updateRoomInPdf($pdfFilePath, $room);

                $_SESSION['info'] = [
                    'status' => 'success',
                    'message' => 'Regcard successfully updated.'
                ];
                header("Location: roleadmin/regform.php");
                exit();
            } else {
                echo "Error: PDF file path not found.";
            }
        } else {
            echo "Error updating record: " . mysqli_error($connection);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Prepare failed: " . mysqli_error($connection);
    }

    // Close connection
    mysqli_close($connection);
} else {
    http_response_code(405);
    echo "Method Not Allowed.";
}

function getPdfFilePath($id) {
    global $connection;
    $sql = "SELECT at_regform FROM regform WHERE id = ?";
    if ($stmt = mysqli_prepare($connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $pdfFilePath);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $pdfFilePath;
    } else {
        echo "Prepare failed: " . mysqli_error($connection);
        return false;
    }
}

function updateRoomInPdf($pdfFilePath, $room) {
    // Initialize FPDI
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();

    // Get the number of pages
    $pageCount = $pdf->setSourceFile($pdfFilePath);

    // Loop through each page
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        // Import page
        $templateId = $pdf->importPage($pageNo);
        $pdf->AddPage();

        // Use imported page
        $pdf->useTemplate($templateId);

        $pdf->SetFont('', '', 9); // Set font size to 9
        $pdf->Text(37, 56, $room);
    }

    // Output updated PDF file
    $pdf->Output($pdfFilePath, 'F');
}
?>