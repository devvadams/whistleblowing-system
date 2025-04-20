<?php
include '../../includes/config.php';
include '../../includes/auth.php';

// Check if user is admin
if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Get tip ID from URL
if (!isset($_GET['id']) {
    header("Location: tips.php");
    exit();
}

$tipId = $conn->real_escape_string($_GET['id']);

// Get tip details
$tipQuery = "SELECT t.*, u.first_name, u.last_name, u.email, u.phone 
             FROM tips t 
             JOIN users u ON t.user_id = u.id 
             WHERE t.id = '$tipId'";
$tipResult = $conn->query($tipQuery);

if ($tipResult->num_rows == 0) {
    header("Location: tips.php?error=Tip not found");
    exit();
}

$tip = $tipResult->fetch_assoc();

// Get status updates
$statusQuery = "SELECT * FROM status_updates 
                WHERE tip_id = '$tipId' 
                ORDER BY created_at DESC";
$statusResult = $conn->query($statusQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tip - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
            <h2>Admin Dashboard</h2>
        </div>
    </header>

    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <main class="admin-content">
            <div class="dashboard-header">
                <h3>View Report Details</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / 
                    <a href="tips.php">Reports</a> / 
                    <span>View Report</span>
                </div>
            </div>

            <div class="tip-details">
                <div class="detail-card">
                    <h4>Report Information</h4>
                    
                    <div class="detail-row">
                        <span class="detail-label">Reference Number:</span>
                        <span class="detail-value"><?php echo $tip['reference_number']; ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value status-<?php echo strtolower(str_replace(' ', '-', $tip['status'])); ?>">
                            <?php echo $tip['status']; ?>
                        </span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Date Submitted:</span>
                        <span class="detail-value"><?php echo date('F j, Y H:i', strtotime($tip['created_at'])); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Last Updated:</span>
                        <span class="detail-value"><?php echo date('F j, Y H:i', strtotime($tip['updated_at'])); ?></span>
                    </div>
                </div>

                <div class="detail-card">
                    <h4>Reporter Information</h4>
                    
                    <div class="detail-row">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tip['first_name'] . ' ' . $tip['last_name']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tip['email']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tip['phone']); ?></span>
                    </div>
                </div>

                <div class="detail-card">
                    <h4>Suspect Information</h4>
                    
                    <div class="detail-row">
                        <span class="detail-label">Suspect Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tip['suspect_name']); ?></span>
                    </div>
                    
                    <?php if ($tip['suspect_position']): ?>
                    <div class="detail-row">
                        <span class="detail-label">Position:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tip['suspect_position']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($tip['suspect_organization']): ?>
                    <div class="detail-row">
                        <span class="detail-label">Organization:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tip['suspect_organization']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="detail-card full-width">
                    <h4>Report Details</h4>
                    <div class="report-content">
                        <?php echo nl2br(htmlspecialchars($tip['tip_details'])); ?>
                    </div>
                </div>

                <?php if ($tip['evidence_path']): ?>
                <div class="detail-card">
                    <h4>Evidence</h4>
                    <div class="evidence-preview">
                        <?php
                        $fileExt = strtolower(pathinfo($tip['evidence_path'], PATHINFO_EXTENSION));
                        $filePath = '../../uploads/' . $tip['evidence_path'];
                        
                        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo '<img src="' . $filePath . '" alt="Evidence" class="evidence-image">';
                        } elseif ($fileExt == 'pdf') {
                            echo '<i class="fas fa-file-pdf evidence-icon"></i>';
                        } else {
                            echo '<i class="fas fa-file evidence-icon"></i>';
                        }
                        ?>
                        <a href="<?php echo $filePath; ?>" target="_blank" class="btn btn-small">View Evidence</a>
                        <a href="<?php echo $filePath; ?>" download class="btn btn-small btn-secondary">Download</a>
                    </div>
                </div>
                <?php endif; ?>

                <div class="detail-card full-width">
                    <h4>Status Updates</h4>
                    
                    <?php if ($statusResult->num_rows > 0): ?>
                    <div class="status-updates">
                        <?php while ($update = $statusResult->fetch_assoc()): ?>
                        <div class="status-update">
                            <div class="update-header">
                                <span class="update-status"><?php echo $update['status']; ?></span>
                                <span class="update-date"><?php echo date('F j, Y H:i', strtotime($update['created_at'])); ?></span>
                            </div>
                            <?php if ($update['update_details']): ?>
                            <div class="update-details">
                                <?php echo nl2br(htmlspecialchars($update['update_details'])); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <p>No status updates available.</p>
                    <?php endif; ?>
                    
                    <form action="process/update-status.php" method="POST" class="status-form">
                        <input type="hidden" name="tip_id" value="<?php echo $tipId; ?>">
                        
                        <div class="form-group">
                            <label for="status">Update Status</label>
                            <select name="status" id="status" required>
                                <option value="">Select Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Under Investigation">Under Investigation</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Dismissed">Dismissed</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="update_details">Details (Optional)</label>
                            <textarea name="update_details" id="update_details" rows="3" placeholder="Enter any additional details about this status update"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>