<?php
session_start();
include 'env.config.php'; // Pastikan env.config.php mengatur koneksi database dengan benar

// Validasi request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Amankan input pengguna
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Query cek pengguna
    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan data ke session
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = $user['level'];
            $_SESSION['id'] = $user['id'];

            // Redirect ke halaman sesuai level
            if ($user['level'] === 'admin') {
                header("Location: ../on-admin/br_admin.php");
            } else if ($user['level'] === 'operator') {
                header("Location: ../on-operator/br_operator.php");
            } else {
                header("Location: ../on-member/br_member.php");
            }
            exit();
        } else {
            // Password salah
            echo "<script>alert('Password salah!'); window.location.href = '../index.php';</script>";
        }
    } else {
        // Username tidak ditemukan
        echo "<script>alert('Username tidak ditemukan!'); window.location.href = '../index.php';</script>";
    }

    // Tutup statement
    $stmt->close();
} else {
    // Jika request bukan POST
    header("Location: ../index.php");
    exit();
}

// Tutup koneksi database
$koneksi->close();
