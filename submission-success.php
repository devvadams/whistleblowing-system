<?php
include 'includes/config.php';
include 'includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get reference number from URL or session
$referenceNumber = isset($_GET['ref']) ? $_GET['ref'] : (isset($_SESSION['last_reference']) ? $_SESSION['last_reference'] : null);

if (!$referenceNumber) {
    header("Location: submit-tip.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Submitted - Whistle Blowing System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
            <h2>Report Submission Successful</h2>
        </div>
    </header>

    <main class="container">
        <div class="submission-success">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h3>Your Report Has Been Submitted Successfully</h3>
            
            <div class="reference-box">
                <p>Your Reference Number:</p>
                <div class="reference-number"><?php echo htmlspecialchars($referenceNumber); ?></div>
                <small>Please keep this number safe to track your report</small>
            </div>
            
            <div class="next-steps">
                <h4>What Happens Next?</h4>
                <ol>
                    <li>Your report will be reviewed by our team</li>
                    <li>You can check the status using your reference number</li>
                    <li>We may contact you for additional information</li>
                </ol>
            </div>
            
            <div class="action-buttons">
                <a href="feedback.php?referenceNumber=<?php echo urlencode($referenceNumber); ?>" class="btn">
                    <i class="fas fa-search"></i> Check Status Now
                </a>
                <a href="submit-tip.php" class="btn btn-secondary">
                    <i class="fas fa-plus"></i> Submit Another Report
                </a>
            </div>
        </div>
    </main>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>