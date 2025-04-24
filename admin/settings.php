<?php
$pageTitle = 'Settings';
require_once 'includes/header.php';

// Get database connection
$db = getDBConnection();

// Get current settings
$stmt = $db->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (DEMO_MODE) {
        $error = "Settings cannot be updated in demo mode.";
    } else {
        try {
            $stmt = $db->prepare("UPDATE settings SET 
                usdt_address = :usdt_address,
                network = :network,
                checkout_timeout = :checkout_timeout,
                check_interval = :check_interval
                WHERE id = 1");
            
            $stmt->execute([
                ':usdt_address' => $_POST['usdt_address'],
                ':network' => $_POST['network'],
                ':checkout_timeout' => $_POST['checkout_timeout'],
                ':check_interval' => $_POST['check_interval']
            ]);
            
            $success = "Settings updated successfully.";
            
            // Refresh settings
            $stmt = $db->query("SELECT * FROM settings LIMIT 1");
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Error updating settings: " . $e->getMessage();
        }
    }
}
?>

<div class="bg-white shadow rounded-lg p-6 m-3">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">System Settings</h2>
        <?php if (DEMO_MODE): ?>
            <div class="mt-2 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Demo Mode Active</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Settings cannot be modified in demo mode. This is a demonstration version of the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($error)): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700"><?php echo $error; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700"><?php echo $success; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <div>
            <label for="usdt_address" class="block text-sm font-medium text-gray-700">USDT Receiving Address</label>
            <div class="mt-1">
                <input type="text" name="usdt_address" id="usdt_address" 
                    value="<?php echo htmlspecialchars($settings['usdt_address']); ?>" 
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    <?php echo DEMO_MODE ? 'disabled' : ''; ?>>
            </div>
        </div>

        <div>
            <label for="usdt_address" class="block text-sm font-medium text-gray-700">Network</label>
            <input type="text" id="network" name="network" value="<?php echo htmlspecialchars($settings['network']); ?>" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" <?php echo DEMO_MODE ? 'disabled' : ''; ?>>
        </div>

        <div>
        <label for="checkout_timeout" class="block text-sm font-medium text-gray-700">Checkout Timeout (seconds)</label>
            <div class="mt-1">
                <input type="number" name="checkout_timeout" id="checkout_timeout" 
                    value="<?php echo htmlspecialchars($settings['checkout_timeout']); ?>" 
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    <?php echo DEMO_MODE ? 'disabled' : ''; ?>>
            </div>
        </div>

        <div>
            <label for="check_interval" class="block text-sm font-medium text-gray-700">Transaction Check Interval (seconds)</label>
            <div class="mt-1">
                <input type="number" name="check_interval" id="check_interval" 
                    value="<?php echo htmlspecialchars($settings['check_interval']); ?>" 
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    <?php echo DEMO_MODE ? 'disabled' : ''; ?>>
            </div>
        </div>

        <?php if (!DEMO_MODE): ?>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Settings
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?> 