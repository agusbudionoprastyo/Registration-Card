<?php
session_start();
require_once '../helper/connection.php'; // Memuat file connection.php
require_once '../vendor/autoload.php'; // Memuat TCPDF dan FPDI

use setasign\Fpdi\Tcpdf\Fpdi;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';

    // Mengambil path file PDF dari database
    $pdfPathQuery = $connection->prepare("SELECT at_regform FROM regform WHERE id = ?");
    $pdfPathQuery->bind_param("s", $id);
    $pdfPathQuery->execute();
    $result = $pdfPathQuery->get_result();
    $row = $result->fetch_assoc();
    $pdfFilePath = __DIR__ . '/' . $row['at_regform'];

    // Cek apakah file PDF ada
    if (file_exists($pdfFilePath)) {
        // Proses pembaruan ruangan di PDF
        if (updateRoomInPdf($pdfFilePath, $room)) {
            // Proses pembaruan ruangan di database
            if (updateRoomInDatabase($connection, $id, $room)) {
                http_response_code(200);
                $_SESSION['info'] = [
                    'status' => 'success',
                    'message' => 'Regcard berhasil diupdate'
                ];
            } else {
                http_response_code(500);
                $_SESSION['info'] = [
                    'status' => 'failed',
                    'message' => 'Gagal menyimpan data.'
                ];
            }
        } else {
            http_response_code(500);
            $_SESSION['info'] = [
                'status' => 'failed',
                'message' => 'Gagal memperbarui PDF.'
            ];
        }
    } else {
        http_response_code(400);
        $_SESSION['info'] = [
            'status' => 'failed',
            'message' => 'File PDF tidak ditemukan.'
        ];
    }
    header('Location: regform.php');
}

function updateRoomInPdf($pdfFilePath, $room) {
    $pdf = new Fpdi();
    $pageCount = $pdf->setSourceFile($pdfFilePath);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $pdf->AddPage();
        $pdf->useTemplate($templateId);

        if ($pageNo == $pageCount) {
            $pdf->SetFont('', '', 9); // Set font size to 9
            $pdf->Text(51, 40, $room); // Ubah posisi dan teks sesuai kebutuhan Anda
        }
    }

    // Simpan PDF ke jalur output
    $outputPdfPath = __DIR__ . '/../signed_doc/updated_regform.pdf';
    return $pdf->Output($outputPdfPath, 'F');
}

function updateRoomInDatabase($connection, $id, $room) {
    $updateQuery = "UPDATE regform SET room = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ss", $room, $id);
    return mysqli_stmt_execute($stmt);
}
?>