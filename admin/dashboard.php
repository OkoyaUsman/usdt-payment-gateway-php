<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';

// Get database connection
$db = getDBConnection();

// Get statistics
$stats = [
    'total' => $db->query("SELECT COUNT(*) FROM transactions")->fetchColumn(),
    'completed' => $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'completed'")->fetchColumn(),
    'failed' => $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'failed'")->fetchColumn(),
    'pending' => $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'pending'")->fetchColumn(),
    'total_amount' => $db->query("SELECT SUM(payment_amount) FROM transactions WHERE status = 'completed'")->fetchColumn() ?? 0
];

// Get recent transactions
$stmt = $db->query("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 10");
$recentTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

$walletApi = file_get_contents("https://apilist.tronscan.org/api/accountv2?address={$settings['usdt_address']}");
$walletData = json_decode($walletApi, true);
$walletBalance = intval($walletData['withPriceTokens'][1]['balance'])/(10**6);
?>

<div class="bg-white shadow rounded-lg p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Transactions -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100">
                    <i class="fas fa-exchange-alt text-indigo-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Transactions</h3>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['total']); ?></p>
                </div>
            </div>
        </div>

        <!-- Completed Transactions -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['completed']); ?></p>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['pending']); ?></p>
                </div>
            </div>
        </div>

        <!-- Failed Transactions -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Failed</h3>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['failed']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
        <!-- Total Amount -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-dollar-sign text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Amount Received</h3>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['total_amount'], 6); ?> USDT</p>
                </div>
            </div>
        </div>

        <!-- Wallet Balance -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-wallet text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Current Wallet Balance</h3>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($walletBalance, 6); ?> USDT</p>
                </div>
            </div>
        </div>
    </div>

<!-- Recent Transactions -->
<div class="bg-white shadow rounded-lg overflow-x-auto">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Transactions</h3>
    </div>
    <div class="border-t border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($recentTransactions as $transaction): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <a href="<?php echo BASE_URL; ?>/admin/transaction/<?php echo $transaction['order_id']; ?>" class="text-indigo-600 hover:text-indigo-900">
                            <?php echo htmlspecialchars($transaction['order_id']); ?>
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($transaction['customer_name']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo number_format($transaction['real_amount'], 6); ?> USDT
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php echo $transaction['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                ($transaction['status'] === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                            <?php echo ucfirst($transaction['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('Y-m-d H:i:s', strtotime($transaction['created_at'])); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 