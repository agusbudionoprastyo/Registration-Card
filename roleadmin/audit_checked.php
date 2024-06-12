<?php
require_once '../helper/connection.php';
session_start();

$errorMsg = ""; // Initialize an error message variable

// Check if the user has the 'admin' role
if ($_SESSION['login']['role'] !== 'admin') {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => 'Unauthorized access: Insufficient privileges'
  ];
  header('Location: regform_guestfolio_audit.php');
  exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id === null || !is_numeric($id)) {
    $_SESSION['info'] = [
        'status' => 'failed',
        'message' => 'Invalid ID'
    ];
    header('Location: regform_guestfolio_audit.php');
    exit;
}

$query = "SELECT * FROM regform WHERE id = $id";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);

if ($row['id']) {
    // If row exists, update its status to 1
    $update_query = "UPDATE regform SET status = 1 WHERE id = $id";
    if (mysqli_query($connection, $update_query)) {
        echo "Regform status updated successfully.";
    } else {
        echo "Failed to update regform status.";
        exit;
    }
}

header('Location: regform_guestfolio_audit.php');
exit;
?>