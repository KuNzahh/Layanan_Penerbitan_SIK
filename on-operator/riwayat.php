<?php
include '../session_start.php'; // Karena session_start.php ada di root folder
include '../include/env.config.php'; // Karena env.config.php ada di folder include

if (isset($_GET['menu'])) {
    $menu = $_GET['menu'];
} else {
    $menu = 'Riwayat';
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

// Query untuk mendapatkan data riwayat unggahan
$query = "
    SELECT 
        b.nama_instansi, 
        b.penanggung_jawab, 
        u.username,
        u.level,
        b.tgl_kegiatan,
        b.id_berkas,
        p.status_berkas,
        p.tanggal_terbit,
        p.sik
    FROM berkas_pemohon b
    LEFT JOIN persyaratan p ON b.id_berkas = p.berkas_id
    LEFT JOIN user u ON b.user_id = u.id
";

$result = $koneksi->query($query);

// Periksa apakah query berhasil
if (!$result) {
    die("Query gagal: " . $koneksi->error);
}

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
        include_once '../include/sidebar_opr.php';
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Riwayat</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="br_operator.php">Beranda</a></li>
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Unggahan</h5>
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
                                        <th>Tanggal Terbit</th>
                                        <th>Username</th>
                                        <th>Pengguna</th>
                                        <th>Nama Instansi</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Waktu</th>
                                        <th>Proses</th>
                                        <th>Status</th>
                                        <th>SIK Terbit</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTable">
                                    <?php
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$no}</td>";
                                        echo "<td>" . htmlspecialchars($row['tanggal_terbit']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['username']) . "</td>"; // Menampilkan username
                                        echo "<td>" . htmlspecialchars($row['level']) . "</td>"; // Menampilkan level pengguna
                                        echo "<td>" . htmlspecialchars($row['nama_instansi']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['penanggung_jawab']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['tgl_kegiatan']) . "</td>";
                                        echo '<td><a href="cek_detail.php?id_berkas=' . $row['id_berkas'] . '" class="btn btn-info btn-sm">Cek Detail</a></td>';
                                        echo "<td>" . htmlspecialchars($row['status_berkas']) . "</td>";

                                        if (!empty($row['sik'])) {
                                            // Jika file SIK tersedia, tampilkan tombol unduh
                                            echo '<td><a href="../sik_upload/' . htmlspecialchars(basename($row['sik'])) . '" class="btn btn-success btn-sm" target="_blank">
                                        <i class="fas fa-download"></i> Unduh SIK
                                        </a></td>';
                                        } else {
                                            // Jika file SIK belum ada
                                            echo '<td><span class="text-danger">Belum Terbit</span></td>';
                                        }
                                        echo "</tr>";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                        // Fungsi debounce untuk membatasi frekuensi pencarian
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
                            const rows = document.querySelectorAll("#dataTable tr");

                            rows.forEach((row) => {
                                const cells = Array.from(row.children);
                                const match = cells.some((cell) =>
                                    cell.textContent.toLowerCase().includes(query)
                                );
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