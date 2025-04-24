<?php
$pageTitle = 'Transactions';
require_once 'includes/header.php';

// Get database connection
$db = getDBConnection();

// Get current page and status filter
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build the query based on status filter
$whereClause = '';
$params = [];

if ($status !== 'all') {
    $whereClause = 'WHERE status = :status';
    $params[':status'] = $status;
}

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM transactions $whereClause";
$stmt = $db->prepare($countQuery);
$stmt->execute($params);
$totalTransactions = $stmt->fetchColumn();
$totalPages = ceil($totalTransactions / $perPage);

// Get transactions for current page
$query = "SELECT * FROM transactions $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
if ($status !== 'all') {
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
}
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="bg-white shadow rounded-lg p-6">
<div class="mb-8">
    <a href="<?php echo BASE_URL; ?>/admin/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Create New Transaction
    </a>
</div>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Transactions</h2>
        <div class="flex flex-wrap gap-2">
            <a href="?status=all" class="px-3 py-1.5 text-sm rounded-full <?php echo $status === 'all' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100'; ?>">All</a>
            <a href="?status=pending" class="px-3 py-1.5 text-sm rounded-full <?php echo $status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:bg-gray-100'; ?>">Pending</a>
            <a href="?status=completed" class="px-3 py-1.5 text-sm rounded-full <?php echo $status === 'completed' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100'; ?>">Completed</a>
            <a href="?status=failed" class="px-3 py-1.5 text-sm rounded-full <?php echo $status === 'failed' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'; ?>">Failed</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Real Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($transaction['order_id']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($transaction['customer_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo number_format($transaction['real_amount']); ?> USDT</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo number_format($transaction['payment_amount'], 6); ?> USDT</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $transaction['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($transaction['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    'bg-red-100 text-red-800'); ?>">
                                <?php echo ucfirst($transaction['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex space-x-2">
                                <a href="<?php echo BASE_URL; ?>/admin/transaction/<?php echo $transaction['order_id']; ?>" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a target="_blank" href="<?php echo BASE_URL; ?>/checkout/<?php echo $transaction['order_id']; ?>" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/delete_transaction/<?php echo $transaction['order_id']; ?>" 
                                   class="text-red-600 hover:text-red-900"
                                   onclick="return confirm('Are you sure you want to delete this transaction? This action cannot be undone.');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-between mt-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                <?php endif; ?>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to 
                        <span class="font-medium"><?php echo min($offset + $perPage, $totalTransactions); ?></span> of 
                        <span class="font-medium"><?php echo $totalTransactions; ?></span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 