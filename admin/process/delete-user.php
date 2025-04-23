<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_admin();

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: users.php?error=No user ID provided");
    exit();
}

$userId = intval($_GET['id']);

// Make sure the user exists
$checkQuery = "SELECT * FROM users WHERE id = $userId";
$checkResult = $conn->query($checkQuery);

if ($checkResult->num_rows === 0) {
    header("Location: users.php?error=User not found");
    exit();
}

// Prevent deleting yourself
if ($userId == $_SESSION['user_id']) {
    header("Location: users.php?error=You cannot delete your own account");
    exit();
}

// Step 1: Delete status updates related to the user's tips
$conn->query("DELETE su FROM status_updates su 
              JOIN tips t ON su.tip_id = t.id 
              WHERE t.user_id = $userId");

// Step 2: Delete tips submitted by the user
$conn->query("DELETE FROM tips WHERE user_id = $userId");

// Step 3: Now delete the user
$deleteQuery = "DELETE FROM users WHERE id = $userId";
if ($conn->query($deleteQuery)) {
    header("Location: ../users.php?success=User deleted successfully");
    exit();
} else {
    header("Location: ../users.php?error=Failed to delete user");
    exit();
}
?>
