<?php 
include 'includes/config.php';
require_once 'includes/auth.php';

// Check if reference number came from URL
$referenceNumber = isset($_GET['referenceNumber']) ? trim($_GET['referenceNumber']) : '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $referenceNumber = trim($_POST['referenceNumber']);
    header("Location: feedback.php?referenceNumber=" . urlencode($referenceNumber));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Report Status - Whistle Blowing System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
            <h2>Report Status Check</h2>
        </div>
    </header>

    <main class="container">
        <div class="feedback-container">
            <form method="POST" class="feedback-form">
                <h3>Check Your Report Status</h3>
                <p>Enter your reference number to view the current status of your report</p>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="referenceNumber">Reference Number *</label>
                    <input type="text" id="referenceNumber" name="referenceNumber" 
                           value="<?php echo htmlspecialchars($referenceNumber); ?>" 
                           required placeholder="Enter your report reference number">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i> Check Status
                    </button>
                </div>
            </form>

            <?php
            // Display status if reference number is provided
            if (!empty($referenceNumber)) {
                $refNumber = $conn->real_escape_string($referenceNumber);
                $userId = $_SESSION['user_id'];
                
                // Query to get tip details and status updates
                $query = "SELECT t.*, su.status as update_status, su.update_details, su.created_at as update_date 
                          FROM tips t
                          LEFT JOIN status_updates su ON t.id = su.tip_id
                          WHERE t.reference_number = '$refNumber' AND t.user_id = $userId
                          ORDER BY su.created_at DESC";
                
                $result = $conn->query($query);
                
                if ($result && $result->num_rows > 0) {
                    $tip = $result->fetch_assoc();
                    echo '<div class="status-container">';
                    echo '<h4>Report Status</h4>';
                    
                    echo '<div class="status-card">';
                    echo '<div class="status-header">';
                    echo '<span class="ref-number">Reference: ' . $tip['reference_number'] . '</span>';
                    echo '<span class="status-badge status-' . strtolower(str_replace(' ', '-', $tip['status'])) . '">' . $tip['status'] . '</span>';
                    echo '</div>';
                    
                    echo '<div class="status-details">';
                    echo '<p><strong>Date Submitted:</strong> ' . date('F j, Y H:i', strtotime($tip['created_at'])) . '</p>';
                    echo '<p><strong>Suspect Name:</strong> ' . htmlspecialchars($tip['suspect_name']) . '</p>';
                    
                    if ($tip['evidence_path']) {
                        echo '<p><strong>Evidence:</strong> ';
                        echo '<a href="uploads/' . $tip['evidence_path'] . '" target="_blank">View Uploaded File</a>';
                        echo '</p>';
                    }
                    echo '</div>';
                    
                    // Status updates
                    if ($result->num_rows > 1) {
                        echo '<div class="status-updates">';
                        echo '<h5>Status History</h5>';
                        
                        while ($update = $result->fetch_assoc()) {
                            if ($update['update_status']) {
                                echo '<div class="update">';
                                echo '<div class="update-date">' . date('M j, Y H:i', strtotime($update['update_date'])) . '</div>';
                                echo '<div class="update-status">' . $update['update_status'] . '</div>';
                                if ($update['update_details']) {
                                    echo '<div class="update-details">' . nl2br(htmlspecialchars($update['update_details'])) . '</div>';
                                }
                                echo '</div>';
                            }
                        }
                        echo '</div>';
                    }
                    
                    echo '</div>'; // Close status-card
                    echo '</div>'; // Close status-container
                } else {
                    echo '<div class="alert alert-error">No report found with that reference number. Please check the number and try again.</div>';
                }
            }
            ?>
        </div>
    </main>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>