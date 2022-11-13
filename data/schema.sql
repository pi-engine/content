-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2022 at 06:05 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `risk`
--

-- --------------------------------------------------------

--
-- Table structure for table `cntent_item`
--

CREATE TABLE `cntent_item` (
                                  `id` int(10) NOT NULL,
                                  `slug` varchar(255) DEFAULT NULL,
                                  `title` text DEFAULT NULL,
                                  `description` text DEFAULT NULL,
                                  `image` varchar(10) DEFAULT NULL,
                                  `image_uri` text DEFAULT NULL,
                                  `type` varchar(255) DEFAULT NULL,
                                  `status` int(10) DEFAULT NULL,
                                  `author_id` int(10) DEFAULT NULL,
                                  `time_create` int(10) NOT NULL DEFAULT 0,
                                  `time_update` int(10) NOT NULL DEFAULT 0,
                                  `time_delete` int(10) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cntent_item`
--
ALTER TABLE `cntent_item`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cntent_item`
--
ALTER TABLE `cntent_item`
    MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
