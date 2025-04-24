<?php
date_default_timezone_set('Africa/Lagos');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'usdtpay');

// Application configuration
define('BASE_URL', 'http://localhost/usdtpay'); //base url
define('SITE_NAME', 'USDT Pay'); //site title
define('ADMIN_EMAIL', 'admin@example.com'); //used for sending emails

// Demo mode configuration
define('DEMO_MODE', false); // Set to true for demo mode, false for production

// Session configuration
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper functions
function generateOrderId() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function generateRandomDecimal() {
    return mt_rand(1, 1000000) / 1000000;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function redirect($path) {
    header("Location: $path");
    exit();
}

// CSRF Protection
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
} 