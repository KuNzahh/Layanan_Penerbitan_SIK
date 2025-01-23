<?php
include '../session_start.php'; // Memastikan pengguna login
include '../include/env.config.php'; // Koneksi database

// Cek apakah ID berkas tersedia
if (!isset($_GET['id_berkas'])) {
    echo "<script>
        alert('ID berkas tidak ditemukan.');
        window.location.href = 'daftar_berkas.php';
    </script>";
    exit();
}

$id_berkas = intval($_GET['id_berkas']); // Mengambil ID berkas dari URL

// Pastikan folder upload ada
$upload_dir = '../sik_upload/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Proses upload file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sik_file'])) {
    $file_tmp = $_FILES['sik_file']['tmp_name'];
    $file_name = basename($_FILES['sik_file']['name']);
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_new_name = $id_berkas . '_' . time() . '.' . $file_ext; // Nama file baru untuk menghindari duplikasi
    $file_dest = $upload_dir . $file_new_name;

    // Validasi file (contoh: hanya PDF diizinkan)
    if ($file_ext !== 'pdf') {
        echo "<script>
            alert('Hanya file PDF yang diizinkan.');
            window.location.href = 'daftar_berkas.php';
        </script>";
        exit();
    }

    // Pindahkan file ke folder tujuan
    if (move_uploaded_file($file_tmp, $file_dest)) {
        // Simpan informasi file ke tabel `persyaratan`
        $query = "UPDATE persyaratan SET sik = ? WHERE berkas_id = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("si", $file_new_name, $id_berkas);
        $stmt->execute();
        $stmt->close();

        // Masukkan data otomatis ke tabel arsip
        $arsip_query = "
            INSERT INTO arsip (berkas_id, syarat_id)
            SELECT b.id_berkas, p.id_syarat
            FROM persyaratan p
            JOIN berkas_pemohon b ON p.berkas_id = b.id_berkas
            WHERE p.berkas_id = ?
            ON DUPLICATE KEY UPDATE
            syarat_id = VALUES(syarat_id)";
        
        $stmt_arsip = $koneksi->prepare($arsip_query);
        $stmt_arsip->bind_param("i", $id_berkas);
        $stmt_arsip->execute();
        $stmt_arsip->close();

        echo "<script>
            alert('Berkas berhasil diupload dan data masuk ke arsip.');
            window.location.href = 'daftar_berkas.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal mengupload file.');
            window.location.href = 'daftar_berkas.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Harap pilih file yang valid.');
        window.location.href = 'daftar_berkas.php';
    </script>";
}
?>
