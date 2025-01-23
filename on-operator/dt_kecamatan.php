<?php
include '../session_start.php'; // Memastikan sesi aktif
include '../include/env.config.php'; // Koneksi ke database

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

// Fungsi untuk mengambil data kecamatan
function get_kecamatan_data($koneksi)
{
    $result = $koneksi->query("SELECT id_kecamatan, nm_kecamatan FROM kecamatan");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Proses Tambah atau Edit Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_kecamatan'])) {
        // Tambah Data
        $nama_kecamatan = $_POST['nm_kecamatan'];
        $stmt = $koneksi->prepare("INSERT INTO kecamatan (nm_kecamatan) VALUES (?)");
        $stmt->bind_param("s", $nama_kecamatan);

        if ($stmt->execute()) {
            echo "<script>alert('Kecamatan berhasil ditambahkan!'); window.location.href='dt_kecamatan.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: {$stmt->error}');</script>";
        }

        $stmt->close();
    } elseif (isset($_POST['perbarui_kecamatan'])) {
        // Perbarui Data
        $id_kecamatan = $_POST['id_kecamatan'];
        $nama_kecamatan = $_POST['nm_kecamatan'];
        $stmt = $koneksi->prepare("UPDATE kecamatan SET nm_kecamatan = ? WHERE id_kecamatan = ?");
        $stmt->bind_param("si", $nama_kecamatan, $id_kecamatan);

        if ($stmt->execute()) {
            echo "<script>alert('Kecamatan berhasil diperbarui!'); window.location.href='dt_kecamatan.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: {$stmt->error}');</script>";
        }

        $stmt->close();
    }
}

// Proses Hapus Data
if (isset($_GET['hapus'])) {
    $id_kecamatan = $_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM kecamatan WHERE id_kecamatan = ?");
    $stmt->bind_param("i", $id_kecamatan);

    if ($stmt->execute()) {
        echo "<script>alert('Kecamatan berhasil dihapus!'); window.location.href='dt_kecamatan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: {$stmt->error}');</script>";
    }

    $stmt->close();
}

// Mendapatkan data kecamatan
$kecamatan_data = get_kecamatan_data($koneksi);

// Mendapatkan data untuk di-edit
$edit_kecamatan = null;
if (isset($_GET['edit'])) {
    $id_kecamatan = $_GET['edit'];
    $stmt = $koneksi->prepare("SELECT * FROM kecamatan WHERE id_kecamatan = ?");
    $stmt->bind_param("i", $id_kecamatan);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_kecamatan = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../include/head.php'; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include_once '../include/navbar.php'; ?>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <img src="../dist/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light">Admin Panel</span>
            </a>

            <div class="sidebar">
                <!-- Sidebar User Panel -->
                <?php if ($user_data): ?>
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?= htmlspecialchars($user_data['gambar']) ?>" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block"><?= htmlspecialchars($user_data['nama']) ?></a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Sidebar Menu -->
                <?php include_once '../include/sidebar_opr.php'; ?>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Data Kecamatan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                                <li class="breadcrumb-item active">Data Kecamatan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <!-- Form Tambah/Perbarui Data -->
                <?php if ($edit_kecamatan): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Form Perbarui Kecamatan</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id_kecamatan" value="<?= htmlspecialchars($edit_kecamatan['id_kecamatan']) ?>">
                                <div class="form-group">
                                    <label for="nm_kecamatan">Nama Kecamatan</label>
                                    <input type="text" class="form-control" id="nm_kecamatan" name="nm_kecamatan" required value="<?= htmlspecialchars($edit_kecamatan['nm_kecamatan']) ?>">
                                </div>
                                <button type="submit" name="perbarui_kecamatan" class="btn btn-warning">Perbarui Kecamatan</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Form Tambah Kecamatan</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="nm_kecamatan">Nama Kecamatan</label>
                                    <input type="text" class="form-control" id="nm_kecamatan" name="nm_kecamatan" required>
                                </div>
                                <button type="submit" name="tambah_kecamatan" class="btn btn-success">Tambah Kecamatan</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tabel Data Kecamatan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Data Kecamatan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kecamatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTableKecamatan">
                                    <?php if (!empty($kecamatan_data)): ?>
                                        <?php foreach ($kecamatan_data as $index => $kecamatan): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($kecamatan['nm_kecamatan']) ?></td>
                                                <td>
                                                    <a href="?edit=<?= $kecamatan['id_kecamatan'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="?hapus=<?= $kecamatan['id_kecamatan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data kecamatan</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include_once '../include/footer.php'; ?>
    <?php include_once '../include/script.php'; ?>
</body>

</html>
