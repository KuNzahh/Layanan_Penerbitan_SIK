-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jan 2025 pada 10.11
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `izinkeramaian`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip`
--

CREATE TABLE `arsip` (
  `id_arsip` int(11) NOT NULL,
  `berkas_id` int(11) NOT NULL,
  `syarat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `arsip`
--

INSERT INTO `arsip` (`id_arsip`, `berkas_id`, `syarat_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `berkas_pemohon`
--

CREATE TABLE `berkas_pemohon` (
  `id_berkas` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_instansi` varchar(225) NOT NULL,
  `penanggung_jawab` varchar(225) NOT NULL,
  `pekerjaan` varchar(20) NOT NULL,
  `alamat` varchar(225) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `kegiatan_id` int(11) DEFAULT NULL,
  `waktu` varchar(225) DEFAULT NULL,
  `tgl_kegiatan` varchar(250) NOT NULL,
  `tempat` varchar(250) NOT NULL,
  `kecamatan_id` int(11) DEFAULT NULL,
  `rangka` varchar(20) NOT NULL,
  `peserta` varchar(20) NOT NULL,
  `berkas` varchar(225) NOT NULL,
  `dasar` varchar(255) NOT NULL,
  `tanggal_surat` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `berkas_pemohon`
--

INSERT INTO `berkas_pemohon` (`id_berkas`, `user_id`, `nama_instansi`, `penanggung_jawab`, `pekerjaan`, `alamat`, `no_hp`, `kegiatan_id`, `waktu`, `tgl_kegiatan`, `tempat`, `kecamatan_id`, `rangka`, `peserta`, `berkas`, `dasar`, `tanggal_surat`) VALUES
(1, 27, 'Dinas Perhubungan', 'Abdul Hadi', 'Pegawai Negeri Sipil', 'Jl.Keramat RT.011 RW.004 Kel.Marabahan Kec.Marabahan Kota', '081649334151', 2, NULL, 'Senin, 2 Januari 2025 Jam 12.20', 'Gedung Olahraga (GOR) Setara Marabahan', 1, 'HUT Dinas', '200', '../Berkas/Ramadhan-BJM-TI-SI-REG-GENAP-20232024.pdf', 'Pasal 20 ', '2025-01-16'),
(2, 3, 'Dinas Parawisata', 'Hilda Nurfadillah', 'Pegawai Negeri Sipil', 'Jl.Keramat RT.011 RW.004 Kel.Marabahan Kec.Marabahan Kota', '081649334151', 2, NULL, 'Sabtu 25 Mei 2025 jam 15.00', 'Lapangan 5 Desembar', 2, 'HUT Dinas', '200', '../Berkas/Ramadhan-BJM-TI-SI-REG-GENAP-20232024.pdf', 'Pasal 20 ', '2025-02-02'),
(3, 29, 'Dinas apa', 'hadiabdul', 'P3k', 'Jl.Keramat RT.011 RW.004 Kel.Marabahan Kec.Marabahan Kota', '081649334151', 2, NULL, 'Senin,22 Januari Jam 16.20', 'Depan Masjid Agung Agung Al anwar', 3, 'HUT Dinas', '111', '../Berkas/2107010145.pdf', 'Tes', '2025-01-16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kecamatan`
--

CREATE TABLE `kecamatan` (
  `id_kecamatan` int(3) NOT NULL,
  `nm_kecamatan` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kecamatan`
--

INSERT INTO `kecamatan` (`id_kecamatan`, `nm_kecamatan`) VALUES
(1, 'Alalak'),
(2, 'Anjir Muara'),
(3, 'Anjir Pasar'),
(4, 'Bakumpai'),
(5, 'Belawang'),
(6, 'Cerbon'),
(7, 'Kuripan'),
(8, 'Jejangkit'),
(9, 'Mandastana'),
(10, 'Marabahan'),
(11, 'Mekarsari'),
(12, 'Rantau Badauh'),
(13, 'Tabukan'),
(14, 'Tabunganen'),
(15, 'Tamban'),
(17, 'Wanaraya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id_kegiatan` int(3) NOT NULL,
  `nm_kegiatan` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kegiatan`
--

INSERT INTO `kegiatan` (`id_kegiatan`, `nm_kegiatan`) VALUES
(1, 'Kegiatan Terbuka'),
(2, 'Kegiatan Tertutup'),
(3, 'Kegiatan dengan Kembang Api'),
(5, 'Kegiatan penyampaian pendapat dimuka umum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kepala`
--

CREATE TABLE `kepala` (
  `id_kepala` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `pangkat` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kepala`
--

INSERT INTO `kepala` (`id_kepala`, `nama`, `pangkat`) VALUES
(1, 'IMAN JUANDA, S.H.', 'INSPEKTUR POLISI SATU NRP 78080175');

-- --------------------------------------------------------

--
-- Struktur dari tabel `persyaratan`
--

CREATE TABLE `persyaratan` (
  `id_syarat` int(5) NOT NULL,
  `berkas_id` int(5) NOT NULL,
  `status_berkas` varchar(20) NOT NULL,
  `tanggal_terbit` date NOT NULL,
  `SIK` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `persyaratan`
--

INSERT INTO `persyaratan` (`id_syarat`, `berkas_id`, `status_berkas`, `tanggal_terbit`, `SIK`) VALUES
(1, 1, 'Diterima', '2025-01-16', '1_1736992700.pdf'),
(2, 2, 'Diterima', '2025-01-16', '2_1736993146.pdf'),
(3, 3, 'Diterima', '2025-01-16', '3_1736993465.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil`
--

CREATE TABLE `profil` (
  `id_profil` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil`
--

INSERT INTO `profil` (`id_profil`, `user_id`, `gambar`) VALUES
(8, 24, '../uploads/profile_pictures/24_1735861054.jpg'),
(9, 2, '../uploads/profile_pictures/2_1735861189.jpg'),
(10, 1, '../uploads/profile_pictures/1_1735971710.jpg'),
(11, 26, '../uploads/profile_pictures/26_1735975666.jpg'),
(12, 27, '../uploads/profile_pictures/27_1736936904.jpg'),
(13, 28, '../uploads/profile_pictures/28_1736344725.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `survey`
--

CREATE TABLE `survey` (
  `id_ulasan` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_survei` date NOT NULL,
  `jenis_kelamin` varchar(20) NOT NULL,
  `pendidikan` varchar(20) NOT NULL,
  `pekerjaan` varchar(30) NOT NULL,
  `pertanyaan1` varchar(30) NOT NULL,
  `pertanyaan2` varchar(30) NOT NULL,
  `pertanyaan3` varchar(30) NOT NULL,
  `pertanyaan4` varchar(30) NOT NULL,
  `pertanyaan5` varchar(30) NOT NULL,
  `pertanyaan6` varchar(30) NOT NULL,
  `pertanyaan7` varchar(30) NOT NULL,
  `pertanyaan8` varchar(30) NOT NULL,
  `pertanyaan9` varchar(30) NOT NULL,
  `rata_rata` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `survey`
--

INSERT INTO `survey` (`id_ulasan`, `user_id`, `tanggal_survei`, `jenis_kelamin`, `pendidikan`, `pekerjaan`, `pertanyaan1`, `pertanyaan2`, `pertanyaan3`, `pertanyaan4`, `pertanyaan5`, `pertanyaan6`, `pertanyaan7`, `pertanyaan8`, `pertanyaan9`, `rata_rata`) VALUES
(13, 24, '2025-01-03', 'Perempuan', 'Magister', 'PNS', 'Puas', 'Puas', 'Puas', 'Cukup Puas', 'Puas', 'Puas', 'Cukup Puas', 'Puas', 'Puas', 3.77778),
(14, 24, '2025-01-06', 'Laki-laki', 'Sarjana', 'Swasta', 'Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 4.88889),
(15, 26, '2025-01-04', 'Perempuan', 'Magister', 'PNS', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 5),
(16, 27, '2025-01-04', 'Perempuan', 'Magister', 'PNS', 'Sangat Puas', 'Sangat Puas', 'Sangat Puas', 'Puas', 'Puas', 'Puas', 'Cukup Puas', 'Tidak Puas', 'Puas', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(225) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `email`, `password`, `level`) VALUES
(1, 'Azijah', 'azijah', 'azijah@gmail.com', '$2y$10$IaONOe4Sn2OHw/sUtFcJDeUOvmRBnlpeRPHM0TjIbZm.uqaOT4oNS', 'admin'),
(2, 'Operator', 'operator', 'operator@gmail.com', '$2y$10$d1sNa51ixDpCLAS5vvWHfe3QmAQcj6sV/udZhuIaaWyr3GEKsVYki', 'operator'),
(3, 'icha', 'icha', 'icha@gmail.com', '$2y$10$Tks8GiSz4u5tzZJ1sz9Mieg7J1h2PHazQtbihzzUkCmkVvSr57LDi', 'pemohon'),
(8, 'Nyoya', 'nyoya', 'nyoya@gmail.com', '$2y$10$PiEjinTTFLw5/.yoLo3b0ud/g3PUjvIh8747usuwEoRtvTC89LB7y', 'pemohon'),
(9, 'usman', 'usman', 'usman@gmail.com', '$2y$10$fvkcHB4QEj00iwnfp8ngruWBgeCsZbxN6bIf06R6NIO4l4kvm56lC', 'pemohon'),
(24, 'putri', 'putri', 'putri@gmail.com', '$2y$10$BNmBEw6PhLEgM1QXpve6Au5l6ELXIGw4/ZKFYNVv69ZxVd/jDEgsS', 'pemohon'),
(25, 'Amin', 'amin', 'amin@gmail.com', '$2y$10$UE1Y.8ZhbMytC/dN3lOfieWd2DAzCxGONa2tZV3J6Cee.oHHaDNre', 'pemohon'),
(26, 'Rahmi', 'rahmi', 'rahmi@gmail.com', '$2y$10$8QZVXJF06wxin2rEwXgJousGcybl7q0UDKuQyutY/vqxhwZ2EFMsq', 'pemohon'),
(27, 'Halimatus Sa\'diah', 'Ichagrnd', 'Icha123@gmail.com', '$2y$10$VTnKsE7DeyG95VJetd9TV.3e8lUelzBBrxxKkGcavQivnBFdlAjgO', 'pemohon'),
(28, 'Monalisa', 'mona', 'mona@gmail.com', '$2y$10$X0wReMFwqj5iIm3tB/N3zuZZZ1fJMfamIbudE.voU6voarnE6xBiW', 'pemohon'),
(29, 'Rezqy', 'Rezqy', 'putri@gmail.com', '$2y$10$e9C.pD9GIfPZdu6wbZjz.uPzFUKJi17OR9..7WdsrIRojSX/xpzcG', 'pemohon'),
(30, 'ziza', 'zizaa', 'ziza@gmail.com', '$2y$10$wKh6iY7HIcL94Gyv9DMJmeg48D5hIyhnt6O/r/vnax3oB7KwGYBU6', 'pemohon'),
(31, 'zizi', 'zizi', 'zizi@gmail.com', '$2y$10$//7iNV5OO67BM7U9h4bzaOJg2FnUzxOeHfQ6CPznmCLTtppomdv/m', 'pemohon');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `arsip`
--
ALTER TABLE `arsip`
  ADD PRIMARY KEY (`id_arsip`),
  ADD KEY `berkas_id` (`berkas_id`),
  ADD KEY `syarat_id` (`syarat_id`);

--
-- Indeks untuk tabel `berkas_pemohon`
--
ALTER TABLE `berkas_pemohon`
  ADD PRIMARY KEY (`id_berkas`),
  ADD KEY `fk_kecamatan` (`kecamatan_id`),
  ADD KEY `fk_kegiatan` (`kegiatan_id`);

--
-- Indeks untuk tabel `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD PRIMARY KEY (`id_kecamatan`);

--
-- Indeks untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id_kegiatan`);

--
-- Indeks untuk tabel `kepala`
--
ALTER TABLE `kepala`
  ADD PRIMARY KEY (`id_kepala`);

--
-- Indeks untuk tabel `persyaratan`
--
ALTER TABLE `persyaratan`
  ADD PRIMARY KEY (`id_syarat`),
  ADD KEY `berkas_id` (`berkas_id`);

--
-- Indeks untuk tabel `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id_profil`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`);

--
-- Indeks untuk tabel `survey`
--
ALTER TABLE `survey`
  ADD PRIMARY KEY (`id_ulasan`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `arsip`
--
ALTER TABLE `arsip`
  MODIFY `id_arsip` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `berkas_pemohon`
--
ALTER TABLE `berkas_pemohon`
  MODIFY `id_berkas` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kecamatan`
--
ALTER TABLE `kecamatan`
  MODIFY `id_kecamatan` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kepala`
--
ALTER TABLE `kepala`
  MODIFY `id_kepala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `persyaratan`
--
ALTER TABLE `persyaratan`
  MODIFY `id_syarat` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `profil`
--
ALTER TABLE `profil`
  MODIFY `id_profil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `survey`
--
ALTER TABLE `survey`
  MODIFY `id_ulasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `arsip`
--
ALTER TABLE `arsip`
  ADD CONSTRAINT `arsip_ibfk_1` FOREIGN KEY (`berkas_id`) REFERENCES `berkas_pemohon` (`id_berkas`) ON DELETE CASCADE,
  ADD CONSTRAINT `arsip_ibfk_2` FOREIGN KEY (`syarat_id`) REFERENCES `persyaratan` (`id_syarat`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `berkas_pemohon`
--
ALTER TABLE `berkas_pemohon`
  ADD CONSTRAINT `fk_kecamatan` FOREIGN KEY (`kecamatan_id`) REFERENCES `kecamatan` (`id_kecamatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id_kegiatan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `persyaratan`
--
ALTER TABLE `persyaratan`
  ADD CONSTRAINT `persyaratan_ibfk_1` FOREIGN KEY (`berkas_id`) REFERENCES `berkas_pemohon` (`id_berkas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `profil`
--
ALTER TABLE `profil`
  ADD CONSTRAINT `profil_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `survey`
--
ALTER TABLE `survey`
  ADD CONSTRAINT `survey_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
