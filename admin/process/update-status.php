<?php
include '../../includes/config.php';
include '../../includes/auth.php';

// Check if user is admin
if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipId = $conn->real_escape_string($_POST['tip_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $updateDetails = isset($_POST['update_details']) ? $conn->real_escape_string($_POST['update_details']) : null;
    $staffId = $_SESSION['user_id'];

    // Update tip status
    $updateQuery = "UPDATE tips SET status = '$status' WHERE id = '$tipId'";
    
    if ($conn->query($updateQuery)) {
        // Add status update record
        $insertQuery = "INSERT INTO status_updates (tip_id, staff_id, status, update_details) 
                        VALUES ('$tipId', '$staffId', '$status', '$updateDetails')";
        $conn->query($insertQuery);
        
        // Redirect back to view-tip page with success message
        header("Location: ../view-tip.php?id=$tipId&success=Status updated successfully");
        exit();
    } else {
        header("Location: ../view-tip.php?id=$tipId&error=Failed to update status");
        exit();
    }
} else {
    // If not POST request, redirect to tips listing
    header("Location: ../tips.php");
    exit();
}
?>