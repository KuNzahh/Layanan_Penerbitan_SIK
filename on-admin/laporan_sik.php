<?php
include '../session_start.php'; // Karena session_start.php ada di root folder
include '../include/env.config.php'; // Karena env.config.php ada di folder include

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
} else {
    $menu = 'Laporan SIK';
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

// Fungsi untuk mendapatkan semua kecamatan
function get_all_kecamatan($koneksi)
{
    $query = "SELECT id_kecamatan, nm_kecamatan FROM kecamatan";
    $result = $koneksi->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi untuk mendapatkan semua kegiatan
function get_all_kegiatan($koneksi)
{
    $query = "SELECT id_kegiatan, nm_kegiatan FROM kegiatan";
    $result = $koneksi->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi untuk mendapatkan jumlah SIK berdasarkan kecamatan dengan filter bulan
function get_sik_count_by_kecamatan($koneksi, $month = null)
{
    $query = "
        SELECT kecamatan.nm_kecamatan, COUNT(persyaratan.id_syarat) AS jumlah_sik
        FROM persyaratan
        INNER JOIN berkas_pemohon ON persyaratan.berkas_id = berkas_pemohon.id_berkas
        INNER JOIN kecamatan ON berkas_pemohon.kecamatan_id = kecamatan.id_kecamatan
    ";
    if ($month) {
        $query .= " WHERE DATE_FORMAT(persyaratan.tanggal_terbit, '%Y-%m') = '$month'";
    }
    $query .= " GROUP BY kecamatan.nm_kecamatan";
    $result = $koneksi->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi untuk mendapatkan jumlah SIK berdasarkan kegiatan dengan filter bulan
function get_sik_count_by_kegiatan($koneksi, $month = null)
{
    $query = "
        SELECT kegiatan.nm_kegiatan, COUNT(persyaratan.id_syarat) AS jumlah_sik
        FROM persyaratan
        INNER JOIN berkas_pemohon ON persyaratan.berkas_id = berkas_pemohon.id_berkas
        INNER JOIN kegiatan ON berkas_pemohon.kegiatan_id = kegiatan.id_kegiatan
    ";
    if ($month) {
        $query .= " WHERE DATE_FORMAT(persyaratan.tanggal_terbit, '%Y-%m') = '$month'";
    }
    $query .= " GROUP BY kegiatan.nm_kegiatan";
    $result = $koneksi->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Data kecamatan dan kegiatan
$allKecamatan = get_all_kecamatan($koneksi);
$allKegiatan = get_all_kegiatan($koneksi);

// Default data (tanpa filter bulan)
$filterMonth = $_GET['filterMonth'] ?? null;
$sikByKecamatan = get_sik_count_by_kecamatan($koneksi, $filterMonth);
$sikByKegiatan = get_sik_count_by_kegiatan($koneksi, $filterMonth);

// Menutup koneksi database
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../include/head.php'; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <?php include_once '../include/navbar.php'; ?>
        <?php include_once '../include/sidebar_adm.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Laporan SIK</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="br_admin.php">Beranda</a></li>
                                <li class="breadcrumb-item active"><?= $menu ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Filter Section -->
            <section class="content">
                <!-- Info Boxes -->
                <div class="row">
                    <!-- Laporan Kecamatan -->
                    <div class="col-md-6 col-sm-12">
                        <div class="info-box" onclick="toggleSection('kecamatanReport')">
                            <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Laporan SIK - Kecamatan</span>
                                <span class="info-box-number">Klik untuk detail</span>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Kegiatan -->
                    <div class="col-md-6 col-sm-12">
                        <div class="info-box" onclick="toggleSection('kegiatanReport')">
                            <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Laporan SIK - Kegiatan</span>
                                <span class="info-box-number">Klik untuk detail</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Laporan SIK Berdasarkan Kecamatan -->
                <div id="kecamatanReport" class="card d-none">
                    <div class="card-header">
                        <h3 class="card-title">Laporan SIK Terbit Berdasarkan Kecamatan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="filterMonth">Pilih Bulan</label>
                                <input type="month" class="form-control" id="filterMonth">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="kecamatanTable" class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kecamatan</th>
                                        <th>Jumlah SIK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    foreach ($allKecamatan as $index => $kecamatan):
                                    ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($kecamatan['nm_kecamatan']) ?></td>
                                            <td>
                                                <?php
                                                $jumlah = array_column($sikByKecamatan, 'jumlah_sik', 'nm_kecamatan')[$kecamatan['nm_kecamatan']] ?? 'Kosong';
                                                echo $jumlah;
                                                $total += is_numeric($jumlah) ? $jumlah : 0;
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>Total</strong></td>
                                        <td><strong><?= $total ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="cetak_lapkecamatan.php" target="_blank" class="btn btn-success">
                            <i class="fas fa-print"></i> Cetak Laporan
                        </a>
                    </div>
                </div>

                <!-- Detail Laporan SIK Berdasarkan Kegiatan -->
                <div id="kegiatanReport" class="card d-none">
                    <div class="card-header">
                        <h3 class="card-title">Laporan SIK Terbit Berdasarkan Kegiatan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="filterMonth">Pilih Bulan</label>
                                <input type="month" class="form-control" id="filterMonth">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="kegiatanTable" class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kegiatan</th>
                                        <th>Jumlah SIK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    foreach ($allKegiatan as $index => $kegiatan):
                                    ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($kegiatan['nm_kegiatan']) ?></td>
                                            <td>
                                                <?php
                                                $jumlah = array_column($sikByKegiatan, 'jumlah_sik', 'nm_kegiatan')[$kegiatan['nm_kegiatan']] ?? 'Kosong';
                                                echo $jumlah;
                                                $total += is_numeric($jumlah) ? $jumlah : 0;
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>Total</strong></td>
                                        <td><strong><?= $total ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="cetak_lapkegiatan.php" target="_blank" class="btn btn-success">
                            <i class="fas fa-print"></i> Cetak Laporan
                        </a>
                    </div>
                </div>
            </section>

            <script>
                // Fungsi untuk toggle visibility laporan
                function toggleSection(sectionId) {
                    document.querySelectorAll('.card').forEach(card => {
                        if (card.id === sectionId) {
                            card.classList.toggle('d-none');
                        } else {
                            card.classList.add('d-none');
                        }
                    });
                }

                // Filter bulan otomatis memengaruhi tabel
                document.getElementById('filterMonth').addEventListener('change', function() {
                    const filterMonth = this.value;
                    fetch(`?filterMonth=${filterMonth}`)
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            // Update tabel kecamatan dan kegiatan
                            document.getElementById('kecamatanTable').innerHTML = doc.getElementById('kecamatanTable').innerHTML;
                            document.getElementById('kegiatanTable').innerHTML = doc.getElementById('kegiatanTable').innerHTML;
                        });
                });
            </script>
        </div>
    </div>

    <?php include_once '../include/footer.php'; ?>
    <?php include_once '../include/script.php'; ?>
</body>

</html>