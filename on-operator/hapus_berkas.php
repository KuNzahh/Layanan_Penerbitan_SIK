<?php
include '../session_start.php'; // Memastikan pengguna login
include '../include/env.config.php'; // Koneksi database

if (isset($_GET['id_berkas'])) {
    $id_berkas = intval($_GET['id_berkas']); // Mengambil ID berkas dari URL

    // Query untuk menghapus berkas dari `berkas_pemohon` dan `persyaratan`
    $query_persyaratan = "DELETE FROM persyaratan WHERE berkas_id = ?";
    $stmt = $koneksi->prepare($query_persyaratan);
    $stmt->bind_param("i", $id_berkas);
    $stmt->execute();
    $stmt->close();

    $query_berkas = "DELETE FROM berkas_pemohon WHERE id_berkas = ?";
    $stmt = $koneksi->prepare($query_berkas);
    $stmt->bind_param("i", $id_berkas);
    $stmt->execute();
    $stmt->close();

    // Redirect dengan pemberitahuan
    echo "<script>
        alert('Berkas telah dihapus.');
        window.location.href = 'daftar_berkas.php';
    </script>";
    exit();
} else {
    // Redirect jika tidak ada ID berkas
    echo "<script>
        alert('ID berkas tidak ditemukan.');
        window.location.href = 'daftar_berkas.php';
    </script>";
    exit();
}
