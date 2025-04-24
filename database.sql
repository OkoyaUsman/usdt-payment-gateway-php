-- Create database
CREATE DATABASE IF NOT EXISTS usdtpay;
USE usdtpay;

-- Admin users table
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings table
CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `usdt_address` varchar(100) NOT NULL,
  `network` varchar(20) NOT NULL,
  `checkout_timeout` int(11) DEFAULT 1800,
  `check_interval` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Transactions table
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `real_amount` decimal(20,6) NOT NULL,
  `payment_amount` decimal(20,6) NOT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
  `address` varchar(100) DEFAULT NULL,
  `network` varchar(20) DEFAULT NULL,
  `tx_hash` text DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$Lxbepuuz9CzBPwmsPGcifefPLmXQ9Wx7kWwzK45shi8r.OqcehH.G', 'admin@example.com', '2025-04-23 13:48:36', '2025-04-23 13:48:36');

-- Insert default settings
INSERT INTO `settings` (`id`, `usdt_address`, `network`, `checkout_timeout`, `check_interval`, `created_at`, `updated_at`) VALUES
(1, 'TYKvowwuNfbWC28QALDbsgxRomxCNRfeCZ', 'TRC20', 3600, 60, '2025-04-23 13:46:07', '2025-04-24 07:24:13');

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;