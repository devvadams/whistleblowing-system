<?php
include '../includes/config.php';
include '../includes/auth.php';

// Check if user is admin
if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get statistics for dashboard
$totalTips = $conn->query("SELECT COUNT(*) as count FROM tips")->fetch_assoc()['count'];
$pendingTips = $conn->query("SELECT COUNT(*) as count FROM tips WHERE status = 'Pending'")->fetch_assoc()['count'];
$investigationTips = $conn->query("SELECT COUNT(*) as count FROM tips WHERE status = 'Under Investigation'")->fetch_assoc()['count'];
$resolvedTips = $conn->query("SELECT COUNT(*) as count FROM tips WHERE status = 'Resolved'")->fetch_assoc()['count'];

// Get recent tips
$recentTipsQuery = "SELECT t.*, u.first_name, u.last_name 
                    FROM tips t 
                    JOIN users u ON t.user_id = u.id 
                    ORDER BY t.created_at DESC 
                    LIMIT 5";
$recentTips = $conn->query($recentTipsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Whistle Blowing System</title>
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
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="admin-profile">
                <h3>Welcome, <?php echo $_SESSION['user_name']; ?></h3>
                <p>Administrator</p>
            </div>
            
            <nav class="admin-nav">
                <ul>
                    <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="tips.php"><i class="fas fa-clipboard-list"></i> All Reports</a></li>
                    <li><a href="pending-tips.php"><i class="fas fa-hourglass-half"></i> Pending Reports</a></li>
                    <li><a href="investigation-tips.php"><i class="fas fa-search"></i> Under Investigation</a></li>
                    <li><a href="resolved-tips.php"><i class="fas fa-check-circle"></i> Resolved Reports</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="dashboard-header">
                <h3>System Overview</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / <span>Dashboard</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon total-reports">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Total Reports</h4>
                        <p><?php echo $totalTips; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon pending-reports">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Pending</h4>
                        <p><?php echo $pendingTips; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon investigation-reports">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Under Investigation</h4>
                        <p><?php echo $investigationTips; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon resolved-reports">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Resolved</h4>
                        <p><?php echo $resolvedTips; ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="recent-reports">
                <div class="section-header">
                    <h4>Recent Reports</h4>
                    <a href="tips.php" class="btn btn-small">View All</a>
                </div>

                <table class="recent-table">
                    <thead>
                        <tr>
                            <th>Ref. No.</th>
                            <th>Reported By</th>
                            <th>Suspect</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($tip = $recentTips->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $tip['reference_number']; ?></td>
                            <td><?php echo htmlspecialchars($tip['first_name'] . ' ' . $tip['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($tip['suspect_name']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($tip['created_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $tip['status'])); ?>">
                                    <?php echo $tip['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="view-tip.php?id=<?php echo $tip['id']; ?>" class="btn-action view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="update-status.php?id=<?php echo $tip['id']; ?>" class="btn-action edit" title="Update Status">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Activity Log (Optional) -->
            <div class="activity-log">
                <div class="section-header">
                    <h4>Recent Activity</h4>
                </div>
                <div class="log-items">
                    <!-- This would be populated from an activity log table -->
                    <div class="log-item">
                        <div class="log-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="log-content">
                            <p>New user registered: John Doe</p>
                            <small>2 hours ago</small>
                        </div>
                    </div>
                    <div class="log-item">
                        <div class="log-icon">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <div class="log-content">
                            <p>New report submitted (REF: WB123456)</p>
                            <small>5 hours ago</small>
                        </div>
                    </div>
                    <div class="log-item">
                        <div class="log-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="log-content">
                            <p>Report marked as resolved (REF: WB123450)</p>
                            <small>1 day ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>