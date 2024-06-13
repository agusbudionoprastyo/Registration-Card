<?php
require_once 'helper/connection.php';

if (isset($_POST['token_id'])) {
    $token_id = $_POST['token_id'];
    $query = mysqli_query($connection, "UPDATE token_device SET status = '1' WHERE token_id = '$token_id'");

    if ($query) {
        echo "Status updated successfully";
    } else {
        echo "Failed to update status";
    }
} else {
    echo "Token ID not provided";
}
$connection->close();
?>