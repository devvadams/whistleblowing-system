<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Whistle Blowing System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
            <h2>Blower Login</h2>
        </div>
    </header>

    <main class="container">
        <form action="process/login-process.php" method="POST" class="login-form">
            <h3>Enter your credentials to login</h3>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required placeholder="Enter your registered email">
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Login</button>
            </div>
            
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
            <p><a href="forgot-password.php">Forgot password?</a></p>
        </form>
    </main>

    <script src="assets/js/script.js"></script>
</body>
</html>