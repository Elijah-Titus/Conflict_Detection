-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 04:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `conflict_ews`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Admin', 'ogahtitus37@gmail.com', '$2y$10$9CTClwnUUm0wiUYmJnCSHOtZ31ada1UnmtGt4iuOAwzM8cFOb.wB6', '2026-10-08 16:28:04');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message_text` varchar(2000) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `sender_type`, `message_text`, `created_at`) VALUES
(3, 1, 'admin', 'are you ok now', '2026-10-08 17:18:26'),
(4, 1, 'user', 'Yes', '2026-10-08 17:19:20'),
(5, 2, 'user', 'hi', '2026-10-08 17:38:40'),
(6, 2, 'user', 'Problem dey here', '2026-10-08 21:35:53'),
(7, 2, 'user', 'omo', '2026-10-08 21:36:15'),
(8, 1, 'user', 'thank you for the Help', '2026-10-09 07:37:34'),
(9, 1, 'admin', 'hw fa', '2026-10-09 14:38:38'),
(10, 1, 'user', 'yo', '2026-10-09 14:38:56');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `incident_type` enum('crop_damage','grazing_dispute','water_dispute','violence','tension') NOT NULL,
  `severity` tinyint(4) NOT NULL,
  `description` text DEFAULT NULL,
  `lga` varchar(100) NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `status` enum('reported','verified','resolved') DEFAULT 'reported',
  `reported_at` datetime DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `incident_type`, `severity`, `description`, `lga`, `latitude`, `longitude`, `status`, `reported_at`, `user_id`) VALUES
(1, 'crop_damage', 2, 'cow in my farm', 'Makurdi', 7.725120, 8.558788, 'reported', '2026-10-07 18:23:02', NULL),
(2, 'violence', 3, 'I\'m hearing Gun shot', 'Makurdi', 7.725242, 8.559285, 'reported', '2026-10-07 18:26:49', NULL),
(3, 'grazing_dispute', 2, 'the cows are entering my farm', 'Makurdi', 7.740000, 8.500000, 'reported', '2026-10-08 10:05:18', NULL),
(7, 'violence', 2, 'Gun shot', 'Makurdi', 7.725000, 8.540000, 'verified', '2026-10-08 10:16:23', NULL),
(8, 'violence', 3, 'Bombing Everywere', 'Makurdi', 7.710000, 8.530000, 'resolved', '2026-10-08 14:40:25', 1),
(9, 'grazing_dispute', 3, 'Cattle moved into harvested farmland near the road; farmers gathering to protest.', 'Gboko', 7.330000, 9.010000, 'reported', '2026-10-08 03:15:33', 1),
(10, 'crop_damage', 2, 'Yam seedlings trampled overnight on about two hectares.', 'Gboko', 7.315000, 8.990000, 'verified', '2026-10-07 03:15:33', 2),
(11, 'water_dispute', 2, 'Disagreement at the stream over access for cattle and washing.', 'Gboko', 7.340000, 9.020000, 'reported', '2026-10-06 03:15:33', 1),
(12, 'tension', 2, 'Rising tension after a missing-cattle accusation at the market.', 'Otukpo', 7.195000, 8.135000, 'reported', '2026-10-07 03:15:33', 2),
(13, 'tension', 1, 'Verbal argument over a boundary path, settled on the spot.', 'Otukpo', 7.180000, 8.120000, 'resolved', '2026-10-05 03:15:33', 1),
(14, 'crop_damage', 1, 'Minor damage to the edge of a cassava plot; owner identified.', 'Katsina-Ala', 7.170000, 9.280000, 'verified', '2026-10-04 03:15:33', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `created_at`) VALUES
(1, 'Elijah Titus', 'elijahdrift67@gmail.com', '08135005785', '$2y$10$kbqjjblSH61GcbOgKaHF3eM.4l8gtjah7TFdnqjD437E2hD4/QSzC', '2026-10-08 10:59:20'),
(2, 'Elijah Titus', 'elijahtituspound@gmail.com', '08135005782', '$2y$10$QXHGi8O0n42HYGt2t7mtQ.CkLH3FV7O0/jEFNMkaFASvGT3JXUwt.', '2026-10-08 17:36:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_user_id_id` (`user_id`,`id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
