<?php
// Include necessary files
include '../includes/config.php';  // Loads auth.php and functions.php automatically
require_once '../includes/auth.php';

require_admin();  // Ensure only admins can access this page

// Check if tip ID is provided
if (!isset($_GET['id'])) {
    redirect_with_message('tips.php', 'error', 'No report specified');
}

$tipId = sanitize_input($_GET['id']);

// Fetch current tip details
$query = "SELECT t.*, u.first_name, u.last_name 
          FROM tips t
          JOIN users u ON t.user_id = u.id
          WHERE t.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $tipId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect_with_message('tips.php', 'error', 'Report not found');
}

$tip = $result->fetch_assoc();

// Generate CSRF token
$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
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
                <h3>Update Report Status</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / 
                    <a href="tips.php">Reports</a> / 
                    <a href="view-tip.php?id=<?php echo $tipId; ?>">View Report</a> / 
                    <span>Update Status</span>
                </div>
            </div>

            <div class="tip-details">
                <div class="detail-card full-width">
                    <h4>Report Information</h4>

                    <div class="detail-row">
                        <span class="detail-label">Reference Number:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tip['reference_number']); ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Current Status:</span>
                        <span class="detail-value status-<?php echo strtolower(str_replace(' ', '-', $tip['status'])); ?>">
                            <?php echo htmlspecialchars($tip['status']); ?>
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Reporter:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tip['first_name'] . ' ' . $tip['last_name']); ?></span>
                    </div>
                </div>

                <div class="detail-card full-width">
                    <h4>Update Status</h4>

                    <form action="update-status.php?id=<?php echo $tipId; ?>" method="POST" class="status-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="form-group">
                            <label for="status">New Status</label>
                            <select name="status" id="status" required>
                                <option value="">Select Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Under Investigation">Under Investigation</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Dismissed">Dismissed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="4" placeholder="Enter any additional details about this status update"></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn">Submit Update</button>
                            <a href="view-tip.php?id=<?php echo $tipId; ?>" class="btn btn-secondary">Cancel</a>
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
