<?php
include '../session_start.php'; // Memastikan sesi aktif
include '../include/env.config.php'; // Koneksi ke database

// Memastikan parameter ID diterima
if (isset($_GET['id'])) {
    $id_kepala = intval($_GET['id']); // Pastikan ID adalah angka

    // Menghapus data kepala berdasarkan ID
    $stmt = $koneksi->prepare("DELETE FROM kepala WHERE id_kepala = ?");
    $stmt->bind_param("i", $id_kepala);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='dt_kepala.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: {$stmt->error}'); window.location.href='dt_kepala.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='dt_kepala.php';</script>";
}

// Menutup koneksi
$koneksi->close();
