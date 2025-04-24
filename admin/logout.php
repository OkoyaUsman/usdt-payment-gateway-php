<?php
require_once '../config.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();
redirect(BASE_URL . '/admin/login');
?> 