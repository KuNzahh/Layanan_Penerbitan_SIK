<?php
include '../session_start.php'; // Karena session_start.php ada di root folder
include '../include/env.config.php'; // Karena env.config.php ada di folder include

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
} else {
    $menu = 'Data Pengguna';
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

// Fungsi untuk menjalankan query dan mengambil satu nilai dari hasilnya
function get_single_value($koneksi, $query, $types = "", $params = [])
{
    $stmt = $koneksi->prepare($query);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->num_rows > 0 ? $result->fetch_assoc() : null;
    $stmt->close();
    return $value;
}

// Mendapatkan jumlah operator
$operator = get_single_value($koneksi, "SELECT COUNT(*) as total FROM user WHERE level = 'operator'");
$total_operator = $operator ? $operator['total'] : 0;

// Mendapatkan jumlah pemohon
$pemohon = get_single_value($koneksi, "SELECT COUNT(*) as total FROM user WHERE level = 'pemohon'");
$total_pemohon = $pemohon ? $pemohon['total'] : 0;

// Mendapatkan jumlah admin
$admin = get_single_value($koneksi, "SELECT COUNT(*) as total FROM user WHERE level = 'admin'");
$total_admin = $admin ? $admin['total'] : 0;


// Query untuk mengambil data dari tabel `user`
$sql = "SELECT id, nama, username, email, level FROM user";
$result = $koneksi->query($sql);

// Tambahkan logika untuk pagination
$perPage = 10; // Jumlah entri per halaman
$totalEntries = $result->num_rows; // Total entri dari query
$totalPages = ceil($totalEntries / $perPage); // Total halaman
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
$offset = ($currentPage - 1) * $perPage; // Offset untuk query

// Query dengan limit dan offset
$sqlPaginated = $sql . " LIMIT $offset, $perPage";
$resultPaginated = $koneksi->query($sqlPaginated);


// Menutup koneksi database
mysqli_close($koneksi);
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
        include_once '../include/sidebar_adm.php';
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Beranda</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="br_admin.php">Beranda</a></li>
                                <li class="breadcrumb-item active"><?= $menu ?></li>
                            </ol>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Info boxes -->
                    <div class="row justify-content-center">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-center">Pemohon</span>
                                    <span class="info-box-number text-center">Jumlah <?php echo $total_pemohon; ?></span>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="tbh_pemohon.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-center">Petugas</span>
                                    <span class="info-box-number text-center">Jumlah <?php echo $total_operator; ?></span>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="tbh_petugas.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>

                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-center">Admin</span>
                                    <span class="info-box-number text-center">Jumlah <?php echo $total_admin; ?></span>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="tbh_admin.php" class=" btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- right col -->
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Data Pengguna</h3>
                                    <div class="card-tools">
                                        <form class="input-group input-group-sm" style="width: 250px;">
                                            <input type="text" name="table_search" class="form-control float-right" placeholder="Cari...">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Level</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Cek apakah ada data
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo '<tr>';
                                                    echo '<td>' . htmlspecialchars($row["nama"]) . '</td>';
                                                    echo '<td>' . htmlspecialchars($row["username"]) . '</td>';
                                                    echo '<td>' . htmlspecialchars($row["email"]) . '</td>';
                                                    echo '<td>' . htmlspecialchars($row["level"]) . '</td>';
                                                    echo '<td>
                                                                <a href="hps_user.php?id=' . $row["id"] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin ingin menghapus akun ini?\');">
                                                                    <i class="fas fa-trash"></i> Hapus
                                                                </a>
                                                            </td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="5" class="text-center">Tidak ada data pengguna</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Level</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </tfoot>

                                    </table>


                                    <div class="row mt-3">
                                        <div class="col-sm-12 col-md-5">
                                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                                Showing <?= $offset + 1 ?> to <?= min($offset + $perPage, $totalEntries) ?> of <?= $totalEntries ?> entries
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-7">
                                            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                                <ul class="pagination justify-content-end">
                                                    <!-- Previous Button -->
                                                    <li class="paginate_button page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                                        <a href="?page=<?= max(1, $currentPage - 1) ?>" aria-controls="example2" class="page-link">Previous</a>
                                                    </li>

                                                    <!-- Page Numbers -->
                                                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                                        <li class="paginate_button page-item <?= $currentPage == $i ? 'active' : '' ?>">
                                                            <a href="?page=<?= $i ?>" aria-controls="example2" class="page-link"><?= $i ?></a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <!-- Next Button -->
                                                    <li class="paginate_button page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                                        <a href="?page=<?= min($totalPages, $currentPage + 1) ?>" aria-controls="example2" class="page-link">Next</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
            </section>
            <!-- /.content -->

        </div>
    </div>
    <!-- /.row (main row) -->
    </div>
    <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php
    include_once '../include/footer.php';
    ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php
    include_once '../include/script.php';
    ?>

</body>

</html>