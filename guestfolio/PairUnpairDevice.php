<?php
require_once 'helper/connection.php';

if (isset($_POST['token_id']) && isset($_POST['status'])) {
    $token_id = $_POST['token_id'];
    $status = $_POST['status']; // Menerima status dari permintaan POST

    // Memperbarui query untuk menggunakan status yang diterima dari permintaan dan mengatur regform_id menjadi 0
    $query = mysqli_query($connection, "UPDATE token_device SET status = '$status', regform_id = 0 WHERE token_id = '$token_id'");

    if ($query) {
        echo "Status updated successfully";
    } else {
        echo "Failed to update status";
    }
} else {
    echo "Token ID or status not provided";
}
$connection->close();
?>