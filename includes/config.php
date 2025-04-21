<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'whistleblowing_system');

// Application settings
define('APP_NAME', 'Whistle Blowing System');
define('APP_ROOT', dirname(dirname(__FILE__)));
define('UPLOAD_DIR', APP_ROOT . '/uploads');
define('MAX_FILE_SIZE', 5); // MB

// Include helper files
require_once 'functions.php';
require_once 'auth.php';

// Initialize database connection
$conn = db_connect();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>