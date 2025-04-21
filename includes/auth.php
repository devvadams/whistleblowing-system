    <?php
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_lifetime' => 86400, // 1 day
            'cookie_secure'   => true,
            'cookie_httponly' => true,
            'use_strict_mode' => true
        ]);
    }

    // Redirect to login if not authenticated
    function require_auth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../login.php?error=Please login first");
            exit();
        }
    }

    // Redirect to index if already logged in
    function redirect_if_authenticated() {
        if (isset($_SESSION['user_id'])) {
            header("Location: ../index.php");
            exit();
        }
    }

    // Check if user is admin
    function require_admin() {
        require_auth();
        if ($_SESSION['user_role'] != 'admin') {
            header("Location: ../index.php?error=Admin access required");
            exit();
        }
    }

    // CSRF protection
    function generate_csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    function verify_csrf_token($token) {
        return isset($_SESSION['csrf_token']) && 
            hash_equals($_SESSION['csrf_token'], $token);
    }
    ?>