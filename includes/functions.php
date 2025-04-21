<?php
// Database connection helper
function db_connect() {
    static $conn;
    if (!isset($conn)) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
    }
    return $conn;
}

// Secure input sanitization
function sanitize_input($data) {
    $conn = db_connect();
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Password hashing
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Generate reference number
function generate_reference_number() {
    $prefix = 'WB';
    $date = date('Ymd');
    $random = strtoupper(bin2hex(random_bytes(3))); // 6 chars
    return "{$prefix}-{$date}-{$random}";
}

// File upload handler
function handle_file_upload($file, $allowed_types, $max_size_mb) {
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, "Upload error: {$file['error']}"];
    }

    // Validate file type
    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        return [false, "Invalid file type. Allowed: " . implode(', ', $allowed_types)];
    }

    // Validate size
    $max_bytes = $max_size_mb * 1024 * 1024;
    if ($file['size'] > $max_bytes) {
        return [false, "File too large. Max: {$max_size_mb}MB"];
    }

    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $target_path = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return [true, $filename];
    }

    return [false, "Failed to move uploaded file"];
}

// Redirect with flash message
function redirect_with_message($url, $type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
    header("Location: $url");
    exit();
}

// Display flash message
function display_flash_message() {
    if (!empty($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        echo "<div class='alert alert-{$type}'>{$message}</div>";
        unset($_SESSION['flash']);
    }
}
?>