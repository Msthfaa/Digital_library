-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 12:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digital_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `synopsis` text DEFAULT NULL,
  `cover_image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `year`, `category`, `synopsis`, `cover_image_url`, `created_at`) VALUES
(1, 'Negeri 5 Menara', 'A. Fuadi', 2009, 'Fiksi', '', 'https://gpu.id/data-gpu/images/uploads/dirimg_buku/re_buku_picture_update_85642.jpg', '2025-06-04 10:41:59'),
(2, 'Ayat-Ayat Cinta', 'Habiburrahman El Shirazy', 2004, 'Religi', '', 'https://gpu.id/data-gpu/images/img-book/94606/624221030.jpg', '2025-06-04 10:41:59'),
(3, 'Dilan 1990', 'Pidi Baiq', 2014, 'Romansa', '', 'https://gpu.id/data-gpu/images/img-book/94757/624171012.jpg', '2025-06-04 10:41:59'),
(4, 'Perahu Kertas', 'Dee Lestari', 2009, 'Fiksi', '', 'https://gpu.id/data-gpu/images/uploads/book/a1cc220da8738dbb71153777f701f754.jpg', '2025-06-04 10:41:59'),
(5, 'Sang Pemimpi', 'Andrea Hirata', 2006, 'Fiksi', '', 'https://gpu.id/data-gpu/images/uploads/dirimg_buku/re_buku_picture_80996.jpg', '2025-06-04 10:41:59'),
(6, 'Bumi', 'Tere Liye', 2014, 'Fantasi', '', 'https://gpu.id/data-gpu/images/img-book/94821/625123002.jpg', '2025-06-04 10:41:59'),
(7, 'Rindu', 'Tere Liye', 2015, 'Fiksi', '', 'https://gpu.id/data-gpu/images/uploads/dirimg_buku/616172003.jpg', '2025-06-04 10:41:59'),
(8, 'Cantik Itu Luka', 'Eka Kurniawan', 2002, 'Fiksi', '', 'https://gpu.id/data-gpu/images/img-book/93810/622202017.jpg', '2025-06-04 10:41:59'),
(9, 'Pulang', 'Tere Liye', 2015, 'Aksi', '', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/items/pulang_tere_liye.jpeg', '2025-06-04 10:41:59'),
(10, 'Orang-Orang Biasa', 'Andrea Hirata', 2019, 'Fiksi', 'a', 'https://cdn.gramedia.com/uploads/items/9786022915249_orang-orang-b.jpg', '2025-06-04 10:41:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `books` ADD FULLTEXT KEY `idx_search` (`title`,`author`,`category`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


