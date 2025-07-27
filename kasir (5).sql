-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Jul 2025 pada 23.34
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
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `nama` varchar(50) NOT NULL,
  `harga` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id_barang`, `tanggal_masuk`, `nama`, `harga`, `jumlah`) VALUES
(5, '2025-06-03 22:23:00', 'Meow Kitten 500g (Bunga)', 22000, 32),
(6, '2025-06-03 22:24:00', 'Excel Chicken & Tuna (Segitiga) 500g', 12500, 12),
(7, '2025-06-03 22:24:00', 'Meow Persian Adult 500g (Bintang)', 22000, 12),
(8, '2025-06-03 22:24:00', 'Meow Persian Kitten (Kotak)', 23000, 29),
(9, '2025-06-03 22:25:00', 'Meow Salmon 500g', 18000, 16),
(10, '2025-06-03 22:25:00', 'Meow Tuna 500g', 18000, 17),
(11, '2025-06-03 22:25:00', 'Tuna Kibbles Ikan', 18000, 12),
(12, '2025-06-03 22:26:00', 'Kitten Hair & Skin Tuna', 20000, 24),
(13, '2025-06-03 22:26:00', 'Kitten Hair & Skin Salmon', 20000, 15),
(14, '2025-06-03 22:26:00', 'Meow Proplain Chicken', 55000, 25),
(15, '2025-06-03 22:27:00', 'Pasir Injae Bentoine 5L', 20000, 26),
(16, '2025-06-03 22:27:00', 'Cat Choize Kitten Salmon with Milk', 24000, 13),
(17, '2025-06-03 22:27:00', 'Cat Choize Adult Salmon', 17000, 36),
(18, '2025-06-03 22:28:00', 'Cat Choize Adult Tuna', 17000, 12),
(19, '2025-06-03 22:28:00', 'Cat Choize Kitten Tuna with Milk', 24000, 22),
(20, '2025-06-03 22:28:00', 'Excel Chicken & Tuna (Pink)', 12500, 42),
(21, '2025-06-03 22:28:00', 'Excel Salmon (Kuning)', 12500, 14),
(22, '2025-06-03 22:29:00', 'Excel Tuna (Pink Fanta)', 11000, 25);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL,
  `tanggal_keluar` datetime NOT NULL,
  `nama_pengeluaran` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `tanggal_keluar`, `nama_pengeluaran`, `jumlah`) VALUES
(3, '2025-07-25 00:18:00', 'Gaji Karyawan', 10000000),
(4, '2025-07-25 00:18:00', 'Sedekah', 500000),
(5, '2025-07-25 00:19:00', 'Uang Makan Karyawan', 500000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `role`
--

INSERT INTO `role` (`id_role`, `nama`) VALUES
(1, 'Admin'),
(2, 'Pemilik'),
(5, 'User');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` bigint(20) NOT NULL,
  `total` bigint(20) NOT NULL,
  `bayar` bigint(20) NOT NULL,
  `kembali` bigint(20) NOT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `total`, `bayar`, `kembali`, `payment_method`, `payment_status`, `order_id`, `created_at`) VALUES
(1, 18000, 20000, 2000, 'cash', 'paid', 'CASH-1753035035-6320', '2025-07-21 01:10:35'),
(2, 330000, 330000, 0, 'qris', 'paid', 'QRIS-1753035051-2599', '2025-07-21 01:10:56'),
(3, 266000, 300000, 34000, 'cash', 'paid', 'CASH-1753035205-9125', '2025-07-21 01:13:25'),
(4, 25000, 25000, 0, 'qris', 'paid', 'QRIS-1753035220-4794', '2025-07-21 01:13:43'),
(5, 220000, 250000, 30000, 'cash', 'paid', 'CASH-1753035281-6615', '2025-07-21 01:14:41'),
(6, 55500, 55500, 0, 'qris', 'paid', 'QRIS-1753035307-1071', '2025-07-21 01:15:08'),
(7, 110000, 110000, 0, 'qris', 'paid', 'QRIS-1753043905-6319', '2025-07-21 03:38:27'),
(8, 24000, 30000, 6000, 'cash', 'paid', 'CASH-1753377336-4825', '2025-07-25 00:15:36'),
(9, 12500, 12500, 0, 'qris', 'pending', 'QRIS-1753377354-3736', '2025-07-25 00:15:55'),
(10, 12500, 12500, 0, 'qris', 'pending', 'QRIS-1753377355-9272', '2025-07-25 00:15:58'),
(11, 12500, 12500, 0, 'qris', 'pending', 'QRIS-1753377358-6764', '2025-07-25 00:15:59'),
(12, 12500, 12500, 0, 'qris', 'pending', 'QRIS-1753377359-8398', '2025-07-25 00:16:04'),
(13, 12500, 12500, 0, 'qris', 'pending', 'QRIS-1753377364-5792', '2025-07-25 00:16:05'),
(14, 12500, 12500, 0, 'qris', 'paid', 'QRIS-1753377365-5065', '2025-07-25 00:16:07'),
(15, 24000, 50000, 26000, 'cash', 'paid', 'CASH-1753400660-1804', '2025-07-25 06:44:20'),
(16, 120000, 200000, 80000, 'cash', 'paid', 'CASH-1753400699-2999', '2025-07-25 06:44:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id_transaksi_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `harga` bigint(20) NOT NULL,
  `qty` int(11) NOT NULL,
  `total` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id_transaksi_detail`, `id_transaksi`, `id_barang`, `nama`, `harga`, `qty`, `total`) VALUES
(1, 1, 9, 'Meow Salmon 500g', 18000, 1, 18000),
(2, 2, 14, 'Meow Proplain Chicken', 55000, 6, 330000),
(3, 3, 19, 'Cat Choize Kitten Tuna with Milk', 24000, 9, 216000),
(4, 3, 20, 'Excel Chicken & Tuna (Pink)', 12500, 4, 50000),
(5, 4, 21, 'Excel Salmon (Kuning)', 12500, 2, 25000),
(6, 5, 5, 'Meow Kitten 500g (Bunga)', 22000, 10, 220000),
(7, 6, 11, 'Tuna Kibbles Ikan', 18000, 1, 18000),
(8, 6, 6, 'Excel Chicken & Tuna (Segitiga) 500g', 12500, 3, 37500),
(9, 7, 5, 'Meow Kitten 500g (Bunga)', 22000, 5, 110000),
(10, 8, 19, 'Cat Choize Kitten Tuna with Milk', 24000, 1, 24000),
(11, 9, 20, 'Excel Chicken & Tuna (Pink)', 12500, 1, 12500),
(12, 15, 16, 'Cat Choize Kitten Salmon with Milk', 24000, 1, 24000),
(13, 16, 16, 'Cat Choize Kitten Salmon with Milk', 24000, 5, 120000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `role_id`) VALUES
(2, 'Angel', 'angel', '12345', 1),
(6, 'Loddy', 'loddy', '12345', 2),
(7, 'User', 'user', '12345', 5);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indeks untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`);

--
-- Indeks untuk tabel `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indeks untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id_transaksi_detail`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id_transaksi_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
