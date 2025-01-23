<?php
session_start();
// Menyertakan file koneksi
include 'include/env.config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['password'];

    // Cek apakah username atau email tersedia di database dan level user sesuai
    $sql = "SELECT * FROM user WHERE (username = ? OR email = ?) AND level = 'pemohon'";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Pengguna ditemukan dan levelnya sesuai
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash kata sandi baru

        // Update kata sandi pengguna
        $update_sql = "UPDATE user SET password = ? WHERE (username = ? OR email = ?) AND level = 'pemohon'";
        $update_stmt = $koneksi->prepare($update_sql);
        $update_stmt->bind_param("sss", $hashed_password, $username, $email);

        if ($update_stmt->execute()) {
            echo "<script>
                    alert('Kata sandi berhasil diubah.');
                    window.location.href = 'index.php'; // Redirect ke halaman login
                  </script>";
        } else {
            echo "<script>
                    alert('Terjadi kesalahan saat mengubah kata sandi.');
                    window.location.href = 'forget.php';
                  </script>";
        }

        $update_stmt->close();
    } else {
        // Pengguna tidak ditemukan atau level tidak sesuai
        echo "<script>
                alert('Username atau email tidak ditemukan.');
                window.location.href = 'forget.php';
              </script>";
    }

    $stmt->close();
}

$koneksi->close();
?>
