<?php
include '../includes/config.php';
require_once '../includes/auth.php';

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: tips.php");
    exit();
}

$tipId = $conn->real_escape_string($_GET['id']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'process/update-status-process.php'; // Ensure this path is correct
    exit();
}

// Rest of your form code...
?>