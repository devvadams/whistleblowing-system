<?php
require_once '../includes/config.php';
require_auth();

// Mark notifications as read when viewed
$conn->query("UPDATE notifications SET is_read=TRUE WHERE user_id={$_SESSION['user_id']}");

$notifications = $conn->query("SELECT n.*, t.reference_number 
                               FROM notifications n JOIN tips t ON n.tip_id=t.id 
                               WHERE n.user_id={$_SESSION['user_id']}
                               ORDER BY n.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Notifications</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <!-- Standard header -->
    </header>

    <main class="container">
        <h2>My Notifications</h2>
        
        <div class="notifications-list">
            <?php while ($note = $notifications->fetch_assoc()): ?>
            <div class="notification <?= $note['is_read'] ? 'read' : 'unread' ?>">
                <div class="notification-icon">
                    <?php if ($note['type'] == 'Resolved'): ?>
                        <i class="fas fa-check-circle success"></i>
                    <?php elseif ($note['type'] == 'Under Investigation'): ?>
                        <i class="fas fa-search investigating"></i>
                    <?php else: ?>
                        <i class="fas fa-info-circle info"></i>
                    <?php endif; ?>
                </div>
                <div class="notification-content">
                    <p><?= htmlspecialchars($note['message']) ?></p>
                    <small><?= date('M j, Y H:i', strtotime($note['created_at'])) ?></small>
                    <?php if ($note['type'] == 'Resolved'): ?>
                        <a href="view-reward.php?tip_id=<?= $note['tip_id'] ?>" class="btn btn-small">
                            View Reward Details
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>