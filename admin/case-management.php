<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_admin();

// Validate and get tip ID
$tipId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tipId <= 0) {
    header("Location: tips.php?error=Invalid case ID");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token (add this to your auth.php)
    if (!verify_csrf_token($_POST['csrf_token'])) {
        header("Location: case-management.php?id=$tipId&error=Invalid request");
        exit();
    }

    $status = sanitize_input($_POST['status']);
    $action = sanitize_input($_POST['action']);
    $notes = sanitize_input($_POST['notes'] ?? '');

    try {
        $conn->begin_transaction();

        // 1. Update tip status
        $updateStmt = $conn->prepare("UPDATE tips SET status=?, updated_at=NOW() WHERE id=?");
        $updateStmt->bind_param("si", $status, $tipId);
        $updateStmt->execute();

        // 2. Record action
        $adminId = $_SESSION['user_id'];
        $actionStmt = $conn->prepare("INSERT INTO case_actions (tip_id, admin_id, action_type, notes) VALUES (?, ?, ?, ?)");
        $actionStmt->bind_param("iiss", $tipId, $adminId, $action, $notes);
        $actionStmt->execute();

        // 3. Send notification if status changed significantly
        if (in_array($status, ['Under Investigation', 'Resolved'])) {
            $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, tip_id, message, type)
                                        SELECT user_id, ?, CONCAT('Your case #', reference_number, ' is now ', ?), ?
                                        FROM tips WHERE id=?");
            $notifStmt->bind_param("issi", $tipId, $status, $status, $tipId);
            $notifStmt->execute();
        }

        $conn->commit();
        header("Location: case-management.php?id=$tipId&success=Case updated successfully");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: case-management.php?id=$tipId&error=Update failed: " . urlencode($e->getMessage()));
        exit();
    }
}

// Get case details using prepared statement
$caseStmt = $conn->prepare("SELECT t.*, u.email, u.phone, u.first_name, u.last_name 
                           FROM tips t 
                           JOIN users u ON t.user_id = u.id 
                           WHERE t.id = ?");
$caseStmt->bind_param("i", $tipId);
$caseStmt->execute();
$caseResult = $caseStmt->get_result();

if ($caseResult->num_rows === 0) {
    header("Location: tips.php?error=Case not found");
    exit();
}

$case = $caseResult->fetch_assoc();

// Get evidence
$evidenceStmt = $conn->prepare("SELECT * FROM evidence WHERE tip_id = ?");
$evidenceStmt->bind_param("i", $tipId);
$evidenceStmt->execute();
$evidence = $evidenceStmt->get_result();

// Get case history
$historyStmt = $conn->prepare("SELECT a.*, u.first_name, u.last_name 
                              FROM case_actions a 
                              JOIN users u ON a.admin_id = u.id 
                              WHERE a.tip_id = ? 
                              ORDER BY a.created_at DESC");
$historyStmt->bind_param("i", $tipId);
$historyStmt->execute();
$history = $historyStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Case Management - <?= htmlspecialchars($case['reference_number']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .evidence-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .evidence-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .evidence-thumbnail {
            max-width: 100%;
            height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .file-icon {
            font-size: 50px;
            color: #6c757d;
            margin: 20px 0;
        }
        .timeline {
            border-left: 3px solid #1a5276;
            padding-left: 20px;
            margin: 20px 0;
        }
        .timeline-item {
            margin-bottom: 25px;
            position: relative;
            padding-left: 20px;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -11px;
            top: 5px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #1a5276;
        }
        .status-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
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
            <h1>Case Management: <?= htmlspecialchars($case['reference_number']) ?></h1>
            <div class="breadcrumb">
                <a href="dashboard.php">Dashboard</a> /
                <a href="tips.php">All Reports</a> /
                <span>Case Details</span>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <div class="case-container">
            <section class="case-overview">
                <div class="case-status">
                    <h3>Current Status: 
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $case['status'])) ?>">
                            <?= htmlspecialchars($case['status']) ?>
                        </span>
                    </h3>
                    <p><strong>Reporter:</strong> <?= htmlspecialchars($case['first_name'] . ' ' . $case['last_name']) ?></p>
                    <p><strong>Submitted:</strong> <?= date('F j, Y H:i', strtotime($case['created_at'])) ?></p>
                    <?php if ($case['status'] === 'Resolved' && !empty($case['resolved_at'])): ?>
                        <p><strong>Resolved:</strong> <?= date('F j, Y H:i', strtotime($case['resolved_at'])) ?></p>
                    <?php endif; ?>
                </div>

                <div class="case-details">
                    <h4>Case Details</h4>
                    <p><strong>Suspect:</strong> <?= htmlspecialchars($case['suspect_name']) ?></p>
                    <?php if (!empty($case['suspect_position'])): ?>
                        <p><strong>Position:</strong> <?= htmlspecialchars($case['suspect_position']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($case['suspect_organization'])): ?>
                        <p><strong>Organization:</strong> <?= htmlspecialchars($case['suspect_organization']) ?></p>
                    <?php endif; ?>
                    <div class="case-description">
                        <p><strong>Description:</strong></p>
                        <p><?= nl2br(htmlspecialchars($case['tip_details'])) ?></p>
                    </div>
                </div>
            </section>

            <section class="status-update">
                <h3>Update Case Status</h3>
                <form method="POST" class="status-form">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                    <div class="form-group">
                        <label for="status">New Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Pending" <?= $case['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Under Investigation" <?= $case['status'] === 'Under Investigation' ? 'selected' : '' ?>>Under Investigation</option>
                            <option value="Resolved" <?= $case['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="Dismissed" <?= $case['status'] === 'Dismissed' ? 'selected' : '' ?>>Dismissed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="action">Action Type</label>
                        <select name="action" id="action" class="form-control" required>
                            <option value="review">Evidence Review</option>
                            <option value="investigation">Field Investigation</option>
                            <option value="interview">Witness Interview</option>
                            <option value="document">Document Verification</option>
                            <option value="resolution">Case Resolution</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" rows="4" class="form-control" required placeholder="Enter details about this status update..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </section>

            <section class="evidence-section">
                <h3>Case Evidence</h3>
                <?php if ($evidence->num_rows > 0): ?>
                    <div class="evidence-gallery">
                        <?php while ($ev = $evidence->fetch_assoc()): ?>
                            <div class="evidence-item">
                                <?php if (strpos($ev['file_type'], 'image') === 0): ?>
                                    <img src="../uploads/<?= htmlspecialchars($ev['file_path']) ?>" class="evidence-thumbnail" alt="Case Evidence">
                                <?php else: ?>
                                    <div class="file-icon">
                                        <i class="fas fa-file-alt"></i>
                                        <p><?= pathinfo($ev['file_path'], PATHINFO_EXTENSION) ?></p>
                                    </div>
                                <?php endif; ?>
                                <p><strong>Type:</strong> <?= htmlspecialchars($ev['file_type']) ?></p>
                                <p><small>Uploaded: <?= date('M j, Y', strtotime($ev['uploaded_at'])) ?></small></p>
                                <a href="../uploads/<?= htmlspecialchars($ev['file_path']) ?>" class="btn btn-small" target="_blank" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>No evidence files submitted for this case.</p>
                <?php endif; ?>
            </section>

            <section class="case-history">
                <h3>Case History</h3>
                <?php if ($history->num_rows > 0): ?>
                    <div class="timeline">
                        <?php while ($act = $history->fetch_assoc()): ?>
                            <div class="timeline-item">
                                <div class="timeline-header">
                                    <strong><?= htmlspecialchars($act['first_name'] . ' ' . $act['last_name']) ?></strong>
                                    <span class="timeline-date"><?= date('M j, Y H:i', strtotime($act['created_at'])) ?></span>
                                </div>
                                <div class="timeline-action">
                                    <strong>Action:</strong> <?= ucwords(str_replace('_', ' ', $act['action_type'])) ?>
                                </div>
                                <?php if (!empty($act['notes'])): ?>
                                    <div class="timeline-notes">
                                        <?= nl2br(htmlspecialchars($act['notes'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>No activity history recorded for this case.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>