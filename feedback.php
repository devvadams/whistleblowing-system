<?php 
include 'includes/config.php';
include 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Whistle Blowing System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
            <h2>Report Feedback</h2>
        </div>
    </header>

    <main class="container">
        <form class="feedback-form" method="GET">
            <h3>Check Report Status</h3>
            <p>Enter your reference number to check the status of your report</p>
            
            <div class="form-group">
                <label for="referenceNumber">Reference Number *</label>
                <input type="text" id="referenceNumber" name="referenceNumber" required placeholder="Enter your report reference number">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Check Status</button>
            </div>
        </form>

        <?php
        if (isset($_GET['referenceNumber'])) {
            $refNumber = $conn->real_escape_string($_GET['referenceNumber']);
            $userId = $_SESSION['user_id'];
            
            // Query to get tip details and status updates
            $query = "SELECT t.*, su.status as update_status, su.update_details, su.created_at as update_date 
                      FROM tips t
                      LEFT JOIN status_updates su ON t.id = su.tip_id
                      WHERE t.reference_number = '$refNumber' AND t.user_id = $userId
                      ORDER BY su.created_at DESC";
            
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                $tip = $result->fetch_assoc();
                echo '<div class="status-container">';
                echo '<h4>Report Status: <span class="status-'.strtolower(str_replace(' ', '-', $tip['status'])).'">'.$tip['status'].'</span></h4>';
                echo '<p><strong>Reference Number:</strong> '.$tip['reference_number'].'</p>';
                echo '<p><strong>Suspect Name:</strong> '.htmlspecialchars($tip['suspect_name']).'</p>';
                echo '<p><strong>Date Submitted:</strong> '.date('F j, Y H:i', strtotime($tip['created_at'])).'</p>';
                
                if ($result->num_rows > 1) {
                    echo '<h5>Status Updates:</h5>';
                    echo '<div class="status-updates">';
                    while ($update = $result->fetch_assoc()) {
                        if ($update['update_status']) {
                            echo '<div class="update">';
                            echo '<p><strong>'.date('F j, Y H:i', strtotime($update['update_date'])).'</strong></p>';
                            echo '<p>Status: '.$update['update_status'].'</p>';
                            if ($update['update_details']) {
                                echo '<p>Details: '.htmlspecialchars($update['update_details']).'</p>';
                            }
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                }
                
                echo '</div>';
            } else {
                echo '<div class="alert alert-error">No report found with that reference number.</div>';
            }
        }
        ?>
    </main>

    <script src="assets/js/script.js"></script>
</body>
</html>