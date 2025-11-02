-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Waktu pembuatan: 02 Nov 2025 pada 12.49
-- Versi server: 8.0.40
-- Versi PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gizi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alternatif`
--

CREATE TABLE `alternatif` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `sex` enum('L','P') NOT NULL,
  `tgl_timbang` date NOT NULL,
  `tgl_lahir` date NOT NULL,
  `umur` int NOT NULL,
  `bb` float(8,2) NOT NULL,
  `tb` float(8,2) NOT NULL,
  `z_score_bb_u` decimal(6,3) DEFAULT NULL,
  `z_score_tb_u` decimal(6,3) DEFAULT NULL,
  `z_score_bb_tb` decimal(6,3) DEFAULT NULL,
  `status_bb_u` varchar(50) DEFAULT NULL,
  `status_tb_u` varchar(50) DEFAULT NULL,
  `status_bb_tb` varchar(50) DEFAULT NULL,
  `imt` decimal(6,3) DEFAULT NULL,
  `z_score_imt` float DEFAULT NULL,
  `status_imt` varchar(255) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `derajat_preferensi`
--

CREATE TABLE `derajat_preferensi` (
  `id` int NOT NULL,
  `alternatif_1_id` int NOT NULL,
  `alternatif_2_id` int NOT NULL,
  `nilai` float NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `konversi_nilai`
--

CREATE TABLE `konversi_nilai` (
  `id` int NOT NULL,
  `alternatif_id` int NOT NULL,
  `k1` decimal(5,2) DEFAULT NULL,
  `k2` decimal(5,2) DEFAULT NULL,
  `k3` decimal(5,2) DEFAULT NULL,
  `k4` decimal(5,2) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kriteria`
--

CREATE TABLE `kriteria` (
  `id` int NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data untuk tabel `kriteria`
--

INSERT INTO `kriteria` (`id`, `kode`, `nama`, `jenis`, `bobot`, `created_at`) VALUES
(1, 'K1', 'Berat Badan menurut Umur (BB/U)', 'Benefit', 0.25, '2025-10-08 13:48:06'),
(2, 'K2', 'Tinggi Badan menurut Umur (TB/U)', 'Benefit', 0.25, '2025-10-08 13:48:25'),
(3, 'K3', 'Berat Badan menurut Tinggi Badan (BB/TB)', 'Benefit', 0.25, '2025-10-08 13:48:48'),
(4, 'K4', 'Indeks Massa Tubuh Menurut Umur (IMT/U)', 'Benefit', 0.25, '2025-10-08 13:49:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sub_kriteria`
--

CREATE TABLE `sub_kriteria` (
  `id` int NOT NULL,
  `id_kriteria` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `batas_bawah` decimal(5,2) DEFAULT NULL,
  `batas_atas` decimal(5,2) DEFAULT NULL,
  `nilai_skala` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `sub_kriteria`
--

INSERT INTO `sub_kriteria` (`id`, `id_kriteria`, `nama`, `batas_bawah`, `batas_atas`, `nilai_skala`) VALUES
(18, 4, 'Sangat Kurus', NULL, -3.00, 1),
(19, 4, 'Kurus', -3.00, -2.00, 2),
(20, 4, 'Normal', -2.00, 2.00, 3),
(21, 4, 'Gemuk', 2.00, NULL, 4),
(22, 3, 'Gizi Buruk', NULL, -3.00, 1),
(23, 3, 'Gizi Kurang', -3.00, -2.00, 2),
(24, 3, 'Gizi Baik', -2.00, 2.00, 3),
(25, 2, 'Sangat Pendek', NULL, -3.00, 1),
(26, 2, 'Pendek', -3.00, -2.00, 2),
(27, 2, 'Normal', -2.00, NULL, 3),
(28, 1, 'Sangat Kurang', NULL, -3.00, 1),
(29, 1, 'Kurang', -3.00, -2.00, 2),
(30, 1, 'Normal', -2.00, 2.00, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kepala') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `name`, `username`, `password`, `role`) VALUES
(1, 'ADMIN', 'admin', '$2y$12$pyGOuMsT7Q142tlwxSEs7O9j4NwpVSLISIMHQQlzLVwGmsNrs/sJC', 'admin'),
(3, 'Kepala', 'kepala', '$2y$12$ivPhfSw6MbWbSokj3moSFOR39Sbn3ezzAdtSbdQNXemdUYRtU/nyy', 'kepala');

-- --------------------------------------------------------

--
-- Struktur dari tabel `who_imt_ref`
--

CREATE TABLE `who_imt_ref` (
  `id` int NOT NULL,
  `sex` enum('L','P') NOT NULL,
  `umur_bln` int NOT NULL,
  `median` float NOT NULL,
  `sd` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `who_imt_ref`
--

INSERT INTO `who_imt_ref` (`id`, `sex`, `umur_bln`, `median`, `sd`) VALUES
(51, 'L', 0, 13.4, 1.4),
(52, 'L', 1, 14.9, 1.4),
(53, 'L', 2, 16.3, 1.5),
(54, 'L', 3, 16.9, 1.5),
(55, 'L', 4, 17.2, 1.5),
(56, 'L', 5, 17.3, 1.5),
(57, 'L', 6, 17.3, 1.5),
(58, 'L', 7, 17.3, 1.5),
(59, 'L', 8, 17.3, 1.4),
(60, 'L', 9, 17.2, 1.4),
(61, 'L', 10, 17, 1.5),
(62, 'L', 11, 16.9, 1.5),
(63, 'L', 12, 16.8, 1.4),
(64, 'L', 13, 16.7, 1.4),
(65, 'L', 14, 16.6, 1.4),
(66, 'L', 15, 16.4, 1.4),
(67, 'L', 16, 16.3, 1.4),
(68, 'L', 17, 16.2, 1.4),
(69, 'L', 18, 16.1, 1.4),
(70, 'L', 19, 16.1, 1.3),
(71, 'L', 20, 16, 1.3),
(72, 'L', 21, 15.9, 1.3),
(73, 'L', 22, 15.8, 1.4),
(74, 'L', 23, 15.8, 1.3),
(75, 'L', 24, 15.7, 1.3),
(76, 'P', 0, 13.3, 1.3),
(77, 'P', 1, 14.6, 1.4),
(78, 'P', 2, 15.8, 1.5),
(79, 'P', 3, 16.4, 1.5),
(80, 'P', 4, 16.7, 1.6),
(81, 'P', 5, 16.8, 1.6),
(82, 'P', 6, 16.9, 1.6),
(83, 'P', 7, 16.9, 1.6),
(84, 'P', 8, 16.8, 1.6),
(85, 'P', 9, 16.7, 1.6),
(86, 'P', 10, 16.6, 1.6),
(87, 'P', 11, 16.5, 1.5),
(88, 'P', 12, 16.4, 1.5),
(89, 'P', 13, 16.2, 1.5),
(90, 'P', 14, 16.1, 1.5),
(91, 'P', 15, 16, 1.5),
(92, 'P', 16, 15.9, 1.5),
(93, 'P', 17, 15.8, 1.5),
(94, 'P', 18, 15.7, 1.5),
(95, 'P', 19, 15.7, 1.4),
(96, 'P', 20, 15.6, 1.4),
(97, 'P', 21, 15.6, 1.4),
(98, 'P', 22, 15.5, 1.4),
(99, 'P', 23, 15.4, 1.5),
(100, 'P', 24, 15.4, 1.4),
(101, 'L', 24, 16, 1.3),
(102, 'L', 25, 16, 1.3),
(103, 'L', 26, 15.9, 1.4),
(104, 'L', 27, 15.9, 1.3),
(105, 'L', 28, 15.9, 1.3),
(106, 'L', 29, 15.8, 1.3),
(107, 'L', 30, 15.8, 1.3),
(108, 'L', 31, 15.8, 1.2),
(109, 'L', 32, 15.7, 1.3),
(110, 'L', 33, 15.7, 1.3),
(111, 'L', 34, 15.7, 1.3),
(112, 'L', 35, 15.6, 1.3),
(113, 'L', 36, 15.6, 1.3),
(114, 'L', 37, 15.6, 1.3),
(115, 'L', 38, 15.5, 1.3),
(116, 'L', 39, 15.5, 1.3),
(117, 'L', 40, 15.5, 1.3),
(118, 'L', 41, 15.5, 1.3),
(119, 'L', 42, 15.4, 1.4),
(120, 'L', 43, 15.4, 1.3),
(121, 'L', 44, 15.4, 1.3),
(122, 'L', 45, 15.4, 1.3),
(123, 'L', 46, 15.4, 1.3),
(124, 'L', 47, 15.3, 1.4),
(125, 'L', 48, 15.3, 1.4),
(126, 'L', 49, 15.3, 1.4),
(127, 'L', 50, 15.3, 1.4),
(128, 'L', 51, 15.3, 1.3),
(129, 'L', 52, 15.3, 1.3),
(130, 'L', 53, 15.3, 1.3),
(131, 'L', 54, 15.3, 1.3),
(132, 'L', 55, 15.2, 1.4),
(133, 'L', 56, 15.2, 1.4),
(134, 'L', 57, 15.2, 1.4),
(135, 'L', 58, 15.2, 1.4),
(136, 'L', 59, 15.2, 1.4),
(137, 'L', 60, 15.2, 1.4),
(138, 'P', 25, 15.7, 1.4),
(139, 'P', 26, 15.6, 1.4),
(140, 'P', 27, 15.6, 1.4),
(141, 'P', 28, 15.6, 1.4),
(142, 'P', 29, 15.6, 1.4),
(143, 'P', 30, 15.5, 1.4),
(144, 'P', 31, 15.5, 1.4),
(145, 'P', 32, 15.5, 1.4),
(146, 'P', 33, 15.5, 1.4),
(147, 'P', 34, 15.4, 1.4),
(148, 'P', 35, 15.4, 1.4),
(149, 'P', 36, 15.4, 1.4),
(150, 'P', 37, 15.4, 1.4),
(151, 'P', 38, 15.4, 1.4),
(152, 'P', 39, 15.3, 1.5),
(153, 'P', 40, 15.3, 1.5),
(154, 'P', 41, 15.3, 1.5),
(155, 'P', 42, 15.3, 1.5),
(156, 'P', 43, 15.3, 1.5),
(157, 'P', 44, 15.3, 1.5),
(158, 'P', 45, 15.3, 1.5),
(159, 'P', 46, 15.3, 1.5),
(160, 'P', 47, 15.3, 1.5),
(161, 'P', 48, 15.3, 1.5),
(162, 'P', 49, 15.3, 1.5),
(163, 'P', 50, 15.3, 1.5),
(164, 'P', 51, 15.3, 1.5),
(165, 'P', 52, 15.2, 1.6),
(166, 'P', 53, 15.3, 1.5),
(167, 'P', 54, 15.3, 1.5),
(168, 'P', 55, 15.3, 1.5),
(169, 'P', 56, 15.3, 1.5),
(170, 'P', 57, 15.3, 1.6),
(171, 'P', 58, 15.3, 1.6),
(172, 'P', 59, 15.3, 1.6),
(173, 'P', 60, 15.3, 1.6);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `derajat_preferensi`
--
ALTER TABLE `derajat_preferensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alternatif1` (`alternatif_1_id`),
  ADD KEY `fk_alternatif2` (`alternatif_2_id`);

--
-- Indeks untuk tabel `konversi_nilai`
--
ALTER TABLE `konversi_nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alternatif_id` (`alternatif_id`);

--
-- Indeks untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `who_imt_ref`
--
ALTER TABLE `who_imt_ref`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=488;

--
-- AUTO_INCREMENT untuk tabel `derajat_preferensi`
--
ALTER TABLE `derajat_preferensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39684;

--
-- AUTO_INCREMENT untuk tabel `konversi_nilai`
--
ALTER TABLE `konversi_nilai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7958;

--
-- AUTO_INCREMENT untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `who_imt_ref`
--
ALTER TABLE `who_imt_ref`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `derajat_preferensi`
--
ALTER TABLE `derajat_preferensi`
  ADD CONSTRAINT `fk_alternatif1` FOREIGN KEY (`alternatif_1_id`) REFERENCES `alternatif` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alternatif2` FOREIGN KEY (`alternatif_2_id`) REFERENCES `alternatif` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `konversi_nilai`
--
ALTER TABLE `konversi_nilai`
  ADD CONSTRAINT `konversi_nilai_ibfk_1` FOREIGN KEY (`alternatif_id`) REFERENCES `alternatif` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  ADD CONSTRAINT `sub_kriteria_ibfk_1` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
