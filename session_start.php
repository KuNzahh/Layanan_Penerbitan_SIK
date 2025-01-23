<?php
// session_start.php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Mulai session hanya jika belum dimulai
}

// Cek apakah pengguna sudah login dengan memeriksa session `id`
if (!isset($_SESSION['id'])) {
    die("Session not found, redirecting...");
    header("Location: ../index.php");
    exit();
} 

