-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 24 Feb 2026 pada 06.06
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_siwarga`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `pekerjaan` varchar(255) DEFAULT NULL,
  `alamat` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `dokumen_pdf` varchar(255) DEFAULT NULL,
  `agama` enum('Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya') NOT NULL DEFAULT 'Islam',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `no_hp`, `tanggal_lahir`, `jenis_kelamin`, `pekerjaan`, `alamat`, `foto`, `dokumen_pdf`, `agama`, `created_at`) VALUES
(4, 'saiman', 'mansai99@gmail.com', '089763422624', '1988-11-24', 'L', 'Lain-lain', 'rt 76 rw 08 dusun tempur desa rusuh', 'uploads/images/img_20260224_114813_8b2f887653b3.jpg', 'uploads/pdfs/pdf_20260224_113806_b9f60fe1c975.pdf', 'Hindu', '2026-02-24 04:38:06'),
(5, 'zainur', 'admin@gmail.com', '089763422698', '2010-09-05', 'L', 'Lain-lain', 'RT 01 RW 03 DUSUN parang desa pring', 'uploads/images/img_20260224_113933_f3a9cba83959.jpg', 'uploads/pdfs/pdf_20260224_113934_d333f09c19a8.pdf', 'Kristen', '2026-02-24 04:39:34');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
