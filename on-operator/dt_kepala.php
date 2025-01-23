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

// Fungsi untuk mengambil data kepala
function get_kepala_data($koneksi)
{
    $result = $koneksi->query("SELECT id_kepala, nama, pangkat FROM kepala");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Proses menambah atau memperbarui data kepala
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_kepala'])) {
        $nama = $_POST['nama'];
        $pangkat = $_POST['pangkat'];

        $stmt = $koneksi->prepare("INSERT INTO kepala (nama, pangkat) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $pangkat);
        $stmt->execute();
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='dt_kepala.php';</script>";
        $stmt->close();
    } elseif (isset($_POST['perbarui_kepala'])) {
        $id_kepala = $_POST['id_kepala'];
        $nama = $_POST['nama'];
        $pangkat = $_POST['pangkat'];

        $stmt = $koneksi->prepare("UPDATE kepala SET nama = ?, pangkat = ? WHERE id_kepala = ?");
        $stmt->bind_param("ssi", $nama, $pangkat, $id_kepala);
        $stmt->execute();
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='dt_kepala.php';</script>";
        $stmt->close();
    }
}

// Mendapatkan data kepala
$kepala_data = get_kepala_data($koneksi);

// Mendapatkan data kepala untuk form edit jika ID tersedia
$edit_kepala = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $koneksi->prepare("SELECT id_kepala, nama, pangkat FROM kepala WHERE id_kepala = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_kepala = $result->fetch_assoc();
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
        <?php include_once '../include/sidebar_opr.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"> Data Kepala</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                                <li class="breadcrumb-item active"> Data Kepala</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <!-- Form Tambah/Perbarui Data -->
                <?php if (isset($_GET['edit_id'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <?= $edit_kepala ? "Form Perbarui Data Kepala" : "Form Tambah Data Kepala"; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="id_kepala" value="<?= $edit_kepala['id_kepala'] ?? ''; ?>">
                                <div class="form-group">
                                    <label for="nama">Nama Kepala</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($edit_kepala['nama'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="pangkat">Pangkat</label>
                                    <input type="text" class="form-control" id="pangkat" name="pangkat" required value="<?= htmlspecialchars($edit_kepala['pangkat'] ?? ''); ?>">
                                </div>
                                <button type="submit" name="perbarui_kepala" class="btn btn-warning">
                                    Perbarui Kepala
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tabel Data Kepala -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Data Kepala</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kepala</th>
                                        <th>Pangkat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kepala_data as $index => $kepala): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($kepala['nama']); ?></td>
                                            <td><?= htmlspecialchars($kepala['pangkat']); ?></td>
                                            <td>
                                                <a href="dt_kepala.php?edit_id=<?= $kepala['id_kepala']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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