<?php
require_once 'includes/header.php';

// Get database connection
$db = getDBConnection();

// Get transaction ID from URL
$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    redirect(BASE_URL.'/admin/transactions');
}

// Delete the transaction
$query = "DELETE FROM transactions WHERE order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindValue(':order_id', $orderId, PDO::PARAM_STR);
$stmt->execute();

// Redirect back to transactions page with success message
$_SESSION['success_message'] = 'Transaction deleted successfully';
redirect(BASE_URL.'/admin/transactions'); 