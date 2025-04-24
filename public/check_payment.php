<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit();
}

$order_id = $_GET['order_id'];
$db = getDBConnection();

// Get transaction details
$stmt = $db->prepare("SELECT * FROM transactions WHERE order_id = ?");
$stmt->execute([$order_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    http_response_code(404);
    echo json_encode(['error' => 'Transaction not found']);
    exit();
}

$url = "https://apilist.tronscanapi.com/api/transfer/trc20?address=" . $transaction['address'] . "&trc20Id=TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t&limit=50&direction=2";
$response = file_get_contents($url);
$data = json_decode($response, true);

// Check if the transaction is in the response
$transaction_found = false;
foreach ($data['data'] as $item) {
    if (floatval(intval($item['amount'])/(10**6)) == floatval($transaction['payment_amount']) && $item['to'] === $transaction['address']) {
        $stmt = $db->prepare("SELECT * FROM transactions WHERE tx_hash = ?");
        $stmt->execute([$item['hash']]);
        $transaction_in_db = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$transaction_in_db) {
            $transaction_found = true;
            $tx_hash = $item['hash'];
            break;
        }
    }
}

if ($transaction_found) {
    if (!empty($transaction['customer_email'])) {
        $email = $transaction['customer_email'];
        $subject = 'Payment Completed';
        $message = 'Hi ' . $transaction['customer_name'] . ',<br>';
        $message .= 'Your payment has been completed. Thank you for your purchase.<br><br>';
        $message .= 'Order ID: ' . $transaction['order_id'] . '<br>';
        $message .= 'Amount: ' . $transaction['payment_amount'] . '<br>';
        $message .= 'Transaction Hash: ' . $tx_hash . '<br>';
        $headers = "From: " . ADMIN_EMAIL . "\r\n";
        $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        mail($email, $subject, $message, $headers);
    }
    $stmt = $db->prepare("UPDATE transactions SET status = 'completed', tx_hash = ?, completed_at = NOW() WHERE order_id = ?");
    $stmt->execute([$tx_hash, $order_id]);
    echo json_encode([
        'status' => 'completed',
        'tx_hash' => $tx_hash,
        'order_id' => $transaction['order_id'],
        'amount' => $transaction['payment_amount']
    ]);
} else {
    echo json_encode([
        'status' => $transaction['status'],
        'order_id' => $transaction['order_id'],
        'amount' => $transaction['payment_amount']
    ]);
}