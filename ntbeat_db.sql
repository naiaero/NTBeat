-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2026 at 05:05 AM
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
-- Database: `ntbeat_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `konser`
--

CREATE TABLE `konser` (
  `id` int(11) NOT NULL,
  `nama_konser` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `tiket_terjual` int(11) DEFAULT 0,
  `poster` varchar(255) DEFAULT 'default-poster.jpg',
  `status` enum('Tersedia','Hampir Habis','Habis','Selesai','Arsip') DEFAULT 'Tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konser`
--

INSERT INTO `konser` (`id`, `nama_konser`, `tanggal`, `waktu`, `lokasi`, `harga`, `kapasitas`, `tiket_terjual`, `poster`, `status`, `created_at`) VALUES
(1, 'Mataram Sound Wave', '2026-06-15', '19:00:00', 'Eks Bandara Selaparang', 150000.00, 1000, 880, 'default-poster.jpg', 'Hampir Habis', '2026-05-16 02:57:39'),
(2, 'Senggigi Jazz', '2026-07-20', '16:00:00', 'Pantai Senggigi', 150000.00, 1000, 540, 'default-poster.jpg', 'Tersedia', '2026-05-16 02:57:39'),
(3, 'Symphony of Lombok', '2026-08-10', '19:30:00', 'Taman Budaya NTB', 200000.00, 500, 0, 'default-poster.jpg', 'Tersedia', '2026-05-16 02:57:39');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `konser_id` int(11) NOT NULL,
  `jumlah_tiket` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status_bayar` enum('Pending','Lunas','Dibatalkan') DEFAULT 'Lunas',
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `order_id`, `user_id`, `konser_id`, `jumlah_tiket`, `total_harga`, `status_bayar`, `tanggal_pesan`) VALUES
(1, 'NTB-20260516-001', 2, 1, 2, 300000.00, 'Lunas', '2026-05-16 02:57:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@ntbeat.com', '0192023a7bbd73250516f069df18b500', 'admin', '2026-05-16 02:57:13'),
(2, 'Salsabila', 'salsabila@student.unram.ac.id', '0192023a7bbd73250516f069df18b500', 'user', '2026-05-16 02:57:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `konser`
--
ALTER TABLE `konser`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `konser_id` (`konser_id`);

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
-- AUTO_INCREMENT for table `konser`
--
ALTER TABLE `konser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`konser_id`) REFERENCES `konser` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
