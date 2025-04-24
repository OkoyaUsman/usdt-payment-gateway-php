<?php
require_once '../config.php';

if (!isset($_GET['order_id'])) {
    die('Invalid order ID');
}

$order_id = $_GET['order_id'];
$db = getDBConnection();

// Get transaction details
$stmt = $db->prepare("SELECT * FROM transactions WHERE order_id = ?");
$stmt->execute([$order_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die('Transaction not found');
}

if ($transaction['status'] !== 'pending') {
    header("Location: " .BASE_URL. ($transaction['status'] === 'completed' ? '/success/' : '/failed/') . $order_id);
    exit();
}

// Get settings
$stmt = $db->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate expiration time based on transaction creation time
$expires = strtotime($transaction['created_at']) + $settings['checkout_timeout'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .tooltip {
            position: relative;
            display: inline-block;
        }
        .tooltip .tooltiptext {
            visibility: hidden;
            background-color: #4F46E5;
            color: white;
            text-align: center;
            border-radius: 4px;
            padding: 4px 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
            white-space: nowrap;
        }
        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #4F46E5 transparent transparent transparent;
        }
        .tooltip.show .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6 m-6 relative">
            <!-- Timer in top right corner of the card -->
            <div class="absolute top-4 right-4">
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-600" id="countdown"><?php echo gmdate("i:s", $settings['checkout_timeout']); ?></div>
                    <div class="text-[10px] text-gray-400">Time left</div>
                </div>
            </div>
            
            <h1 class="text-2xl font-bold text-center mb-6">USDTPay</h1>
            
            <div class="mt-6">
                <div class="text-center">
                    <div class="text-sm text-gray-500 mb-2">Amount to Send</div>
                    <div class="tooltip">
                        <div class="text-3xl font-bold text-indigo-600 mb-6 cursor-pointer hover:text-indigo-700 transition-colors" 
                             onclick="copyAmount()" 
                             id="amount-display">
                            <?php echo number_format($transaction['payment_amount'], 6); ?> USDT
                        </div>
                        <span class="tooltiptext">Amount copied!</span>
                    </div>
                    
                    <div class="text-sm text-gray-500 mb-2">Scan QR Code to Pay</div>
                    <div class="inline-block bg-white rounded-lg shadow">
                        <img src="<?php echo BASE_URL; ?>/public/qrcode.php?data=<?php echo urlencode($settings['usdt_address']); ?>" alt="USDT Payment QR Code" class="w-48 h-48">
                    </div>
                    <div class="mt-4">
                        <div class="text-sm text-gray-500">USDT Address (<?php echo htmlspecialchars($transaction['network']); ?>):</div>
                        <div class="mt-1 flex items-center">
                            <div class="p-2 bg-gray-100 rounded text-sm font-mono break-all flex-grow">
                                <?php echo htmlspecialchars($transaction['address']); ?>
                            </div>
                            <button id="copy-button" onclick="copyUSDTAddress()" 
                                    class="ml-1 px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Animation -->
            <div class="mt-6 mb-6 text-center" id="loading">
                <div class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-indigo-600 border-t-transparent"></div>
                <div class="mt-1 text-sm text-gray-600">Checking payment status...</div>
            </div>

            <div class="flex justify-center space-x-4">
                <button onclick="cancelTransaction()" class="bg-red-500 text-sm text-white px-4 py-2 rounded hover:bg-red-600">
                    Cancel Payment
                </button>
            </div>
        </div>
    </div>

    <script>
        // Countdown timer
        const expires = <?php echo $expires; ?>;
        function updateCountdown() {
            const now = Math.floor(Date.now() / 1000);
            const remaining = expires - now;
            
            if (remaining <= 0) {
                let countdown = document.getElementById('countdown');
                countdown.textContent = '00:00';
                countdown.style.color = 'red';
                cancelTransaction('timeout');
                return;
            }
            
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            document.getElementById('countdown').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);

        // Check payment status
        function checkPaymentStatus() {
            fetch('<?php echo BASE_URL; ?>/public/check_payment.php?order_id=<?php echo $order_id; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'completed') {
                        window.location.href = '<?php echo BASE_URL; ?>/success/<?php echo $order_id; ?>';
                    } else if (data.status === 'failed') {
                        window.location.href = '<?php echo BASE_URL; ?>/failed/<?php echo $order_id; ?>';
                    }
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                });
        }

        // Check payment status every 5 seconds
        setInterval(checkPaymentStatus, 5000);
        checkPaymentStatus(); // Initial check

        // Cancel transaction
        function cancelTransaction(reason = 'user_cancelled') {
            fetch('<?php echo BASE_URL; ?>/public/cancel_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: '<?php echo $order_id; ?>',
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                window.location.href = '<?php echo BASE_URL; ?>/failed/<?php echo $order_id; ?>';
            });
        }

        // Copy amount function
        function copyAmount() {
            const amount = '<?php echo $transaction['payment_amount']; ?>';
            navigator.clipboard.writeText(amount).then(() => {
                const tooltip = document.querySelector('.tooltip');
                tooltip.classList.add('show');
                setTimeout(() => {
                    tooltip.classList.remove('show');
                }, 2000);
            });
        }

        // Copy USDT address function
        function copyUSDTAddress() {
            const address = '<?php echo $transaction['address']; ?>';
            navigator.clipboard.writeText(address).then(() => {
                const copyButton = document.getElementById('copy-button');
                copyButton.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => {
                    copyButton.innerHTML = '<i class="fas fa-copy"></i>';
                }, 2000);
            });
        }
    </script>
</body>
</html> 