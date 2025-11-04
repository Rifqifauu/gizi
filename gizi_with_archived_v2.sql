-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Waktu pembuatan: 04 Nov 2025 pada 01.38
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
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `id_arsip` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `alternatif`
--

INSERT INTO `alternatif` (`id`, `nama`, `sex`, `tgl_timbang`, `tgl_lahir`, `umur`, `bb`, `tb`, `z_score_bb_u`, `z_score_tb_u`, `z_score_bb_tb`, `status_bb_u`, `status_tb_u`, `status_bb_tb`, `imt`, `z_score_imt`, `status_imt`, `is_archived`, `id_arsip`) VALUES
(488, 'Aisyah', 'P', '2024-04-12', '2023-06-10', 10, 8.40, 74.00, 0.310, 1.000, 0.800, 'Normal', 'Normal', 'Gizi Baik', 15.340, -0.79, 'Normal', 1, 1),
(489, 'Bima', 'L', '2025-03-11', '2025-01-20', 2, 4.70, 52.90, 0.000, -0.170, 1.560, 'Normal', 'Normal', 'Gizi Baik', 16.795, 0.33, 'Normal', 1, 1),
(490, 'Zahra', 'P', '2024-09-27', '2021-08-16', 37, 14.00, 129.00, -0.480, -0.470, -3.330, 'Normal', 'Normal', 'Gizi Buruk', 8.413, -4.99, 'Sangat Kurus', 1, 1),
(491, 'Rafi', 'L', '2024-04-16', '2019-07-25', 57, 14.00, 176.20, -2.460, 0.600, -5.650, 'Kurang', 'Normal', 'Gizi Buruk', 4.509, -7.64, 'Sangat Kurus', 1, 1),
(492, 'Siti', 'P', '2024-03-12', '2021-12-15', 27, 12.00, 107.90, -0.240, -0.170, -2.200, 'Normal', 'Normal', 'Gizi Kurang', 10.307, -3.78, 'Sangat Kurus', 1, 1),
(493, 'Adnan', 'L', '2024-01-21', '2019-11-20', 50, 23.70, 160.20, 2.580, 0.400, -2.840, 'Tidak Diketahui', 'Normal', 'Gizi Kurang', 9.235, -4.33, 'Sangat Kurus', 1, 1),
(494, 'Dewi', 'P', '2025-09-06', '2022-09-12', 36, 13.70, 133.90, -0.510, 1.900, -3.790, 'Normal', 'Normal', 'Gizi Buruk', 7.641, -5.54, 'Sangat Kurus', 1, 1),
(495, 'Farhan', 'L', '2024-05-01', '2024-02-13', 3, 4.90, 54.70, -0.630, -0.300, 1.410, 'Normal', 'Normal', 'Gizi Baik', 16.377, -0.35, 'Normal', 1, 1),
(496, 'Intan', 'P', '2025-04-11', '2023-01-13', 27, 13.80, 113.10, 0.980, 1.570, -1.910, 'Normal', 'Normal', 'Gizi Baik', 10.788, -3.44, 'Sangat Kurus', 1, 1),
(497, 'Gilang', 'L', '2024-08-02', '2020-09-22', 46, 16.60, 151.40, -0.240, 0.400, -4.020, 'Normal', 'Normal', 'Gizi Buruk', 7.242, -6.28, 'Sangat Kurus', 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip`
--

CREATE TABLE `arsip` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `archived_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `arsip`
--

INSERT INTO `arsip` (`id`, `nama`, `archived_at`) VALUES
(1, 'Arsip ', '2025-11-04');

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

--
-- Dumping data untuk tabel `derajat_preferensi`
--

INSERT INTO `derajat_preferensi` (`id`, `alternatif_1_id`, `alternatif_2_id`, `nilai`, `created_at`) VALUES
(40584, 488, 489, 0, '2025-11-04 01:34:22'),
(40585, 488, 490, 1, '2025-11-04 01:34:22'),
(40586, 488, 491, 1.25, '2025-11-04 01:34:22'),
(40587, 488, 492, 0.75, '2025-11-04 01:34:22'),
(40588, 488, 493, 1.5, '2025-11-04 01:34:22'),
(40589, 488, 494, 1, '2025-11-04 01:34:22'),
(40590, 488, 495, 0, '2025-11-04 01:34:22'),
(40591, 488, 496, 0.5, '2025-11-04 01:34:22'),
(40592, 488, 497, 1, '2025-11-04 01:34:22'),
(40593, 489, 488, 0, '2025-11-04 01:34:22'),
(40594, 489, 490, 1, '2025-11-04 01:34:22'),
(40595, 489, 491, 1.25, '2025-11-04 01:34:22'),
(40596, 489, 492, 0.75, '2025-11-04 01:34:22'),
(40597, 489, 493, 1.5, '2025-11-04 01:34:22'),
(40598, 489, 494, 1, '2025-11-04 01:34:22'),
(40599, 489, 495, 0, '2025-11-04 01:34:22'),
(40600, 489, 496, 0.5, '2025-11-04 01:34:22'),
(40601, 489, 497, 1, '2025-11-04 01:34:22'),
(40602, 490, 488, 0, '2025-11-04 01:34:22'),
(40603, 490, 489, 0, '2025-11-04 01:34:22'),
(40604, 490, 491, 0.25, '2025-11-04 01:34:22'),
(40605, 490, 492, 0, '2025-11-04 01:34:22'),
(40606, 490, 493, 0.75, '2025-11-04 01:34:22'),
(40607, 490, 494, 0, '2025-11-04 01:34:22'),
(40608, 490, 495, 0, '2025-11-04 01:34:22'),
(40609, 490, 496, 0, '2025-11-04 01:34:22'),
(40610, 490, 497, 0, '2025-11-04 01:34:22'),
(40611, 491, 488, 0, '2025-11-04 01:34:22'),
(40612, 491, 489, 0, '2025-11-04 01:34:22'),
(40613, 491, 490, 0, '2025-11-04 01:34:22'),
(40614, 491, 492, 0, '2025-11-04 01:34:22'),
(40615, 491, 493, 0.5, '2025-11-04 01:34:22'),
(40616, 491, 494, 0, '2025-11-04 01:34:22'),
(40617, 491, 495, 0, '2025-11-04 01:34:22'),
(40618, 491, 496, 0, '2025-11-04 01:34:22'),
(40619, 491, 497, 0, '2025-11-04 01:34:22'),
(40620, 492, 488, 0, '2025-11-04 01:34:22'),
(40621, 492, 489, 0, '2025-11-04 01:34:22'),
(40622, 492, 490, 0.25, '2025-11-04 01:34:22'),
(40623, 492, 491, 0.5, '2025-11-04 01:34:22'),
(40624, 492, 493, 0.75, '2025-11-04 01:34:22'),
(40625, 492, 494, 0.25, '2025-11-04 01:34:22'),
(40626, 492, 495, 0, '2025-11-04 01:34:22'),
(40627, 492, 496, 0, '2025-11-04 01:34:22'),
(40628, 492, 497, 0.25, '2025-11-04 01:34:22'),
(40629, 493, 488, 0, '2025-11-04 01:34:22'),
(40630, 493, 489, 0, '2025-11-04 01:34:22'),
(40631, 493, 490, 0.25, '2025-11-04 01:34:22'),
(40632, 493, 491, 0.25, '2025-11-04 01:34:22'),
(40633, 493, 492, 0, '2025-11-04 01:34:22'),
(40634, 493, 494, 0.25, '2025-11-04 01:34:22'),
(40635, 493, 495, 0, '2025-11-04 01:34:22'),
(40636, 493, 496, 0, '2025-11-04 01:34:22'),
(40637, 493, 497, 0.25, '2025-11-04 01:34:22'),
(40638, 494, 488, 0, '2025-11-04 01:34:22'),
(40639, 494, 489, 0, '2025-11-04 01:34:22'),
(40640, 494, 490, 0, '2025-11-04 01:34:22'),
(40641, 494, 491, 0.25, '2025-11-04 01:34:22'),
(40642, 494, 492, 0, '2025-11-04 01:34:22'),
(40643, 494, 493, 0.75, '2025-11-04 01:34:22'),
(40644, 494, 495, 0, '2025-11-04 01:34:22'),
(40645, 494, 496, 0, '2025-11-04 01:34:22'),
(40646, 494, 497, 0, '2025-11-04 01:34:22'),
(40647, 495, 488, 0, '2025-11-04 01:34:22'),
(40648, 495, 489, 0, '2025-11-04 01:34:22'),
(40649, 495, 490, 1, '2025-11-04 01:34:22'),
(40650, 495, 491, 1.25, '2025-11-04 01:34:22'),
(40651, 495, 492, 0.75, '2025-11-04 01:34:22'),
(40652, 495, 493, 1.5, '2025-11-04 01:34:22'),
(40653, 495, 494, 1, '2025-11-04 01:34:22'),
(40654, 495, 496, 0.5, '2025-11-04 01:34:22'),
(40655, 495, 497, 1, '2025-11-04 01:34:22'),
(40656, 496, 488, 0, '2025-11-04 01:34:22'),
(40657, 496, 489, 0, '2025-11-04 01:34:22'),
(40658, 496, 490, 0.5, '2025-11-04 01:34:22'),
(40659, 496, 491, 0.75, '2025-11-04 01:34:22'),
(40660, 496, 492, 0.25, '2025-11-04 01:34:22'),
(40661, 496, 493, 1, '2025-11-04 01:34:22'),
(40662, 496, 494, 0.5, '2025-11-04 01:34:22'),
(40663, 496, 495, 0, '2025-11-04 01:34:22'),
(40664, 496, 497, 0.5, '2025-11-04 01:34:22'),
(40665, 497, 488, 0, '2025-11-04 01:34:22'),
(40666, 497, 489, 0, '2025-11-04 01:34:22'),
(40667, 497, 490, 0, '2025-11-04 01:34:22'),
(40668, 497, 491, 0.25, '2025-11-04 01:34:22'),
(40669, 497, 492, 0, '2025-11-04 01:34:22'),
(40670, 497, 493, 0.75, '2025-11-04 01:34:22'),
(40671, 497, 494, 0, '2025-11-04 01:34:22'),
(40672, 497, 495, 0, '2025-11-04 01:34:22'),
(40673, 497, 496, 0, '2025-11-04 01:34:22');

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

--
-- Dumping data untuk tabel `konversi_nilai`
--

INSERT INTO `konversi_nilai` (`id`, `alternatif_id`, `k1`, `k2`, `k3`, `k4`, `updated_at`) VALUES
(7958, 488, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:13:10'),
(7959, 489, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:13:10'),
(7960, 490, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:13:10'),
(7961, 491, 3.00, 2.00, 1.00, 1.00, '2025-11-04 01:13:10'),
(7962, 492, 3.00, 3.00, 2.00, 1.00, '2025-11-04 01:13:10'),
(7963, 493, 3.00, 0.00, 2.00, 1.00, '2025-11-04 01:13:10'),
(7964, 494, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:13:10'),
(7965, 495, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:13:10'),
(7966, 496, 3.00, 3.00, 3.00, 1.00, '2025-11-04 01:13:10'),
(7967, 497, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:13:10'),
(7968, 488, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:33:05'),
(7969, 489, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:33:05'),
(7970, 490, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:33:05'),
(7971, 491, 3.00, 2.00, 1.00, 1.00, '2025-11-04 01:33:05'),
(7972, 492, 3.00, 3.00, 2.00, 1.00, '2025-11-04 01:33:05'),
(7973, 493, 3.00, 0.00, 2.00, 1.00, '2025-11-04 01:33:05'),
(7974, 494, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:33:05'),
(7975, 495, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:33:05'),
(7976, 496, 3.00, 3.00, 3.00, 1.00, '2025-11-04 01:33:05'),
(7977, 497, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:33:05'),
(7978, 488, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:33:07'),
(7979, 489, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:33:07'),
(7980, 490, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:33:07'),
(7981, 491, 3.00, 2.00, 1.00, 1.00, '2025-11-04 01:33:07'),
(7982, 492, 3.00, 3.00, 2.00, 1.00, '2025-11-04 01:33:07'),
(7983, 493, 3.00, 0.00, 2.00, 1.00, '2025-11-04 01:33:07'),
(7984, 494, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:33:07'),
(7985, 495, 3.00, 3.00, 3.00, 3.00, '2025-11-04 01:33:07'),
(7986, 496, 3.00, 3.00, 3.00, 1.00, '2025-11-04 01:33:07'),
(7987, 497, 3.00, 3.00, 1.00, 1.00, '2025-11-04 01:33:07');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_arsip` (`id_arsip`);

--
-- Indeks untuk tabel `arsip`
--
ALTER TABLE `arsip`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=498;

--
-- AUTO_INCREMENT untuk tabel `arsip`
--
ALTER TABLE `arsip`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `derajat_preferensi`
--
ALTER TABLE `derajat_preferensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40674;

--
-- AUTO_INCREMENT untuk tabel `konversi_nilai`
--
ALTER TABLE `konversi_nilai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7988;

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
-- Ketidakleluasaan untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  ADD CONSTRAINT `fk_user_arsip` FOREIGN KEY (`id_arsip`) REFERENCES `arsip` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
