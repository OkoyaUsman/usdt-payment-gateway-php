<?php
require_once '../config.php';

if (!isset($_GET['order_id'])) {
    die('Invalid order ID');
}

$order_id = $_GET['order_id'];
$db = getDBConnection();

// Get transaction details
$stmt = $db->prepare("SELECT * FROM transactions WHERE order_id = ? AND status = 'completed'");
$stmt->execute([$order_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die('Transaction not found');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6 m-6">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h1 class="text-2xl font-bold text-center mt-4">Payment Successful!</h1>
                <p class="text-gray-600 mt-2">Thank you for your payment.</p>
            </div>

            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-medium text-gray-900">Transaction Details</h2>
                <dl class="mt-2 space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Order ID</dt>
                        <dd class="text-sm text-gray-900"><?php echo $transaction['order_id']; ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="text-sm text-gray-900"><?php echo number_format($transaction['payment_amount'], 6); ?> USDT</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="text-sm text-gray-900"><?php echo date('Y-m-d H:i:s', strtotime($transaction['completed_at'])); ?></dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">A confirmation email has been sent to <?php echo htmlspecialchars($transaction['customer_email'] == '' ? 'your email address' : $transaction['customer_email']); ?></p>
            </div>
        </div>
    </div>
</body>
</html> 