<?php
include '../session_start.php'; // Cek session dan pastikan pengguna login
include '../include/env.config.php';    // Koneksi ke database

// Periksa apakah `id` telah diterima untuk dihapus
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Memastikan ID adalah integer

    // Query untuk menghapus data
    $query = "DELETE FROM user WHERE id = $id";

    // Eksekusi query
    if ($koneksi->query($query) === TRUE) {
        echo "<script>alert('Data pengguna berhasil dihapus.'); window.location.href='dt_pengguna_adm.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $koneksi->error . "'); window.location.href='dt_pengguna_adm.php';</script>";
    }
} else {
    echo "<script>alert('ID ulasan tidak ditemukan atau tidak valid.'); window.location.href='dt_pengguna_adm.php';</script>";
}

$koneksi->close();
