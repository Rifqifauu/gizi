-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Waktu pembuatan: 09 Okt 2025 pada 02.01
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
  `status_gizi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `alternatif`
--

INSERT INTO `alternatif` (`id`, `nama`, `sex`, `tgl_timbang`, `tgl_lahir`, `umur`, `bb`, `tb`, `z_score_bb_u`, `z_score_tb_u`, `z_score_bb_tb`, `status_bb_u`, `status_tb_u`, `status_bb_tb`, `imt`, `status_gizi`) VALUES
(148, 'M. Mitakhul Wafri', 'L', '2025-02-16', '2020-02-22', 59, 17.60, 108.00, -0.292, -0.404, -0.151, 'BB Normal', 'Normal', 'Gz.Baik', 15.089, NULL),
(149, 'Aira Azkadina Fawzia', 'P', '2025-02-16', '2020-04-13', 58, 17.40, 108.50, -0.202, -0.012, -0.396, 'BB Normal', 'Normal', 'Gz.Baik', 14.781, NULL),
(150, 'M. Hafizan', 'L', '2025-02-16', '2020-04-01', 58, 17.00, 108.00, -0.466, -0.255, -0.568, 'BB Normal', 'Normal', 'Gz.Baik', 14.575, NULL),
(151, 'Al Khalid Rayyan', 'L', '2025-02-16', '2020-05-12', 57, 17.50, 107.20, -0.159, -0.271, -0.038, 'BB Normal', 'Normal', 'Gz.Baik', 15.228, NULL),
(152, 'M. Haikal Luis', 'L', '2025-02-16', '2020-06-06', 56, 16.20, 107.50, -0.684, -0.106, -1.037, 'BB Normal', 'Normal', 'Gz.Baik', 14.018, NULL),
(153, 'Ataya Afizah Amalina', 'P', '2025-02-16', '2020-08-31', 53, 16.00, 106.00, -0.476, 0.017, -0.766, 'BB Normal', 'Normal', 'Gz.Baik', 14.239, NULL),
(154, 'Ataya Afizah Amalia', 'P', '2025-02-16', '2020-08-31', 53, 16.00, 106.00, -0.476, 0.061, -0.812, 'BB Normal', 'Normal', 'Gz.Baik', 14.186, NULL),
(155, 'M. Akhtar Fahzan', 'L', '2025-02-16', '2020-08-26', 53, 16.10, 106.10, -0.547, 0.510, -1.439, 'BB Normal', 'Normal', 'Gz.Baik', 13.551, NULL),
(156, 'Syauqi Fahim', 'L', '2025-02-16', '2020-06-17', 49, 17.70, 106.40, -0.481, 0.493, -0.281, 'BB Normal', 'Normal', 'Gz.Baik', 15.634, NULL),
(157, 'Aurin rizka aninda', 'P', '2025-02-16', '2021-10-12', 48, 17.50, 107.00, 0.455, 0.684, 0.616, 'BB Normal', 'Normal', 'Gz.Baik', 15.629, NULL),
(158, 'Finnur Abhiram Janu Rakha', 'L', '2025-02-16', '2021-02-10', 47, 15.60, 106.00, -0.437, 0.515, -1.161, 'BB Normal', 'Normal', 'Gz.Baik', 13.839, NULL),
(159, 'Siti Aisyah Syaherli', 'P', '2025-02-16', '2021-02-20', 47, 16.40, 104.30, -0.021, 0.383, -0.381, 'BB Normal', 'Normal', 'Gz.Baik', 14.708, NULL),
(160, 'Nadhira Zifana Almahrya', 'P', '2025-02-16', '2021-08-26', 48, 15.50, 106.50, -0.411, 0.683, -0.105, 'BB Normal', 'Normal', 'Gz.Baik', 14.167, NULL),
(161, 'Widad Bariroh Az Zahra', 'P', '2025-02-16', '2021-08-26', 41, 15.60, 106.50, -0.348, 1.883, -1.166, 'BB Normal', 'Normal', 'Gz.Baik', 13.759, NULL),
(162, 'Aleena Aprillia Ramadhani', 'P', '2025-02-16', '2021-09-06', 41, 14.60, 101.00, -0.135, 0.586, -0.698, 'BB Normal', 'Normal', 'Gz.Baik', 14.312, NULL);

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
(3, 'K1', 'Berat Badan menurut Umur (BB/U)', 'Benefit', 0.25, '2025-10-08 13:48:06'),
(4, 'K2', 'Tinggi Badan menurut Umur (TB/U)', 'Benefit', 0.25, '2025-10-08 13:48:25'),
(5, 'K3', 'Berat Badan menurut Tinggi Badan (BB/TB)', 'Benefit', 0.25, '2025-10-08 13:48:48'),
(6, 'K4', 'Indeks Massa Tubuh menurut Umur (IMT/U)', 'Benefit', 0.25, '2025-10-08 13:49:04');

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
(1, 6, 'Sangat Kurus', NULL, -3.00, 1),
(3, 6, 'Kurus', -3.00, -2.00, 2);

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
(1, 'RIFQI NUR FAUZI', 'rifqifauu', '$2y$12$pyGOuMsT7Q142tlwxSEs7O9j4NwpVSLISIMHQQlzLVwGmsNrs/sJC', 'admin'),
(3, 'ADMIN', 'admin', '$2y$12$OxO0HR7cYkJMSw0x9P1ak.PQm3Zg/erdF1w4Z8pqMdGPk0S1hAvoW', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  ADD CONSTRAINT `sub_kriteria_ibfk_1` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
