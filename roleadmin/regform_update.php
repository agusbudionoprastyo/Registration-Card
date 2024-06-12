<?php
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
    // $pdfFilePath = __DIR__ . $row['at_regform'];
    $pdfFilePath = __DIR__ . '/' . $row['at_regform'];

    // Cek apakah file PDF ada
    if (file_exists($pdfFilePath)) {
        // Proses pembaruan ruangan di PDF
        if (updateRoomInPdf($pdfFilePath, $room)) {
            // Proses pembaruan ruangan di database
            if (updateRoomInDatabase($id, $room)) {
                http_response_code(200);
                echo "Regcard berhasil diupdate";
            } else {
                http_response_code(500);
                echo "Gagal menyimpan data.";
            }
        } else {
            http_response_code(500);
            echo "Gagal memperbarui PDF.";
        }
    } else {
        http_response_code(400);
        echo "File PDF tidak ditemukan.";
    }
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
            $pdf->Text(60, 25, $room); // Ubah posisi dan teks sesuai kebutuhan Anda
        }
    }

    // Simpan PDF ke jalur output
    $outputPdfPath = __DIR__ . '/../signed_doc/updated_regform.pdf';
    return $pdf->Output($outputPdfPath, 'F');
}

function updateRoomInDatabase($id, $room) {
    global $connection;

    // Mulai transaksi
    $connection->begin_transaction();

    try {
        // Prepare the SQL statement for updating the table regform
        $stmt = $connection->prepare("UPDATE regform SET room = ? WHERE id = ?");
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $connection->error);
        }

        $stmt->bind_param("ss", $room, $id);

        // Eksekusi pernyataan SQL
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data: " . $stmt->error);
        }

        // Komit transaksi
        $connection->commit();

        return true;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $connection->rollback();
        echo "Gagal menyimpan data : " . $e->getMessage();
        return false;
    } finally {
        // Menutup statement
        $stmt->close();
    }
}
?>