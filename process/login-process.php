<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    // Get user from database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            // Redirect to dashboard or submit tip page
            header("Location: ../submit-tip.php");
            exit();
        } else {
            header("Location: ../login.php?error=Invalid email or password");
            exit();
        }
    } else {
        header("Location: ../login.php?error=Invalid email or password");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>