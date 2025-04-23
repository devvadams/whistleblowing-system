<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Check if user is admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Get user by ID
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$userId = intval($_GET['id']);
$query = "SELECT * FROM users WHERE id = $userId";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: users.php?error=User not found");
    exit();
}

$user = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $role = $conn->real_escape_string($_POST['role']);

    $updateQuery = "UPDATE users 
                    SET first_name = '$firstName', last_name = '$lastName', email = '$email', phone = '$phone', role = '$role'
                    WHERE id = $userId";

    if ($conn->query($updateQuery)) {
        $_SESSION['message'] = 'User updated successfully';
        header("Location: edit-user.php?id=$userId");
        exit();
    } else {
        $error = "Failed to update user. Please try again.";
    }
    
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Dashboard</title>
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
                <h3>Edit User</h3>
                <div class="breadcrumb">
                    <a href="dashboard.php">Home</a> / 
                    <a href="users.php">Users</a> / 
                    <span>Edit User</span>
                </div>
            </div>

            <div class="form-card">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="edit-user.php?id=<?php echo $userId; ?>" method="POST" class="admin-form">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="role">User Role</label>
                        <select id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn">Update User</button>
                    </div>
                </form>
            </div>
        </main>

        <?php if (isset($_SESSION['message'])): ?>
    <script>
        alert("<?php echo $_SESSION['message']; ?>"); // Display success message after clicking "Update User"
    </script>
    <?php unset($_SESSION['message']); ?> <!-- Clear the message after displaying it -->
<?php endif; ?>

    </div>

    <script src="../../assets/js/admin.js"></script>
</body>
</html>
