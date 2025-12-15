-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 03:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chlandestine`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_sessions`
--

CREATE TABLE `auth_sessions` (
  `session_id` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('UNPAID','PAID','CANCELLED') DEFAULT 'UNPAID',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `user_id`, `plan_id`, `amount`, `status`, `payment_method`, `created_at`) VALUES
('INV-20251101-001', 5, 1, 15000.00, 'PAID', 'Gopay', '2025-11-01 03:00:00'),
('INV-20251201-002', 6, 2, 50000.00, 'PAID', 'BCA', '2025-12-01 02:30:00'),
('INV-20251203-003', 5, 1, 15000.00, 'PAID', 'OVO', '2025-12-04 11:24:42'),
('INV-20251204-120', 5, 2, 50000.00, 'PAID', 'Transfer Bank', '2025-12-04 13:38:30'),
('INV-20251204-188', 7, 2, 50000.00, 'PAID', 'Transfer Bank', '2025-12-04 14:03:28'),
('INV-20251204-477', 7, 2, 50000.00, 'PAID', 'GoPay', '2025-12-04 14:05:51'),
('INV-20251208-353', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:19:53'),
('INV-20251208-444', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:19:11'),
('INV-20251208-455', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:19:50'),
('INV-20251208-472', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:19:50'),
('INV-20251208-501', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:20:11'),
('INV-20251208-599', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:19:53'),
('INV-20251208-604', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:19:48'),
('INV-20251208-652', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:20:15'),
('INV-20251208-883', 5, 1, 15000.00, 'PAID', 'Mandiri', '2025-12-08 06:23:50'),
('INV-20251211-335', 5, 1, 15000.00, 'PAID', 'BCA', '2025-12-11 18:52:01');

-- --------------------------------------------------------

--
-- Table structure for table `ip_access_logs`
--

CREATE TABLE `ip_access_logs` (
  `log_id` bigint(20) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` enum('REGISTER','LOGIN_SUCCESS','LOGIN_FAILED') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ip_access_logs`
--

INSERT INTO `ip_access_logs` (`log_id`, `ip_address`, `user_id`, `activity_type`, `timestamp`) VALUES
(1, '192.168.1.10', 1, 'LOGIN_SUCCESS', '2025-12-02 11:26:40'),
(2, '192.168.1.11', 5, 'LOGIN_SUCCESS', '2025-12-03 11:26:40'),
(3, '10.0.0.55', 6, 'LOGIN_SUCCESS', '2025-12-04 06:26:40'),
(4, '202.134.0.15', NULL, 'LOGIN_FAILED', '2025-12-04 11:26:40'),
(5, '::1', 3, 'LOGIN_SUCCESS', '2025-12-04 14:32:42'),
(6, '::1', NULL, 'LOGIN_FAILED', '2025-12-04 14:33:02'),
(7, '::1', 5, 'LOGIN_SUCCESS', '2025-12-04 14:37:03'),
(8, '::1', 3, 'LOGIN_SUCCESS', '2025-12-05 06:53:42'),
(9, '::1', 5, 'LOGIN_SUCCESS', '2025-12-05 06:53:59'),
(10, '::1', 3, 'LOGIN_SUCCESS', '2025-12-05 06:54:12'),
(11, '::1', 3, 'LOGIN_SUCCESS', '2025-12-05 07:40:19'),
(12, '::1', 5, 'LOGIN_SUCCESS', '2025-12-05 07:49:24'),
(13, '::1', 3, 'LOGIN_SUCCESS', '2025-12-05 07:49:51'),
(14, '::1', 5, 'LOGIN_SUCCESS', '2025-12-05 07:51:02'),
(15, '::1', 5, 'LOGIN_SUCCESS', '2025-12-08 06:19:00'),
(16, '::1', 3, 'LOGIN_SUCCESS', '2025-12-11 19:48:24'),
(17, '::1', 3, 'LOGIN_SUCCESS', '2025-12-11 19:48:41'),
(18, '118.99.113.0', 10, '', '2025-12-11 19:53:16'),
(19, '118.99.113.0', 10, 'LOGIN_SUCCESS', '2025-12-11 19:53:28');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Super user, bisa lihat semua log'),
(2, 'User', 'Pengguna biasa extension');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `token_amount` int(11) NOT NULL,
  `duration_days` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`plan_id`, `plan_name`, `price`, `token_amount`, `duration_days`) VALUES
(1, 'Paket Hemat', 15000.00, 1000, 30),
(2, 'Paket Sultan', 50000.00, 5000, 30);

-- --------------------------------------------------------

--
-- Table structure for table `system_errors`
--

CREATE TABLE `system_errors` (
  `error_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `error_code` varchar(50) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usage_logs`
--

CREATE TABLE `usage_logs` (
  `log_id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tokens_spent` int(11) NOT NULL,
  `action_type` enum('OCR','TRANSLATE','INPAINT') NOT NULL,
  `webtoon_source_url` text DEFAULT NULL,
  `execution_time_ms` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usage_logs`
--

INSERT INTO `usage_logs` (`log_id`, `user_id`, `tokens_spent`, `action_type`, `webtoon_source_url`, `execution_time_ms`, `created_at`) VALUES
(1, 5, 10, 'TRANSLATE', 'https://webtoon.com/tower-of-god/ep1', 1200, '2025-11-29 11:26:18'),
(2, 5, 15, 'TRANSLATE', 'https://webtoon.com/tower-of-god/ep2', 1500, '2025-11-30 11:26:18'),
(3, 5, 5, 'OCR', 'https://webtoon.com/onepiece/100', 800, '2025-12-02 11:26:18'),
(4, 6, 50, 'TRANSLATE', 'https://naver.com/manhwa/sololeveling', 4500, '2025-12-01 11:26:18'),
(5, 6, 20, 'INPAINT', 'https://naver.com/manhwa/sololeveling', 3000, '2025-12-01 11:26:18'),
(6, 6, 100, 'TRANSLATE', 'https://naver.com/manhwa/windbreaker', 9000, '2025-12-03 11:26:18'),
(7, 6, 10, 'OCR', 'https://test-image.com/sample', 500, '2025-12-04 10:26:18'),
(8, 1, 1, 'OCR', 'debug_test_image.jpg', 200, '2025-12-04 11:26:18'),
(9, 5, 10, 'TRANSLATE', 'https://webtoon.com/tower-of-god/ep1', 1200, '2025-11-29 11:26:18'),
(10, 5, 15, 'TRANSLATE', 'https://webtoon.com/tower-of-god/ep2', 1500, '2025-11-30 11:26:18'),
(11, 5, 5, 'OCR', 'https://webtoon.com/onepiece/100', 800, '2025-12-02 11:26:18'),
(12, 6, 50, 'TRANSLATE', 'https://naver.com/manhwa/sololeveling', 4500, '2025-12-01 11:26:18'),
(13, 6, 20, 'INPAINT', 'https://naver.com/manhwa/sololeveling', 3000, '2025-12-01 11:26:18'),
(14, 6, 100, 'TRANSLATE', 'https://naver.com/manhwa/windbreaker', 9000, '2025-12-03 11:26:18'),
(15, 6, 10, 'OCR', 'https://test-image.com/sample', 500, '2025-12-04 10:26:18'),
(16, 1, 1, 'OCR', 'debug_test_image.jpg', 200, '2025-12-04 11:26:18'),
(17, 5, 10, 'TRANSLATE', 'https://webtoon.com/tower-of-god/ep1', 1200, '2025-11-29 11:26:22'),
(18, 5, 15, 'TRANSLATE', 'https://webtoon.com/tower-of-god/ep2', 1500, '2025-11-30 11:26:22'),
(19, 5, 5, 'OCR', 'https://webtoon.com/onepiece/100', 800, '2025-12-02 11:26:22'),
(20, 6, 50, 'TRANSLATE', 'https://naver.com/manhwa/sololeveling', 4500, '2025-12-01 11:26:22'),
(21, 6, 20, 'INPAINT', 'https://naver.com/manhwa/sololeveling', 3000, '2025-12-01 11:26:22'),
(22, 6, 100, 'TRANSLATE', 'https://naver.com/manhwa/windbreaker', 9000, '2025-12-03 11:26:22'),
(23, 6, 10, 'OCR', 'https://test-image.com/sample', 500, '2025-12-04 10:26:22'),
(24, 1, 1, 'OCR', 'debug_test_image.jpg', 200, '2025-12-04 11:26:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT 2,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `username`, `email`, `password_hash`, `created_at`, `ip_address`) VALUES
(1, 1, 'eldwin_admin', 'eldwin.sianto@binus.ac.id', '$2y$10$yB9ngJUb9Iz77ih9xyIiZOb9soWm4OoYN5L06hkrmFoAhZdir02Ay', '2025-12-04 10:48:43', NULL),
(2, 1, 'taufiq_admin', 'taufiq.ayubi@binus.ac.id', '$2y$10$yB9ngJUb9Iz77ih9xyIiZOb9soWm4OoYN5L06hkrmFoAhZdir02Ay', '2025-12-04 10:48:43', NULL),
(3, 1, 'yosia_admin', 'yosia.boimau@binus.ac.id', '$2y$10$yB9ngJUb9Iz77ih9xyIiZOb9soWm4OoYN5L06hkrmFoAhZdir02Ay', '2025-12-04 10:48:43', NULL),
(4, 1, 'rauh_admin', 'rauh.abdillah@binus.ac.id', '$2y$10$yB9ngJUb9Iz77ih9xyIiZOb9soWm4OoYN5L06hkrmFoAhZdir02Ay', '2025-12-04 10:48:43', NULL),
(5, 2, 'agus_user', 'agus.andi@gmail.com', '$2y$10$yB9ngJUb9Iz77ih9xyIiZOb9soWm4OoYN5L06hkrmFoAhZdir02Ay', '2025-12-04 10:48:43', NULL),
(6, 2, 'yosa_user', 'yosa.sune@gmail.com', '$2y$10$yB9ngJUb9Iz77ih9xyIiZOb9soWm4OoYN5L06hkrmFoAhZdir02Ay', '2025-12-04 10:48:43', NULL),
(7, 2, 'Daniel Sentosa', 'danielsa@gmail.com', '$2y$10$yB9ngJUb9Iz77ih9xyIiZOb9soWm4OoYN5L06hkrmFoAhZdir02Ay', '2025-12-04 14:02:54', NULL),
(9, 2, 'Andreas Prasetyo', 'andreas.prasetyo@gmail.com', '$2y$10$xntXNkJApD.NSiOCApMrquAcB/aP2NXyHOCzK6PBob9R3JPiDKCEy', '2025-12-11 18:49:15', NULL),
(10, 2, 'Yohanes', 'yohanesaja@gmail.com', '$2y$10$p2d/WgjxVYoXFRPelnHzH..4MI2cpheiY23Ip64YV/qbURZuwZ7eS', '2025-12-11 19:53:16', '118.99.113.0');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `setting_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `target_language` varchar(5) DEFAULT 'id',
  `font_size` int(11) DEFAULT 12,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`setting_id`, `user_id`, `target_language`, `font_size`, `updated_at`) VALUES
(1, 1, 'id', 12, '2025-12-04 11:23:58'),
(2, 2, 'id', 12, '2025-12-04 11:23:58'),
(3, 3, 'id', 12, '2025-12-04 11:23:58'),
(4, 4, 'id', 12, '2025-12-04 11:23:58'),
(5, 5, 'en', 14, '2025-12-04 11:23:58'),
(6, 6, 'id', 12, '2025-12-04 11:23:58'),
(7, 7, 'en', 20, '2025-12-04 14:03:19');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `wallet_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `balance` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`wallet_id`, `user_id`, `balance`, `last_updated`) VALUES
(1, 1, 99999, '2025-12-04 10:50:40'),
(2, 2, 99999, '2025-12-04 10:50:40'),
(3, 3, 99999, '2025-12-04 10:50:40'),
(4, 4, 99999, '2025-12-04 10:50:40'),
(5, 5, 17000, '2025-12-11 18:52:15'),
(6, 6, 5000, '2025-12-04 10:50:40'),
(7, 7, 10000, '2025-12-04 14:06:00'),
(9, 9, 0, '2025-12-11 18:49:15'),
(10, 10, 0, '2025-12-11 19:53:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_sessions`
--
ALTER TABLE `auth_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `ip_access_logs`
--
ALTER TABLE `ip_access_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `system_errors`
--
ALTER TABLE `system_errors`
  ADD PRIMARY KEY (`error_id`);

--
-- Indexes for table `usage_logs`
--
ALTER TABLE `usage_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`wallet_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ip_access_logs`
--
ALTER TABLE `ip_access_logs`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_errors`
--
ALTER TABLE `system_errors`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usage_logs`
--
ALTER TABLE `usage_logs`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_sessions`
--
ALTER TABLE `auth_sessions`
  ADD CONSTRAINT `auth_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`plan_id`);

--
-- Constraints for table `usage_logs`
--
ALTER TABLE `usage_logs`
  ADD CONSTRAINT `usage_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
