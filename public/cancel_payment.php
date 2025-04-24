<?php
require_once '../config.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order_id']) || !isset($input['reason'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID and reason are required']);
    exit();
}

$order_id = $input['order_id'];
$reason = $input['reason'];
$db = getDBConnection();

// Get transaction details
$stmt = $db->prepare("SELECT * FROM transactions WHERE order_id = ? AND status = 'pending'");
$stmt->execute([$order_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    http_response_code(404);
    echo json_encode(['error' => 'Transaction not found or already processed']);
    exit();
}

// Update transaction status
$stmt = $db->prepare("UPDATE transactions SET status = 'failed', failure_reason = ? WHERE order_id = ?");
$stmt->execute([$reason, $order_id]);

echo json_encode([
    'status' => 'success',
    'message' => 'Transaction cancelled successfully'
]); 