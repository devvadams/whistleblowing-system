<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Whistle Blowing System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
            <h2>Create Blower Account</h2>
        </div>
    </header>

    <main class="container">
        <form action="process/signup-process.php" method="POST" class="signup-form">
            <h3>Please fill all required information (*)</h3>
            
            <div class="form-group">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="firstName" required placeholder="Enter your first name">
            </div>
            
            <div class="form-group">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="lastName" required placeholder="Enter your last name">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required placeholder="Enter a valid email address">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" required placeholder="Enter a valid phone number">
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required placeholder="Enter password">
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password *</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm password">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Create Account</button>
            </div>
            
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </main>

    <script src="assets/js/script.js"></script>
</body>
</html>