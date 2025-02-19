<?php
include '../session_start.php'; // Cek session
include '../include/env.config.php'; // Koneksi ke database

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
} else {
    $menu = 'Arsip';
}

if (empty($_SESSION)) {
    include_once 'login.php';
    include_once 'include/login.php';
}

// Mengambil ID pengguna yang sedang login dari session
$id = $_SESSION['id'] ?? null;

// Fungsi untuk mengambil data pengguna
function get_user_data($koneksi, $id)
{
    $stmt = $koneksi->prepare("
        SELECT 
            u.nama,
            u.username,
            u.email,
            COALESCE(p.gambar, '../dist/img/avatar5.png') AS gambar
        FROM user u
        LEFT JOIN profil p ON u.id = p.user_id
        WHERE u.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Mendapatkan data pengguna
$user_data = $id ? get_user_data($koneksi, $id) : null;

// Fungsi untuk mengambil data arsip, berkas, dan syarat dari database
function get_arsip_data($koneksi)
{
    $query = "
        SELECT 
            arsip.id_arsip,
            berkas_pemohon.berkas AS nama_berkas,
            persyaratan.SIK AS nama_sik,
            berkas_pemohon.penanggung_jawab AS nama_penanggung_jawab,
            persyaratan.tanggal_terbit
        FROM arsip
        LEFT JOIN berkas_pemohon ON arsip.berkas_id = berkas_pemohon.id_berkas
        LEFT JOIN persyaratan ON arsip.syarat_id = persyaratan.id_syarat
    ";

    $result = $koneksi->query($query);

    if (!$result) {
        die("Query gagal: " . $koneksi->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Periksa koneksi database
if (!isset($koneksi)) {
    die("Koneksi database tidak tersedia. Pastikan file env.config.php sudah benar.");
}

// Ambil data arsip
$arsip_data = get_arsip_data($koneksi);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include_once '../include/head.php';
    ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <?php
        include_once '../include/navbar.php';
        include_once '../include/sidebar_opr.php';
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Data Arsip</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="br_operator.php">Beranda</a></li>
                                <li class="breadcrumb-item active"><?= $menu ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Data Arsip</h5>
                    </div>
                    <div class="card-body">
                        <!-- Search Box -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="searchBox"
                                        placeholder="Cari data..." />
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Berkas</th>
                                        <th>SIK</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Tanggal Terbit</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTableArsip">
                                    <?php if (!empty($arsip_data)): ?>
                                        <?php foreach ($arsip_data as $index => $arsip): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <?php if (!empty($arsip['nama_berkas'])): ?>
                                                        <a href="../uploads/<?= htmlspecialchars($arsip['nama_berkas']) ?>"
                                                            class="btn btn-info btn-sm" target="_blank">
                                                            Lihat Berkas
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak ada berkas</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($arsip['nama_sik'])): ?>
                                                        <a href="../sik_upload/<?= htmlspecialchars($arsip['nama_sik']) ?>"
                                                            class="btn btn-success btn-sm" target="_blank">
                                                            Lihat SIK
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak ada SIK</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($arsip['nama_penanggung_jawab']) ?></td>
                                                <td><?= htmlspecialchars($arsip['tanggal_terbit']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data arsip</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                        // Fungsi debounce untuk mengontrol frekuensi pencarian
                        function debounce(func, delay) {
                            let timeout;
                            return function(...args) {
                                clearTimeout(timeout);
                                timeout = setTimeout(() => func.apply(this, args), delay);
                            };
                        }

                        // Fungsi pencarian otomatis
                        function searchData() {
                            const query = document.getElementById("searchBox").value.toLowerCase();
                            const rows = document.querySelectorAll("#dataTableArsip tr");

                            rows.forEach((row) => {
                                const cells = Array.from(row.querySelectorAll("td"));
                                const match = cells.some((cell) => {
                                    const text = cell.textContent.trim().toLowerCase();
                                    return text.includes(query);
                                });

                                row.style.display = match ? "" : "none";
                            });
                        }

                        // Pasang event listener untuk pencarian
                        document.addEventListener("DOMContentLoaded", function() {
                            const searchBox = document.getElementById("searchBox");
                            if (searchBox) {
                                searchBox.addEventListener("input", debounce(searchData, 300)); // 300ms jeda debounce
                            }
                        });
                    </script>

                </div>
            </section>
        </div>
    </div>

    <?php
    include_once '../include/footer.php';
    ?>
    <?php
    include_once '../include/script.php';
    ?>

</body>

</html>