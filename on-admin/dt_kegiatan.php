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

// Fungsi untuk mengambil data kegiatan
function get_kegiatan_data($koneksi)
{
    $result = $koneksi->query("SELECT id_kegiatan, nm_kegiatan FROM kegiatan");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Proses Tambah atau Edit Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_kegiatan'])) {
        // Tambah Data
        $nama_kegiatan = $_POST['nm_kegiatan'];
        $stmt = $koneksi->prepare("INSERT INTO kegiatan (nm_kegiatan) VALUES (?)");
        $stmt->bind_param("s", $nama_kegiatan);

        if ($stmt->execute()) {
            echo "<script>alert('Kegiatan berhasil ditambahkan!'); window.location.href='dt_kegiatan.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: {$stmt->error}');</script>";
        }

        $stmt->close();
    } elseif (isset($_POST['perbarui_kegiatan'])) {
        // Perbarui Data
        $id_kegiatan = $_POST['id_kegiatan'];
        $nama_kegiatan = $_POST['nm_kegiatan'];
        $stmt = $koneksi->prepare("UPDATE kegiatan SET nm_kegiatan = ? WHERE id_kegiatan = ?");
        $stmt->bind_param("si", $nama_kegiatan, $id_kegiatan);

        if ($stmt->execute()) {
            echo "<script>alert('Kegiatan berhasil diperbarui!'); window.location.href='dt_kegiatan.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: {$stmt->error}');</script>";
        }

        $stmt->close();
    }
}

// Proses Hapus Data
if (isset($_GET['hapus'])) {
    $id_kegiatan = $_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM kegiatan WHERE id_kegiatan = ?");
    $stmt->bind_param("i", $id_kegiatan);

    if ($stmt->execute()) {
        echo "<script>alert('Kegiatan berhasil dihapus!'); window.location.href='dt_kegiatan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: {$stmt->error}');</script>";
    }

    $stmt->close();
}

// Mendapatkan data Kegiatan
$kegiatan_data = get_kegiatan_data($koneksi);

// Mendapatkan data untuk di-edit
$edit_kegiatan = null;
if (isset($_GET['edit'])) {
    $id_kegiatan = $_GET['edit'];
    $stmt = $koneksi->prepare("SELECT * FROM kegiatan WHERE id_kegiatan = ?");
    $stmt->bind_param("i", $id_kegiatan);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_kegiatan = $result->fetch_assoc();
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
                <?php include_once '../include/sidebar_adm.php'; ?>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Data Kegiatan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="br_admin.php">Beranda</a></li>
                                <li class="breadcrumb-item active">Data Kegiatan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <!-- Form Tambah/Perbarui Data -->
                <?php if ($edit_kegiatan): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Form Perbarui Kegiatan</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id_kegiatan" value="<?= htmlspecialchars($edit_kegiatan['id_kegiatan']) ?>">
                                <div class="form-group">
                                    <label for="nm_kegiatan">Nama Kegiatan</label>
                                    <input type="text" class="form-control" id="nm_kegiatan" name="nm_kegiatan" required value="<?= htmlspecialchars($edit_kegiatan['nm_kegiatan']) ?>">
                                </div>
                                <button type="submit" name="perbarui_kegiatan" class="btn btn-warning">Perbarui Kegiatan</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Form Tambah Kegiatan</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="nm_kegiatan">Nama Kegiatan</label>
                                    <input type="text" class="form-control" id="nm_kegiatan" name="nm_kegiatan" required>
                                </div>
                                <button type="submit" name="tambah_kegiatan" class="btn btn-success">Tambah Kegiatan</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tabel Data Kegiatan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daftar Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTableKegiatan">
                                    <?php if (!empty($kegiatan_data)): ?>
                                        <?php foreach ($kegiatan_data as $index => $kegiatan): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($kegiatan['nm_kegiatan']) ?></td>
                                                <td>
                                                    <a href="?edit=<?= $kegiatan['id_kegiatan'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="?hapus=<?= $kegiatan['id_kegiatan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data Kegiatan</td>
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
