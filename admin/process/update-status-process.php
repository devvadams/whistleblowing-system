<?php
include '../../includes/config.php';
include '../../includes/auth.php';

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipId = $conn->real_escape_string($_GET['id']);
    $status = $conn->real_escape_string($_POST['status']);
    $notes = isset($_POST['notes']) ? $conn->real_escape_string($_POST['notes']) : '';
    $adminId = $_SESSION['user_id'];

    // Update the tip status
    $updateQuery = "UPDATE tips SET status = '$status' WHERE id = '$tipId'";
    
    if ($conn->query($updateQuery)) {
        // Record the status change
        $logQuery = "INSERT INTO status_updates (tip_id, admin_id, status, notes) 
                     VALUES ('$tipId', '$adminId', '$status', '$notes')";
        $conn->query($logQuery);
        
        header("Location: ../tips.php?success=Status updated successfully");
        exit();
    } else {
        header("Location: ../update-status.php?id=$tipId&error=Update failed");
        exit();
    }
} else {
    header("Location: ../tips.php");
    exit();
}
?>