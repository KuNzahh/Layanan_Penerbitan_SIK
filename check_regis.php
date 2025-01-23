<?php
session_start();
include 'include/env.config.php'; // Pastikan file koneksi.php sudah dikonfigurasi dengan benar

// Validasi username realtime
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && !isset($_POST['nama'])) {
    $username = trim($_POST['username']);

    // Debugging: Menampilkan pesan jika script berhasil diakses
    echo "Proses Validasi...<br>";

    // Query untuk memeriksa apakah username sudah ada
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika username sudah ada di database
    if ($result->num_rows > 0) {
        echo "<span style='color:red'>Username sudah digunakan</span>";
    } elseif (strlen($username) < 4) {
        // Jika username kurang dari 4 karakter
        echo "<span style='color:red'>Username harus minimal 4 karakter</span>";
    } else {
        echo "<span style='color:green'>Username tersedia</span>";
    }

    $stmt->close();
    exit(); // Menghentikan eksekusi agar PHP tidak melanjutkan ke bagian registrasi
}

// Proses registrasi pengguna
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nama'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Pengecekan panjang username
    if (strlen($username) < 4) {
        // Jika username kurang dari 4 karakter, tampilkan pesan dan hentikan proses registrasi
        echo "<script>alert('Username harus minimal 4 karakter.'); window.history.back();</script>";
        exit();
    }

    // Mengecek ulang apakah username sudah ada di database
    $sql_check = "SELECT * FROM user WHERE username = ?";
    $stmt_check = $koneksi->prepare($sql_check);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Jika username sudah ada, tampilkan pesan dan hentikan proses registrasi
        echo "<script>alert('Username sudah digunakan, silakan pilih username lain.'); window.history.back();</script>";
        $stmt_check->close();
        exit();
    }

    // Menggunakan hash untuk menyimpan password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Level pengguna default adalah 'pemohon'
    $level = 'pemohon';

    // Query untuk menyimpan data pengguna baru
    $sql = "INSERT INTO user (nama, username, email, password, level) VALUES ('$nama', '$username', '$email', '$hashed_password', '$level')";

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Akun baru berhasil dibuat.'); window.location='index.php';</script>";
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }

    $stmt_check->close();
    mysqli_close($koneksi); // Menutup koneksi hanya jika koneksi tersedia
}
