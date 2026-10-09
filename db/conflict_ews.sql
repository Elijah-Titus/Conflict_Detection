-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2026 at 03:50 PM
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
(3, 'grazing_dispute', 2, 'the cows are entering my farm', 'Makurdi', 5.048000, 7.933200, 'reported', '2026-10-08 10:05:18', NULL),
(4, 'violence', 2, 'Gun shot', 'Makurdi', 5.048000, 7.933200, 'reported', '2026-10-08 10:14:00', NULL),
(5, 'violence', 2, 'Gun shot', 'Makurdi', 5.048000, 7.933200, 'reported', '2026-10-08 10:14:45', NULL),
(6, 'violence', 2, 'Gun shot', 'Makurdi', 5.048000, 7.933200, 'reported', '2026-10-08 10:16:21', NULL),
(7, 'violence', 2, 'Gun shot', 'Makurdi', 5.048000, 7.933200, 'reported', '2026-10-08 10:16:23', NULL),
(8, 'violence', 3, 'Bombing Everywere', 'Makurdi', 6.333200, 5.623800, 'reported', '2026-10-08 14:40:25', 1);

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
  `role` enum('farmer','admin') NOT NULL DEFAULT 'farmer',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Elijah Titus', 'elijahdrift67@gmail.com', '08135005785', '$2y$10$kbqjjblSH61GcbOgKaHF3eM.4l8gtjah7TFdnqjD437E2hD4/QSzC', 'farmer', '2026-10-08 10:59:20');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message_text` varchar(2000) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `chat_messages_user_id_id` (`user_id`, `id`),
  CONSTRAINT `chat_messages_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
