-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 10:24 AM
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
-- Database: `housing`
--

-- --------------------------------------------------------

--
-- Table structure for table `featured_units`
--

CREATE TABLE `featured_units` (
  `id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `county` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `images` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `featured_units`
--

INSERT INTO `featured_units` (`id`, `type`, `county`, `location`, `price`, `images`) VALUES
(3, '2 Bedroom BnB with Free WIFI, Smart TV, Fully Furnished, Secure Parking and 24/7 Security', 'Embu', 'Embu', 2500.00, '1755848016_feem_1208097f611c5_IMG-20250821-WA0000.jpg,1755848016_feem_1208097f611c5_IMG-20250821-WA0001.jpg,1755848016_feem_1208097f611c5_IMG-20250821-WA0002.jpg,1755848016_feem_1208097f611c5_IMG-20250821-WA0003.jpg,1755848016_feem_1208097f611c5_IMG-20250821-WA0004.jpg'),
(4, 'Bungalow 3 Bedroom', 'Kiambu', 'Ruiru, Kenyatta road exit 14 Thika Super Highway', 13850000.00, '1755848250_feem_1208097f611c5_IMG-20250821-WA0005.jpg'),
(5, '4 Bedroom Maisonette', 'Kiambu', 'Ruiru, Along Gitunguri Road', 20850000.00, '1755848387_feem_1208097f611c5_IMG-20250821-WA0006.jpg'),
(6, 'Green Phase 2 Gated Community', 'Embu', 'Embu', 6500000.00, '1755848503_feem_1208097f611c5_IMG-20250821-WA0007.jpg,1755848503_feem_1208097f611c5_IMG-20250821-WA0008.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `housing_units`
--

CREATE TABLE `housing_units` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `county` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `premises_name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `units` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `housing_units`
--

INSERT INTO `housing_units` (`id`, `owner_id`, `county`, `location`, `premises_name`, `type`, `units`, `price`, `created_at`) VALUES
(1, 8, 'Tharaka Nithi', 'Chiakariga', 'Mugendi Ventures', 'Villa', 20, 20000.00, '2025-08-18 10:15:23'),
(2, 8, 'Murang\'a', 'Murang\'a Town', 'DebbyVentures', 'Bungalow', 30, 10000.00, '2025-08-18 11:20:42'),
(4, 10, 'Kirinyaga', 'Mwea Ngurubani', 'Ngurubani Coaches', 'Bungalow', 20, 113000.00, '2025-08-19 06:54:24'),
(5, 10, 'Meru', 'Nkubu', 'BREEZE', 'Bedsitter', 20, 5500.00, '2025-08-19 07:00:23'),
(7, 11, 'Nairobi', 'Karen', 'Wanjeru Plaza', 'Apartment', 20, 30000.00, '2025-08-21 07:26:28'),
(8, 10, 'Meru', 'Nkubu', 'BUME ZONE', '2 Bedroom', 20, 35000.00, '2025-08-21 07:38:45'),
(9, 11, 'Kirinyaga', 'Kamugunda', 'Kamugunda 1', 'Single Room', 30, 3500.00, '2025-08-21 07:50:21'),
(10, 11, 'Kirinyaga', 'Kerugoya', 'Ngurubani 3', 'Bungalow', 24, 20000.00, '2025-08-21 08:16:24'),
(11, 11, 'Kirinyaga', 'Kamugunda', 'Kamugunda 1', 'Bungalow', 10, 50000.00, '2025-08-21 08:41:32'),
(12, 13, 'Embu', 'Muthatari', 'Gatondo 1', 'Bungalow', 10, 200000.00, '2025-08-22 08:18:10'),
(13, 11, 'Kiambu', 'Ruiru', 'Wanjeru 4', 'Bungalow', 34, 34567.00, '2025-08-22 09:59:26'),
(14, 11, 'Embu', 'Gakwegori', 'Muiruri 2', 'Office', 10, 16000.00, '2025-08-22 12:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `landlords`
--

CREATE TABLE `landlords` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `second_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `plan` varchar(50) NOT NULL,
  `billing` varchar(20) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landlords`
--

INSERT INTO `landlords` (`id`, `first_name`, `second_name`, `username`, `email`, `password`, `created_at`, `plan`, `billing`, `price`) VALUES
(6, 'Abigael', 'Kendi', 'abby', 'kendiabby@gmail.com', '$2y$10$TSkj21wG6aoUjcEK2UKvNOTayWMrXvvDW4YLui205PDxzF0aVA/zW', '2025-08-13 12:22:52', '', '', 0),
(7, 'vicksy', 'blessed', 'pasi', 'murimivictor106@gmail.com', '$2y$10$SLxOAi6s/XLnBi1c1JOcjudtsydMUbsssyKw.icskGZ60yM1IrSUO', '2025-08-15 16:20:38', '', '', 0),
(11, 'Liz', 'Muriithi', 'Lim', 'liz@gmail.com', '$2y$10$RuDsjabzq/I5Gb5KASZoeOUQ1GdJ2SVLk8Dzpap.Xf8JI1For0gYu', '2025-08-21 07:24:02', '', '', 0),
(12, 'Precious', 'Joy', 'Precious', 'joy@gmail.com', '$2y$10$gbTllDwTaV/TbD.chg4SGuqFDmfvUV/cM8CeHrR0J9oZKlL12RyEq', '2025-08-21 13:21:16', 'Professional', 'monthly', 1500),
(13, 'Zedah', 'Solutions', 'ZPS', 'zedahrealty@gmail.com', '$2y$10$FaroHcAon6OBasjCLFy17edUzUvPG41zsfuUQZKMWfJRlfIE6eBfO', '2025-08-22 08:12:09', 'Enterprise', 'yearly', 80000);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`) VALUES
(1, 'liz@gmail.com', '67a7319e833ea50ed2d058a474f85ae301d76a6d8c5349393d0f66d1ac13b58ab3a16bcb9b0d20daf7d5a4ccf05eabbbb18b', '2025-08-28 11:02:09');

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`id`, `property_id`, `image_path`, `uploaded_at`) VALUES
(1, 1, 'uploads/1755512123_background.jpg', '2025-08-18 10:15:23'),
(2, 1, 'uploads/1755512123_front.jpg', '2025-08-18 10:15:23'),
(3, 2, 'uploads/1755516043_rentcol.jpg', '2025-08-18 11:20:43'),
(4, 2, 'uploads/1755516043_salemkt.jpeg', '2025-08-18 11:20:43'),
(7, 4, 'uploads/1755586464_logo.png', '2025-08-19 06:54:24'),
(8, 4, 'uploads/1755586464_whatsapp.png', '2025-08-19 06:54:24'),
(9, 5, 'uploads/1755586823_salemkt.jpeg', '2025-08-19 07:00:23'),
(10, 5, 'uploads/1755586823_security.jpeg', '2025-08-19 07:00:23'),
(13, 7, 'uploads/1755761188_salemkt.jpeg', '2025-08-21 07:26:28'),
(14, 8, 'uploads/1755761925_background.jpg', '2025-08-21 07:38:45'),
(15, 8, 'uploads/1755761925_finance.jpg', '2025-08-21 07:38:45'),
(16, 9, 'uploads/1755762621_about2.jpg', '2025-08-21 07:50:21'),
(17, 9, 'uploads/1755762621_front.jpg', '2025-08-21 07:50:21'),
(18, 10, 'uploads/1755764184_about1.jpg', '2025-08-21 08:16:24'),
(19, 10, 'uploads/1755764185_background.jpg', '2025-08-21 08:16:25'),
(20, 11, 'uploads/1755765692_main1.jpg', '2025-08-21 08:41:32'),
(21, 11, 'uploads/1755765692_rentcol.jpg', '2025-08-21 08:41:32'),
(22, 12, 'uploads/1755850690_feem_1208097f611c5_IMG-20250821-WA0005.jpg', '2025-08-22 08:18:10'),
(23, 13, 'uploads/1755856766_rentcol.jpg', '2025-08-22 09:59:26'),
(24, 14, 'uploads/1755867529_house15.jpg', '2025-08-22 12:58:49'),
(25, 14, 'uploads/1755867529_main1.jpg', '2025-08-22 12:58:49'),
(26, 14, 'uploads/1755867529_rentcol.jpg', '2025-08-22 12:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `county` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `premises_name` varchar(150) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `age` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `alt_phone` varchar(20) DEFAULT NULL,
  `unit_type` varchar(100) NOT NULL,
  `unit_number` varchar(50) NOT NULL,
  `amount_charged` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `owner_id`, `county`, `location`, `premises_name`, `full_name`, `age`, `email`, `phone`, `alt_phone`, `unit_type`, `unit_number`, `amount_charged`, `created_at`) VALUES
(7, 11, 'Nairobi', 'Karen', 'Wanjeru Plaza', 'Precious Joy', 21, 'joy@gmail.com', '0794392928', '0701020304', '2 Bedroom', '4', 30000.00, '2025-08-21 14:04:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `featured_units`
--
ALTER TABLE `featured_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `housing_units`
--
ALTER TABLE `housing_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landlords`
--
ALTER TABLE `landlords`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `featured_units`
--
ALTER TABLE `featured_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `housing_units`
--
ALTER TABLE `housing_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `landlords`
--
ALTER TABLE `landlords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `housing_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `landlords` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
