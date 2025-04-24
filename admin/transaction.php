<?php
$pageTitle = 'Transaction Details';
require_once 'includes/header.php';

// Get database connection
$db = getDBConnection();

// Get transaction ID from URL
$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    redirect('/admin/transactions');
}

// Get transaction details
$query = "SELECT * FROM transactions WHERE order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindValue(':order_id', $orderId, PDO::PARAM_STR);
$stmt->execute();
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    redirect('/admin/transactions');
}

$stmt = $db->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Format dates
$createdAt = date('M d, Y H:i:s', strtotime($transaction['created_at']));
$updatedAt = date('M d, Y H:i:s', strtotime($transaction['updated_at']));
$completedAt = $transaction['completed_at'] ? date('M d, Y H:i:s', strtotime($transaction['completed_at'])) : 'N/A';
$expiresAt = date('M d, Y H:i:s', strtotime($transaction['created_at']) + $settings['checkout_timeout']);
?>

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Transaction Details</h2>
        <a href="<?php echo BASE_URL; ?>/admin/transactions" class="text-indigo-600 hover:text-indigo-900">
            <i class="fas fa-arrow-left mr-2"></i>Back to Transactions
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h3>
            <dl class="grid grid-cols-1 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction['customer_name']); ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo $transaction['customer_email'] ? htmlspecialchars($transaction['customer_email']) : 'N/A'; ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Order ID</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction['order_id']); ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo $createdAt; ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo $updatedAt; ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Completed At</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo $completedAt; ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Expires At</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo $expiresAt; ?></dd>
                </div>
            </dl>
        </div>

        <!-- Payment Information -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Payment Information</h3>
            <dl class="grid grid-cols-1 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Real Amount</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo number_format($transaction['real_amount'], 2); ?> USDT</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Payment Amount</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo number_format($transaction['payment_amount'], 6); ?> USDT</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php echo $transaction['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                ($transaction['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                'bg-red-100 text-red-800'); ?>">
                            <?php echo ucfirst($transaction['status']); ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Failure Reason</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo $transaction['failure_reason'] ? htmlspecialchars($transaction['failure_reason']) : 'N/A'; ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">USDT Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono"><?php echo $transaction['address'] ? htmlspecialchars($transaction['address']) : 'N/A'; ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Network</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo $transaction['network'] ? htmlspecialchars($transaction['network']) : 'N/A'; ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Transaction Hash</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono break-all"><?php echo $transaction['tx_hash'] ? htmlspecialchars($transaction['tx_hash']) : 'N/A'; ?></dd>
                </div>
            </dl>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 