<?php
include '../includes/config.php';
include '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $suspectName = $conn->real_escape_string($_POST['suspectName']);
    $suspectPosition = isset($_POST['suspectPosition']) ? $conn->real_escape_string($_POST['suspectPosition']) : '';
    $suspectOrganization = isset($_POST['suspectOrganization']) ? $conn->real_escape_string($_POST['suspectOrganization']) : '';
    $tipDetails = $conn->real_escape_string($_POST['tipDetails']);
    $userId = $_SESSION['user_id'];

    // Handle file upload
    $evidencePath = null;
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['evidence']['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Check file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $fileType = $_FILES['evidence']['type'];
        $fileSize = $_FILES['evidence']['size'] / 1024 / 1024; // MB
        
        if (in_array($fileType, $allowedTypes) && $fileSize <= 5) {
            if (move_uploaded_file($_FILES['evidence']['tmp_name'], $targetPath)) {
                $evidencePath = $fileName;
            }
        }
    }

    // Generate reference number
    $referenceNumber = 'WB-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

    // Insert tip into database
    $query = "INSERT INTO tips (user_id, reference_number, suspect_name, suspect_position, suspect_organization, tip_details, evidence_path) 
              VALUES ('$userId', '$referenceNumber', '$suspectName', '$suspectPosition', '$suspectOrganization', '$tipDetails', '$evidencePath')";
    
    if ($conn->query($query)) {
        // Insert initial status update
        $tipId = $conn->insert_id;
        $statusQuery = "INSERT INTO status_updates (tip_id, status) VALUES ('$tipId', 'Pending')";
        $conn->query($statusQuery);
        
        // Store reference number in session for display
        $_SESSION['last_reference'] = $referenceNumber;
        
        // Redirect to success page
        header("Location: ../submission-success.php?ref=" . $referenceNumber);
        exit();
    } else {
        header("Location: ../submit-tip.php?error=Failed to submit report. Please try again.");
        exit();
    }
} else {
    header("Location: ../submit-tip.php");
    exit();
}
?>