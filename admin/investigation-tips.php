<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Only admin can access
if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Fetch reports under investigation
$query = "SELECT t.*, u.first_name, u.last_name 
          FROM tips t 
          JOIN users u ON t.user_id = u.id 
          WHERE t.status = 'Under Investigation'
          ORDER BY t.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Under Investigation - Admin Dashboard</title>
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
                <h3>Reports Under Investigation</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / 
                    <span>Under Investigation</span>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Reporter Name</th>
                            <th>Suspect Name</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($tip = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tip['reference_number']); ?></td>
                                    <td><?php echo htmlspecialchars($tip['first_name'] . ' ' . $tip['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($tip['suspect_name']); ?></td>
                                    <td><?php echo date('F j, Y H:i', strtotime($tip['created_at'])); ?></td>
                                    <td>
                                        <span class="status-<?php echo strtolower(str_replace(' ', '-', $tip['status'])); ?>">
                                            <?php echo htmlspecialchars($tip['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view-tip.php?id=<?php echo $tip['id']; ?>" class="btn btn-small">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No reports found under investigation.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>
