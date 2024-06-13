<?php
header("Access-Control-Allow-Origin: https://guestfolio.dafam.cloud");
require_once 'helper/connection.php'; // Memuat file connection.php
require_once 'vendor/autoload.php'; // Memuat TCPDF dan FPDI

use setasign\Fpdi\Tcpdf\Fpdi;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $signatureData = $_POST['signature'];
    $id = $_POST['id'];
    $pdfFile = $_POST['pdfFile'];
    $folio = $_POST['folio'];

    // Dekode data tanda tangan dari base64 menjadi gambar
    $decodedSignature = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
    $signatureFilename = 'signature_' . uniqid() . '.png';
    $signatureFilePath = __DIR__ . '/signature/' . $signatureFilename;

    // Cek apakah direktori 'signature' ada, jika tidak, buat direktori tersebut
    if (!is_dir(__DIR__ . '/signature')) {
        mkdir(__DIR__ . '/signature', 0775, true);
    }

    // Simpan tanda tangan ke direktori server
    file_put_contents($signatureFilePath, $decodedSignature);

    $inputPdfFilename = __DIR__ . '/attachement_pdf/' . $pdfFile;
    $outputPdfFilename = 'guestfolio_' . $folio . '_signed.pdf';
    $outputPdfFilePath = __DIR__ . '/signed_doc/' . $outputPdfFilename;
    $at_guestfolio = '../signed_doc/' . $outputPdfFilename;

    // Cek apakah direktori 'signed_doc' ada, jika tidak, buat direktori tersebut
    if (!is_dir(__DIR__ . '/signed_doc')) {
        mkdir(__DIR__ . '/signed_doc', 0775, true);
    }

    // Tambahkan tanda tangan ke PDF
    addSignatureToPdf($inputPdfFilename, $signatureFilePath, $outputPdfFilePath);

    // Memeriksa koneksi (gunakan $connection dari connection.php)
    if (!$connection) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }

    // Mulai transaksi
    $connection->begin_transaction();

    try {
        // Prepare the SQL statement for updating the table regform
        $stmt = $connection->prepare("UPDATE regform SET g_signature_path = ?, at_guestfolio = ?, gf_device_token = NULL WHERE id = ?");
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $connection->error);
        }

        $stmt->bind_param("sss", $signatureFilename, $at_guestfolio, $id);

        // Eksekusi pernyataan SQL
        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data: " . $stmt->error);
        }

        // Komit transaksi
        $connection->commit();

        http_response_code(200);
        echo "Data berhasil disimpan.";
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $connection->rollback();
        http_response_code(500);
        echo "Gagal menyimpan data: " . $e->getMessage();
    }

    // Menutup statement dan koneksi
    if (isset($stmt)) {
        $stmt->close();
    }
    $connection->close();
} else {
    http_response_code(405);
    echo "Metode tidak diizinkan.";
}

function addSignatureToPdf($inputPdfPath, $signatureImagePath, $outputPdfPath) {
    $pdf = new FPDI();
    $pageCount = $pdf->setSourceFile($inputPdfPath);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $pdf->AddPage();
        $pdf->useTemplate($templateId);

        if ($pageNo == $pageCount) {
            $x = 160;
            $y = 190;
            $pdf->Image($signatureImagePath, $x, $y, 40, 20, 'PNG');
        }
    }

    // Simpan PDF ke jalur output
    $pdf->Output($outputPdfPath, 'F');
}
?>