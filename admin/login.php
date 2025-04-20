<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    // Check if user exists and is admin
    $query = "SELECT * FROM users WHERE email = '$email' AND role = 'admin'";
    $result = $conn->query($query);
    
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        if (password_verify($password, $admin['password'])) {
            // Set admin session variables
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_email'] = $admin['email'];
            $_SESSION['user_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
            $_SESSION['user_role'] = 'admin';
            
            header("Location: dashboard.php");
            exit();
        }
    }
    
    // If we get here, login failed
    header("Location: login.php?error=Invalid credentials or not an admin");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Whistle Blowing System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-login {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .admin-login h2 {
            color: #1a5276;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
        </div>
    </header>

    <main class="container">
        <div class="admin-login">
            <h2>Admin Login</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter admin email">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Login</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>