-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 02:48 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chat_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `receiver_email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_encrypted` tinyint(1) DEFAULT 0,
  `encryption_method` varchar(20) DEFAULT NULL,
  `encryption_key` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_email`, `receiver_email`, `message`, `timestamp`, `is_encrypted`, `encryption_method`, `encryption_key`) VALUES
(19, 'emon@gmail.com', 'fahim@gmail.com', 'hi', '2025-04-30 09:26:07', 0, NULL, NULL),
(20, 'emon@gmail.com', 'fahim@gmail.com', '[SECRET]RIJSS', '2025-04-30 09:26:53', 0, NULL, NULL),
(21, 'fahim@gmail.com', 'emon@gmail.com', '[SECRET]CFSUPM', '2025-04-30 09:28:51', 0, NULL, NULL),
(22, 'emon@gmail.com', 'fahim@gmail.com', '[SECRET]', '2025-04-30 09:31:44', 0, NULL, NULL),
(23, 'fahim@gmail.com', 'emon@gmail.com', '[SECRET]', '2025-04-30 09:46:40', 0, NULL, NULL),
(24, 'fahim@gmail.com', 'aunik@gmail.com', 'Hi', '2025-04-30 09:59:24', 0, NULL, NULL),
(25, 'aunik@gmail.com', 'fahim@gmail.com', 'hello', '2025-04-30 10:00:48', 0, NULL, NULL),
(26, 'aunik@gmail.com', 'fahim@gmail.com', '[SECRET]RSUHFAYFY', '2025-04-30 10:01:10', 0, NULL, NULL),
(27, 'fahim@gmail.com', 'aunik@gmail.com', '[SECRET]IV', '2025-04-30 10:02:03', 0, NULL, NULL),
(28, 'aunik@gmail.com', 'fahim@gmail.com', '[SECRET]GKMG', '2025-04-30 10:03:01', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`) VALUES
(3, 'fahim@gmail.com', '1234'),
(5, 'abdullah@gmail.com', '1234'),
(6, 'aunik@gmail.com', '1234'),
(7, 'emon@gmail.com', '1234');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
