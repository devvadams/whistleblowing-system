<?php 
// tips-list.php
include 'includes/config.php';
include 'includes/auth.php'; // Should include admin check

// Check if user is admin
if ($_SESSION['user_role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Get all tips
$query = "SELECT t.*, u.first_name, u.last_name FROM tips t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Tips - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="government-header">
            <h1>FEDERAL GOVERNMENT OF NIGERIA</h1>
            <p>Federal Ministry of Finance</p>
            <h2>Submitted Tips - Admin Panel</h2>
        </div>
    </header>

    <main class="container">
        <div class="admin-nav">
            <a href="tips-list.php" class="active">Submitted Tips</a>
            <a href="tip-status.php">Tip Status</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ref. No.</th>
                        <th>Submitted By</th>
                        <th>Suspect Name</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['reference_number']; ?></td>
                        <td><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['suspect_name']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                        <td class="status-<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                            <?php echo $row['status']; ?>
                        </td>
                        <td>
                            <a href="view-tip.php?id=<?php echo $row['id']; ?>" class="btn btn-small">View</a>
                            <a href="update-status.php?id=<?php echo $row['id']; ?>" class="btn btn-small btn-secondary">Update Status</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="../assets/js/script.js"></script>
</body>
</html>