<?php
require_once '../includes/config.php';
require_admin();

$query = "SELECT t.*, u.first_name, u.last_name 
          FROM tips t 
          JOIN users u ON t.user_id = u.id 
          WHERE t.status = 'Pending'
          ORDER BY t.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reports - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
                <h3>Pending Reports</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / 
                    <span>Pending Reports</span>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ref. No.</th>
                            <th>Submitted By</th>
                            <th>Suspect</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['reference_number']) ?></td>
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['suspect_name']) ?></td>
                                <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="view-tip.php?id=<?= $row['id'] ?>" class="btn-action view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="update-status.php?id=<?= $row['id'] ?>" class="btn-action edit" title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">No pending reports found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../../assets/js/admin.js"></script>
</body>
</html>
