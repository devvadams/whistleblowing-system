<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $checkEmail = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        header("Location: ../signup.php?error=Email already exists");
        exit();
    }
    
    // Insert new user
    $query = "INSERT INTO users (first_name, last_name, email, phone, password) 
              VALUES ('$firstName', '$lastName', '$email', '$phone', '$password')";
    
    if ($conn->query($query)) {
        // Get the new user ID
        $userId = $conn->insert_id;
        
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        
        header("Location: ../submit-tip.php");
        exit();
    } else {
        header("Location: ../signup.php?error=Registration failed. Please try again.");
        exit();
    }
} else {
    header("Location: ../signup.php");
    exit();
}
?>