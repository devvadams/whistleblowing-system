<?php 
include 'includes/config.php';
require_once 'includes/auth.php'; // This will check if user is logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Tip - Whistle Blowing System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
            <h2>Submit Tip/Report</h2>
        </div>
    </header>

    <main class="container">
        <form action="process/tip-process.php" method="POST" enctype="multipart/form-data" class="tip-form">
            <h3>Report Suspected Corruption</h3>
            <p>Please provide detailed information about the person or activity you're reporting.</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="suspectName">Suspect Name *</label>
                <input type="text" id="suspectName" name="suspectName" required placeholder="Name of the person being reported">
            </div>
            
            <div class="form-group">
                <label for="suspectPosition">Suspect Position</label>
                <input type="text" id="suspectPosition" name="suspectPosition" placeholder="Position/Title of the suspect">
            </div>
            
            <div class="form-group">
                <label for="suspectOrganization">Suspect Organization</label>
                <input type="text" id="suspectOrganization" name="suspectOrganization" placeholder="Organization/Department of the suspect">
            </div>
            
            <div class="form-group">
                <label for="tipDetails">Report Details *</label>
                <textarea id="tipDetails" name="tipDetails" rows="6" required placeholder="Provide detailed information about the incident, including dates, locations, and any other relevant information"></textarea>
            </div>
            
            <div class="form-group">
                <label for="evidence">Upload Evidence (Optional)</label>
                <input type="file" id="evidence" name="evidence">
                <small>You can upload documents, images, or other files that support your report (Max 5MB)</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Submit Report</button>
            </div>
        </form>
    </main>

    <script src="assets/js/script.js"></script>
</body>
</html>