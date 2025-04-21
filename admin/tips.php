<?php
include '../includes/config.php';
// include '../includes/auth.php';
require_once '../includes/auth.php';

// Check if user is admin
if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get all tips with user information
$query = "SELECT t.*, u.first_name, u.last_name 
          FROM tips t 
          JOIN users u ON t.user_id = u.id 
          ORDER BY t.created_at DESC";
$result = $conn->query($query);

// Get filter from URL if exists
$filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reports - Admin Dashboard</title>
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
                <h3>All Submitted Reports</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / <span>All Reports</span>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="filter-options">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="filter">Filter by Status:</label>
                        <select name="filter" id="filter" onchange="this.form.submit()">
                            <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Reports</option>
                            <option value="Pending" <?php echo $filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Under Investigation" <?php echo $filter == 'Under Investigation' ? 'selected' : ''; ?>>Under Investigation</option>
                            <option value="Resolved" <?php echo $filter == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="Dismissed" <?php echo $filter == 'Dismissed' ? 'selected' : ''; ?>>Dismissed</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Tips Table -->
            <div class="table-container">
                <table class="data-table">
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
                        <?php 
                        // Modify query based on filter
                        if ($filter != 'all') {
                            $query = "SELECT t.*, u.first_name, u.last_name 
                                      FROM tips t 
                                      JOIN users u ON t.user_id = u.id 
                                      WHERE t.status = '$filter'
                                      ORDER BY t.created_at DESC";
                            $result = $conn->query($query);
                        }

                        if ($result->num_rows > 0):
                            while ($tip = $result->fetch_assoc()):
                        ?>
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
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="no-data">No reports found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination (optional) -->
            <div class="pagination">
                <a href="#" class="page-link disabled">&laquo;</a>
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <a href="#" class="page-link">&raquo;</a>
            </div>
        </main>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>