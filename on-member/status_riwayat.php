<?php
include '../session_start.php'; // Cek session dan pastikan pengguna login
include '../include/env.config.php'; // Koneksi ke database

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
} else {
    $menu = 'Beranda';
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

// Query untuk mengambil status terbaru
$status_query = "
    SELECT 
        b.id_berkas,
        b.nama_instansi,
        p.status_berkas,
        p.tanggal_terbit,
        p.SIK
    FROM berkas_pemohon b
    LEFT JOIN persyaratan p ON b.id_berkas = p.berkas_id
    WHERE b.user_id = ?
    ORDER BY b.id_berkas DESC
    LIMIT 1
";

$status_stmt = $koneksi->prepare($status_query);
$status_stmt->bind_param("i", $id);
$status_stmt->execute();
$status_result = $status_stmt->get_result();
$status_data = $status_result->num_rows > 0 ? $status_result->fetch_assoc() : null;
$status_stmt->close();

// Query untuk mendapatkan riwayat berkas
$riwayat_query = "
    SELECT 
        b.id_berkas,
        b.nama_instansi,
        p.tanggal_terbit,
        b.tgl_kegiatan,
        b.tempat,
        p.SIK
    FROM berkas_pemohon b
    LEFT JOIN persyaratan p ON b.id_berkas = p.berkas_id
    WHERE b.user_id = ?
    ORDER BY b.id_berkas DESC
";
$riwayat_stmt = $koneksi->prepare($riwayat_query);
$riwayat_stmt->bind_param("i", $id);
$riwayat_stmt->execute();
$riwayat_result = $riwayat_stmt->get_result();
$riwayat_stmt->close();

// Proses unggah ulang berkas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['berkas']) && isset($_POST['id_berkas'])) {
    $id_berkas = intval($_POST['id_berkas']);
    $berkas = $_FILES['berkas'];

    if ($berkas['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $berkas['tmp_name'];
        $fileName = uniqid() . '_' . basename($berkas['name']);
        $uploadDir = '../berkas/';
        $uploadFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
            // Perbarui database
            $stmt = $koneksi->prepare("
                UPDATE berkas_pemohon
                SET berkas = ?
                WHERE id_berkas = ?
            ");
            $stmt->bind_param("si", $uploadFilePath, $id_berkas);

            if ($stmt->execute()) {
                echo "<script>alert('Berkas berhasil diunggah ulang.'); window.location.href='status_riwayat.php';</script>";
            } else {
                echo "<script>alert('Gagal memperbarui database.'); window.history.back();</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Gagal mengunggah file.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan saat mengunggah file.'); window.history.back();</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../include/head.php'; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once '../include/navbar.php'; ?>
        <?php include_once '../include/sidebar_mbr.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Status dan Riwayat SIK</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                                <li class="breadcrumb-item active">Status dan Riwayat</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Terbaru -->
            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-primary">Status SIK Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($status_data): ?>
                            <?php if ($status_data['status_berkas'] === 'Diterima'): ?>
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-check"></i> Berkas Diterima</h5>
                                    Silahkan Unduh Surat Izin Keramaian.
                                    <div class="mt-3">
                                        <a href="../sik_upload/<?= htmlspecialchars(basename($status_data['SIK'])) ?>" class="btn btn-success" target="_blank">
                                            <i class="fas fa-download"></i> Unduh SIK Cetak
                                        </a>

                                    </div>
                                </div>
                            <?php elseif ($status_data['status_berkas'] === 'Ditolak'): ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Berkas Ditolak</h5>
                                    Silahkan revisi berkas Anda dengan mengunggah ulang persyaratan.
                                    <div class="mt-3">
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id_berkas" value="<?= htmlspecialchars($status_data['id_berkas']) ?>">
                                            <div class="form-group">
                                                <label for="berkas">Unggah Berkas Baru</label>
                                                <input type="file" class="form-control-file" id="berkas" name="berkas" accept=".pdf" required>
                                            </div>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-edit"></i> Revisi Berkas Persyaratan
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            <?php else: ?>
                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> Proses Penelitian</h5>
                                    Berkas Anda sedang dalam proses penelitian.
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-secondary alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-info-circle"></i> Belum Ada Status</h5>
                                Belum ada data yang terdaftar.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Riwayat Pembuatan SIK -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-primary">Riwayat Pembuatan SIK</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Instansi</th>
                                    <th>Tanggal Terbit</th>
                                    <th>Waktu Kegiatan</th>
                                    <th>Tempat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($riwayat = $riwayat_result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($riwayat['nama_instansi']) ?></td>
                                        <td><?= htmlspecialchars($riwayat['tanggal_terbit'] ?: '-') ?></td>
                                        <td><?= htmlspecialchars($riwayat['tgl_kegiatan']) ?></td>
                                        <td><?= htmlspecialchars($riwayat['tempat']) ?></td>
                                        <td>
                                            <?php if (!empty($riwayat['SIK'])): ?>
                                                <a href="../sik_upload/<?= htmlspecialchars(basename($riwayat['SIK'])) ?>" class="btn btn-success btn-sm" target="_blank">
                                                    <i class="fas fa-download"></i> Unduh SIK
                                                </a>

                                            <?php else: ?>
                                                <span class="text-muted">Belum Tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include_once '../include/footer.php'; ?>
    <?php include_once '../include/script.php'; ?>
</body>

</html>