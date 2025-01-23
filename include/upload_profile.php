<?php
session_start();
include '../include/env.config.php'; // Koneksi ke database

// Pastikan pengguna sudah login
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Anda harus login terlebih dahulu.'); window.location.href = '../index.php';</script>";
    exit;
}

// Ambil ID pengguna dari sesi
$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $upload_dir = '../uploads/profile_pictures/'; // Direktori tempat menyimpan gambar
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg']; // Tipe file yang diperbolehkan

    // Periksa apakah direktori upload ada, jika tidak maka buat
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Validasi file
    if (!in_array($file['type'], $allowed_types)) {
        echo "<script>alert('Hanya file gambar (JPEG, PNG) yang diperbolehkan.'); window.history.back();</script>";
        exit;
    }

    if ($file['size'] > 2 * 1024 * 1024) { // Maksimal ukuran file 2MB
        echo "<script>alert('Ukuran file terlalu besar. Maksimal 2MB.'); window.history.back();</script>";
        exit;
    }

    // Generate nama unik untuk file
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = $id . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;

    // Pindahkan file yang diunggah ke direktori tujuan
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Periksa apakah entri profil sudah ada untuk user ini
        $stmt = $koneksi->prepare("SELECT id_profil FROM profil WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Jika sudah ada, perbarui gambar
            $stmt = $koneksi->prepare("UPDATE profil SET gambar = ? WHERE user_id = ?");
            $stmt->bind_param("si", $file_path, $id);
        } else {
            // Jika belum ada, tambahkan entri baru
            $stmt = $koneksi->prepare("INSERT INTO profil (user_id, gambar) VALUES (?, ?)");
            $stmt->bind_param("is", $id, $file_path);
        }

        if ($stmt->execute()) {
            // Periksa level pengguna untuk menentukan arah halaman
            $stmt = $koneksi->prepare("SELECT level FROM user WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user['level'] === 'pemohon') {
                echo "<script>alert('Gambar profil berhasil diperbarui.'); window.location.href = '../on-member/profil.php';</script>";
            } elseif ($user['level'] === 'admin') {
                echo "<script>alert('Gambar profil berhasil diperbarui.'); window.location.href = '../on-admin/profil.php';</script>";
            } else {
                echo "<script>alert('Gambar profil berhasil diperbarui.'); window.location.href = '../on-operator/profil.php';</script>";
            }
        } else {
            echo "<script>alert('Terjadi kesalahan saat menyimpan data ke database.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Gagal mengunggah file.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Tidak ada file yang diunggah.'); window.history.back();</script>";
}

// Tutup koneksi
$koneksi->close();
?>
