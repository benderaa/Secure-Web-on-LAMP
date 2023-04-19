<?php
session_start();

// Set content security policy
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");

// Set error handling
set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    error_log("Error: $errstr in $errfile on line $errline");
    http_response_code(500);
    echo "500 Internal Server Error";
    exit;
});

// Set secure session management
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Check if request is potentially malicious
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if CSRF token is valid
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo "403 Forbidden";
        exit;
    }
}

// If the user is logged in and the request is not potentially malicious, show the landing page
?>
