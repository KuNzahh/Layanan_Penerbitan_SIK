<?php
include '../session_start.php'; // Cek session dan pastikan pengguna login
include '../include/env.config.php'; // Koneksi ke database

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
$user_data = $id ? get_user_data($koneksi, $id) : null;


// Mendapatkan data berkas pemohon
if (!isset($_GET['id_berkas'])) {
    header('Location: daftar_berkas.php');
    exit;
}

$id_berkas = intval($_GET['id_berkas']);
$berkas_data = [];
$stmt = $koneksi->prepare("
    SELECT 
        b.*, 
        k.nm_kegiatan AS nm_kegiatan, 
        kc.nm_kecamatan AS nm_kecamatan 
    FROM berkas_pemohon b
    LEFT JOIN kegiatan k ON b.kegiatan_id = k.id_kegiatan
    LEFT JOIN kecamatan kc ON b.kecamatan_id = kc.id_kecamatan
    WHERE b.id_berkas = ?
");
$stmt->bind_param("i", $id_berkas);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $berkas_data = $result->fetch_assoc();
} else {
    echo "<script>alert('Data tidak ditemukan.'); window.location.href='daftar_berkas.php';</script>";
    exit;
}
$stmt->close();

// Proses perubahan status
if (isset($_POST['status'])) {
    $status = $_POST['status'];
    $dasar = isset($_POST['dasar']) ? $_POST['dasar'] : null;
    $tanggal_surat = isset($_POST['tanggal_surat']) ? $_POST['tanggal_surat'] : null;

    // Validasi hanya jika status adalah "Diterima"
    if ($status === "Diterima") {
        if (empty($dasar) || empty($tanggal_surat)) {
            echo "<script>alert('Harap lengkapi Dasar dan Tanggal Surat sebelum menentukan status Diterima.'); window.history.back();</script>";
            exit;
        }
    }

    // Validasi status hanya boleh "Diterima" atau "Ditolak"
    if ($status === "Diterima" || $status === "Ditolak") {
        $stmt = $koneksi->prepare("
            UPDATE persyaratan
            SET status_berkas = ?, tanggal_terbit = ?
            WHERE berkas_id = ?
        ");
        $tanggal_terbit = ($status === "Diterima") ? date('Y-m-d') : null; // Tanggal terbit hanya diisi jika status Diterima
        $stmt->bind_param("ssi", $status, $tanggal_terbit, $id_berkas);

        // Update Dasar dan Tanggal Surat di tabel `berkas_pemohon`
        $stmt2 = $koneksi->prepare("
            UPDATE berkas_pemohon
            SET dasar = ?, tanggal_surat = ?
            WHERE id_berkas = ?
        ");
        $stmt2->bind_param("ssi", $dasar, $tanggal_surat, $id_berkas);

        if ($stmt->execute() && $stmt2->execute()) {
            echo "<script>alert('Status berhasil diperbarui menjadi {$status}.'); window.location.href='daftar_berkas.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui status.'); window.history.back();</script>";
        }
        $stmt->close();
        $stmt2->close();
    } else {
        echo "<script>alert('Status tidak valid.'); window.history.back();</script>";
    }
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../include/head.php'; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php 
        include_once '../include/navbar.php';
        include_once '../include/sidebar_opr.php';
        ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Detail Berkas Pemohon</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="daftar_berkas.php">Daftar Berkas</a></li>
                                <li class="breadcrumb-item active">Detail Berkas</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-primary">Periksa dan Lengkapi Data Berkas</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_instansi">Nama Instansi</label>
                                        <input type="text" class="form-control" id="nama_instansi" value="<?= htmlspecialchars($berkas_data['nama_instansi']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="penanggung_jawab">Penanggung Jawab</label>
                                        <input type="text" class="form-control" id="penanggung_jawab" value="<?= htmlspecialchars($berkas_data['penanggung_jawab']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="pekerjaan">Pekerjaan</label>
                                        <input type="text" class="form-control" id="pekerjaan" value="<?= htmlspecialchars($berkas_data['pekerjaan']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat">Alamat</label>
                                        <input type="text" class="form-control" id="alamat" value="<?= htmlspecialchars($berkas_data['alamat']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">No. Hp</label>
                                        <input type="text" class="form-control" id="no_hp" value="<?= htmlspecialchars($berkas_data['no_hp']) ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nm_kegiatan">Bentuk Kegiatan</label>
                                        <input type="text" class="form-control" id="nm_kegiatan" value="<?= htmlspecialchars($berkas_data['nm_kegiatan']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="tgl_kegiatan">Waktu Kegiatan</label>
                                        <input type="text" class="form-control" id="tgl_kegiatan" value="<?= htmlspecialchars($berkas_data['tgl_kegiatan']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="tempat">Tempat</label>
                                        <input type="text" class="form-control" id="tempat" value="<?= htmlspecialchars($berkas_data['tempat']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="nm_kecamatan">Kecamatan</label>
                                        <input type="text" class="form-control" id="nm_kecamatan" value="<?= htmlspecialchars($berkas_data['nm_kecamatan']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="rangka">Dalam Rangka</label>
                                        <input type="text" class="form-control" id="rangka" value="<?= htmlspecialchars($berkas_data['rangka']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="jumlah_peserta">Jumlah Peserta</label>
                                        <input type="number" class="form-control" id="jumlah_peserta" value="<?= htmlspecialchars($berkas_data['peserta']) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="berkas">Cek Berkas</label>
                                <a href="<?= htmlspecialchars($berkas_data['berkas']) ?>" target="_blank" class="btn btn-info">Lihat Berkas</a>
                            </div>
                            <div class="form-group">
                                <label for="dasar">Dasar</label>
                                <input type="text" class="form-control" id="dasar" name="dasar" placeholder="Masukkan Dasar">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_surat">Tanggal Surat</label>
                                <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat">
                            </div>
                            <div class="mt-3">
                                <button type="submit" name="status" value="Diterima" class="btn btn-success">
                                    <i class="fas fa-check"></i> Diterima
                                </button>
                                <button type="submit" name="status" value="Ditolak" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Ditolak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include_once '../include/footer.php'; ?>
    <?php include_once '../include/script.php'; ?>
</body>

</html>
