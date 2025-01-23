<?php
include '../session_start.php';
include '../include/env.config.php';

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
} else {
    $menu = 'Laporan Arsip';
}

// Mendapatkan data pengguna
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

$id = $_SESSION['id'];
$user_data = get_user_data($koneksi, $id);

// Mendapatkan data arsip dengan filter
$filterMonth = $_GET['filter_month'] ?? null;
$filterYear = $_GET['filter_year'] ?? null;

$query = "
    SELECT 
        arsip.id_arsip,
        persyaratan.tanggal_terbit,
        berkas_pemohon.nama_instansi,
        berkas_pemohon.penanggung_jawab,
        berkas_pemohon.berkas AS berkas_pemohon,
        persyaratan.SIK AS sik,
        persyaratan.status_berkas
    FROM arsip
    LEFT JOIN persyaratan ON arsip.syarat_id = persyaratan.id_syarat
    LEFT JOIN berkas_pemohon ON persyaratan.berkas_id = berkas_pemohon.id_berkas
    WHERE 1=1
";

if ($filterMonth && $filterYear) {
    $query .= " AND DATE_FORMAT(persyaratan.tanggal_terbit, '%Y-%m') = '$filterYear-$filterMonth'";
} elseif ($filterYear) {
    $query .= " AND YEAR(persyaratan.tanggal_terbit) = '$filterYear'";
}

$query .= " ORDER BY persyaratan.tanggal_terbit DESC";
$result = $koneksi->query($query);
$arsip_data = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total SIK
$totalSIK = count($arsip_data);

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

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Laporan Arsip</h1>
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

            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Laporan Arsip</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="filter_month">Bulan:</label>
                                    <select class="form-control" name="filter_month" id="filter_month">
                                        <option value="">Semua Bulan</option>
                                        <?php
                                        $months = [
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ];
                                        foreach ($months as $key => $month) {
                                            $selected = ($filterMonth === $key) ? 'selected' : '';
                                            echo "<option value='$key' $selected>$month</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filter_year">Tahun:</label>
                                    <select class="form-control" name="filter_year" id="filter_year">
                                        <option value="">Semua Tahun</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= 2000; $year--) {
                                            $selected = ($filterYear == $year) ? 'selected' : '';
                                            echo "<option value='$year' $selected>$year</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4 text-right">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Arsip</h3>
                        <a href="cetak_laparsip.php?filter_month=<?= htmlspecialchars($filterMonth) ?>&filter_year=<?= htmlspecialchars($filterYear) ?>" class="btn btn-success float-right">Cetak Laporan</a>
                    </div>
                    <div class="card-body">
                        <p><strong>Total SIK: <?= $totalSIK ?></strong></p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Terbit</th>
                                        <th>Nama Instansi</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Berkas Pemohon</th>
                                        <th>SIK</th>
                                        <th>Status Berkas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($arsip_data)): ?>
                                        <?php foreach ($arsip_data as $index => $arsip): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($arsip['tanggal_terbit']) ?></td>
                                                <td><?= htmlspecialchars($arsip['nama_instansi']) ?></td>
                                                <td><?= htmlspecialchars($arsip['penanggung_jawab']) ?></td>
                                                <td><a href="../uploads/<?= htmlspecialchars($arsip['berkas_pemohon']) ?>" target="_blank">Lihat Berkas</a></td>
                                                <td><a href="../sik_upload/<?= htmlspecialchars($arsip['sik']) ?>" target="_blank">Lihat SIK</a></td>
                                                <td><?= htmlspecialchars($arsip['status_berkas']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data arsip</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include_once '../include/footer.php'; ?>
        <?php include_once '../include/script.php'; ?>
    </div>
</body>
</html>
