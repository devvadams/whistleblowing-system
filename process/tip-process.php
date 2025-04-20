<?php
include '../includes/config.php';
include '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $suspectName = $conn->real_escape_string($_POST['suspectName']);
    $suspectPosition = $conn->real_escape_string($_POST['suspectPosition']);
    $suspectOrganization = $conn->real_escape_string($_POST['suspectOrganization']);
    $tipDetails = $conn->real_escape_string($_POST['tipDetails']);
    $userId = $_SESSION['user_id'];
    $referenceNumber = 'WB' . time() . rand(100, 999); // Simple reference number
    
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
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $fileType = $_FILES['evidence']['type'];
        $fileSize = $_FILES['evidence']['size'] / 1024 / 1024; // MB
        
        if (in_array($fileType, $allowedTypes) && $fileSize <= 5) {
            if (move_uploaded_file($_FILES['evidence']['tmp_name'], $targetPath)) {
                $evidencePath = $fileName;
            }
        }
    }
    
    // Insert tip into database
    $query = "INSERT INTO tips (user_id, reference_number, suspect_name, suspect_position, suspect_organization, tip_details, evidence_path) 
              VALUES ('$userId', '$referenceNumber', '$suspectName', '$suspectPosition', '$suspectOrganization', '$tipDetails', '$evidencePath')";
    
    if ($conn->query($query)) {
        // Insert initial status update
        $tipId = $conn->insert_id;
        $statusQuery = "INSERT INTO status_updates (tip_id, status) VALUES ('$tipId', 'Pending')";
        $conn->query($statusQuery);
        
        // Redirect to success page with reference number
        header("Location: ../feedback.php?success=1&ref=" . $referenceNumber);
        exit();
    } else {
        header("Location: ../submit-tip.php?error=Failed to submit tip. Please try again.");
        exit();
    }
} else {
    header("Location: ../submit-tip.php");
    exit();
}
?>