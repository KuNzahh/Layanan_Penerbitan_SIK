<?php
session_start();
include '../include/env.config.php'; // Pastikan file koneksi database Anda benar

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
    $level = 'pemohon'; // Tetapkan level ke 'pemohon'

    // Cek apakah username atau email sudah ada di database
    $cek_sql = "SELECT * FROM user WHERE username = ? OR email = ?";
    $stmt = $koneksi->prepare($cek_sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username atau email sudah terdaftar
        echo "<script>
                alert('Username atau email sudah digunakan!');
                window.location.href = 'tbh_pemohon.php';
              </script>";
    } else {
        // Tambahkan data ke database
        $insert_sql = "INSERT INTO user (nama, username, email, password, level) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $koneksi->prepare($insert_sql);
        $insert_stmt->bind_param("sssss", $nama, $username, $email, $password, $level);

        if ($insert_stmt->execute()) {
            // Berhasil menambahkan data
            echo "<script>
                    alert('Pemohon berhasil ditambahkan!');
                    window.location.href = 'dt_pengguna.php'; // Ganti dengan halaman data pemohon
                  </script>";
        } else {
            // Gagal menambahkan data
            echo "<script>
                    alert('Terjadi kesalahan saat menambahkan pemohon.');
                    window.location.href = 'tbh_pemohon.php';
                  </script>";
        }

        $insert_stmt->close();
    }

    $stmt->close();
    $koneksi->close();
} else {
    // Redirect jika bukan metode POST
    header("Location: tbh_pemohon.php");
    exit();
}
