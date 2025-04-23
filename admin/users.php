<?php
require_once '../includes/config.php';
require_admin();

$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
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
                <h3>User Management</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / 
                    <span>Users</span>
                </div>
                <a href="add-user.php" class="btn">Add New User</a>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['first_name'].' '.$user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= ucfirst($user['role']) ?></td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="edit-user.php?id=<?= $user['id'] ?>" class="btn-action edit" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="process/delete-user.php?id=<?= $user['id'] ?>" 
                                   class="btn-action delete" 
                                   onclick="return confirm('Are you sure you want to delete this user?')"
                                   title="Delete User">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../../assets/js/admin.js"></script>
</body>
</html>
