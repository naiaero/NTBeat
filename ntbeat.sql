-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 12:12 PM
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
-- Database: `ntbeat`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `email` (`email`);

-- --------------------------------------------------------

--
-- Table structure for table `konser`
--

CREATE TABLE IF NOT EXISTS `konser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_konser` varchar(150) NOT NULL,
  `lineup` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `tiket_terjual` int(11) DEFAULT 0,
  `poster` varchar(255) DEFAULT 'default-poster.jpg',
  `status` enum('Tersedia','Hampir Habis','Habis','Selesai','Arsip') DEFAULT 'Tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE IF NOT EXISTS `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `konser_id` int(11) NOT NULL,
  `jumlah_tiket` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status_bayar` enum('Pending','Lunas','Dibatalkan') DEFAULT 'Lunas',
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `fk_pesanan_konser` (`konser_id`),
  CONSTRAINT `fk_pesanan_konser` FOREIGN KEY (`konser_id`) REFERENCES `konser` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

