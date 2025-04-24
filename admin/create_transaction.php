<?php
$pageTitle = 'Create Transaction';
require_once 'includes/header.php';

// Get database connection
$db = getDBConnection();

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $realAmount = floatval($_POST['amount']);
        $customerName = trim($_POST['customer_name']);
        $customerEmail = trim($_POST['customer_email']);
        
        if ($realAmount <= 0) {
            throw new Exception('Amount must be greater than 0');
        }
        
        if (empty($customerName)) {
            throw new Exception('Customer name is required');
        }
        
        if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Valid customer email is required');
        }
        
        // Generate order ID and random decimal
        $orderId = generateOrderId();
        $randomDecimal = generateRandomDecimal();
        
        // Calculate real amount
        $paymentAmount = $realAmount + $randomDecimal;
        
        // Get settings 
        $stmt = $db->query("SELECT * FROM settings LIMIT 1");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        // Insert transaction
        $stmt = $db->prepare("INSERT INTO transactions (order_id, customer_name, customer_email, real_amount, payment_amount, address, network, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$orderId, $customerName, $customerEmail, $realAmount, $paymentAmount, $settings['usdt_address'], $settings['network']]);
        
        $message = 'Transaction created successfully!';
        $messageType = 'success';
        
        // Clear form values on success
        $realAmount = '';
        $customerName = '';
        $customerEmail = '';
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}
?>

<div class="bg-white shadow rounded-lg p-6 m-3">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Create New Transaction</h2>
    </div>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-50 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700"><?php echo $message; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Amount (USDT)</label>
            <div class="mt-1">
                <input type="number" step="0.000001" name="amount" id="amount" required
                    value="<?php echo isset($realAmount) ? htmlspecialchars($realAmount) : ''; ?>"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
        </div>

        <div>
            <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
            <div class="mt-1">
                <input type="text" name="customer_name" id="customer_name" required
                    value="<?php echo isset($customerName) ? htmlspecialchars($customerName) : ''; ?>"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
        </div>

        <div>
            <label for="customer_email" class="block text-sm font-medium text-gray-700">Customer Email</label>
            <div class="mt-1">
                <input type="email" name="customer_email" id="customer_email" required
                    value="<?php echo isset($customerEmail) ? htmlspecialchars($customerEmail) : ''; ?>"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create Transaction
            </button>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?> 