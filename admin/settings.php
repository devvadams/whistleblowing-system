<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Check if user is admin
if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and save settings (you can add saving logic here)
    header("Location: settings.php?success=Settings updated");
    $_SESSION['message'] = 'Settings updated successfully'; // Set session message
    redirect_with_message('settings.php', 'success', 'Settings updated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Dashboard</title>
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
                <h3>System Settings</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / 
                    <span>Settings</span>
                </div>
            </div>

            <div class="tip-details">
                <div class="detail-card full-width">
                    <h4>Update System Settings</h4>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert success">
                            <?php echo htmlspecialchars($_GET['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="settings-form">
                        <div class="form-group">
                            <label for="system_name">System Name</label>
                            <input type="text" id="system_name" name="system_name" value="Whistle Blowing System" required>
                        </div>

                        <div class="form-group">
                            <label for="items_per_page">Items Per Page</label>
                            <select id="items_per_page" name="items_per_page" required>
                                <option value="10">10</option>
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="report_expiry">Report Expiry (Days)</label>
                            <input type="number" id="report_expiry" name="report_expiry" value="30" min="1" required>
                        </div>

                        <div class="form-group form-actions">
                            <button type="submit" class="btn">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <?php if (isset($_SESSION['message'])): ?>
    <script>
        alert("<?php echo $_SESSION['message']; ?>"); // Display success message
    </script>
    <?php unset($_SESSION['message']); ?> <!-- Clear the message after displaying -->
    <?php endif; ?>

    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>
