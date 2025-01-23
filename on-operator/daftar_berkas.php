<?php
include '../session_start.php'; // Cek session pengguna
include '../include/env.config.php'; // Koneksi database

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
} else {
    $menu = 'Daftar Berkas';
}

if (empty($_SESSION)) {
    include_once 'login.php';
    include_once 'include/login.php';
}

// Mengambil ID pengguna yang sedang login dari session
$id = $_SESSION['id'];

// Fungsi untuk mengambil data pengguna
function get_user_data($koneksi, $id)
{
    $stmt = $koneksi->prepare("
        SELECT 
            u.nama,
            u.username,
            u.email,
            p.gambar
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
$user_data = get_user_data($koneksi, $id);

// Pagination settings
$limit = 10; // Limit data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
$offset = ($page - 1) * $limit; // Offset untuk query SQL

// Query untuk mengambil total rows
$total_sql = "SELECT COUNT(*) as total FROM berkas_pemohon";
$total_result = $koneksi->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit); // Menghitung total halaman

// Query untuk mengambil data dengan pagination
$sql = "
    SELECT 
        b.id_berkas,
        b.nama_instansi,
        b.penanggung_jawab,
        b.tgl_kegiatan,
        u.username, -- Menambahkan username
        IFNULL(p.status_berkas, 'Belum Diproses') AS status_berkas,
        IFNULL(p.tanggal_terbit, '-') AS tanggal_terbit
    FROM berkas_pemohon b
    LEFT JOIN persyaratan p ON b.id_berkas = p.berkas_id
    LEFT JOIN user u ON b.user_id = u.id -- Bergabung dengan tabel user untuk mendapatkan username
    ORDER BY b.id_berkas DESC
    LIMIT $limit OFFSET $offset
";
$result = $koneksi->query($sql);

// Fungsi untuk sinkronisasi persyaratan
function sync_persyaratan($koneksi)
{
    $query = "
        INSERT INTO persyaratan (berkas_id, status_berkas)
        SELECT b.id_berkas, 'Menunggu Verifikasi'
        FROM berkas_pemohon b
        LEFT JOIN persyaratan p ON b.id_berkas = p.berkas_id
        WHERE p.id_syarat IS NULL
    ";
    $koneksi->query($query);
}

// Sinkronisasi data persyaratan
sync_persyaratan($koneksi);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../include/head.php'; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <?php include_once '../include/navbar.php'; ?>
        <?php include_once '../include/sidebar_opr.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Daftar Berkas</h1>
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

            <!-- Content Section -->
            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-center">
                            <strong>Proses Penerbitan Berkas Pemohon SIK Terbaru</strong>
                        </h5>
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
                            <table class="table table-bordered table-striped" id="data-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Instansi</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Username</th>
                                        <th>Waktu</th>
                                        <th>Proses</th>
                                        <th>Status</th>
                                        <th>Tanggal Terbit</th>
                                        <th>Cetak & Upload</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTable">
                                    <?php
                                    $no = 1;
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$no}</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_instansi']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['penanggung_jawab']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['tgl_kegiatan']) . "</td>";
                                            echo '<td><a href="cek_detail.php?id_berkas=' . $row['id_berkas'] . '" class="btn btn-info btn-sm">Cek Detail</a></td>';
                                            echo "<td>" . htmlspecialchars($row['status_berkas']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['tanggal_terbit']) . "</td>";
                                            echo '<td>
                                        <a href="cetak_sik.php?id_berkas=' . $row['id_berkas'] . '" class="btn btn-warning btn-sm">Cetak</a>
                                        <form method="post" action="upload_file.php?id_berkas=' . $row['id_berkas'] . '" enctype="multipart/form-data" style="display:inline-block;" id="form-upload-' . $row['id_berkas'] . '">
                                            <input type="file" name="sik_file" id="upload-file-' . $row['id_berkas'] . '" style="display:none;" onchange="document.getElementById(\'form-upload-' . $row['id_berkas'] . '\').submit();">
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById(\'upload-file-' . $row['id_berkas'] . '\').click();">Upload</button>
                                        </form>
                                        <a href="hapus_berkas.php?id_berkas=' . $row['id_berkas'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin ingin menghapus berkas ini?\')">Hapus</a>
                                    </td>';
                                            echo "</tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center">Tidak ada data ditemukan.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                                <tfoot class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Instansi</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Username</th>
                                        <th>Waktu Kegiatan</th>
                                        <th>Proses</th>
                                        <th>Status</th>
                                        <th>Tanggal Terbit</th>
                                        <th>Cetak & Upload</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                        Menampilkan <?= $offset + 1 ?> hingga <?= min($offset + $limit, $total_rows) ?> dari <?= $total_rows ?> entri
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        <ul class="pagination">
                                            <!-- Previous Button -->
                                            <?php if ($page > 1): ?>
                                                <li class="paginate_button page-item previous">
                                                    <a href="?page=<?= $page - 1 ?>" class="page-link" aria-controls="example2" tabindex="0">Sebelumnya</a>
                                                </li>
                                            <?php else: ?>
                                                <li class="paginate_button page-item previous disabled">
                                                    <a href="#" class="page-link" aria-controls="example2" tabindex="0">Sebelumnya</a>
                                                </li>
                                            <?php endif; ?>

                                            <!-- Page Numbers -->
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="paginate_button page-item <?= $i == $page ? 'active' : '' ?>">
                                                    <a href="?page=<?= $i ?>" class="page-link" aria-controls="example2" tabindex="0"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <!-- Next Button -->
                                            <?php if ($page < $total_pages): ?>
                                                <li class="paginate_button page-item next">
                                                    <a href="?page=<?= $page + 1 ?>" class="page-link" aria-controls="example2" tabindex="0">Selanjutnya</a>
                                                </li>
                                            <?php else: ?>
                                                <li class="paginate_button page-item next disabled">
                                                    <a href="#" class="page-link" aria-controls="example2" tabindex="0">Selanjutnya</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <script>
                        // Fungsi debounce untuk mengontrol pencarian
                        function debounce(func, delay) {
                            let timeout;
                            return function(...args) {
                                clearTimeout(timeout);
                                timeout = setTimeout(() => func.apply(this, args), delay);
                            };
                        }

                        // Fungsi pencarian
                        function searchData() {
                            const query = document.getElementById("searchBox").value.trim().toLowerCase();
                            const rows = document.querySelectorAll("#dataTable tr");

                            rows.forEach((row) => {
                                const cells = Array.from(row.children);
                                const match = cells.some((cell) => cell.textContent.toLowerCase().includes(query));
                                row.style.display = match ? "" : "none";
                            });
                        }

                        // Pasang event listener
                        document.addEventListener("DOMContentLoaded", function() {
                            const searchBox = document.getElementById("searchBox");
                            if (searchBox) {
                                searchBox.addEventListener("input", debounce(searchData, 300));
                            }
                        });
                    </script>
                </div>
            </section>
        </div>
    </div>
    <?php include_once '../include/footer.php'; ?>
    <?php include_once '../include/script.php'; ?>
</body>


</html>