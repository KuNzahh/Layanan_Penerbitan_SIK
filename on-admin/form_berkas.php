<?php
include '../session_start.php'; // Karena session_start.php ada di root folder
include '../include/env.config.php'; // Karena env.config.php ada di folder include

if (isset($_GET['menu'])) {

    $menu = $_GET['menu'];
} else {
    $menu = 'Dashboard';
}

if (empty($_SESSION)) {
    include_once './login.php';
    include_once './include/login.php';
}

// Mengambil ID pengguna yang sedang login dari session
$id = $_SESSION['id'];

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

// Proses pengiriman formulir
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_instansi = $_POST['nama_instansi'];
    $penanggung_jawab = $_POST['penanggung_jawab'];
    $pekerjaan = $_POST['pekerjaan'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $bentuk_kegiatan = $_POST['bentuk_kegiatan'];
    $waktu = $_POST['waktu'];
    $tempat = $_POST['tempat'];
    $rangka = $_POST['rangka'];
    $peserta = $_POST['peserta'];

    // Proses unggah file
    $berkas = '';
    if (isset($_FILES['surat_permohonan']) && $_FILES['surat_permohonan']['error'] == 0) {
        $nama_file = $_FILES['surat_permohonan']['name'];
        $file_tmp = $_FILES['surat_permohonan']['tmp_name'];
        $upload_dir = '../Berkas/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $target_file = $upload_dir . basename($nama_file);
        if (move_uploaded_file($file_tmp, $target_file)) {
            $berkas = $target_file;
        } else {
            echo "<script>alert('Pengunggahan file gagal.'); window.location.href='tambah_berkas.php';</script>";
            exit;
        }
    }
    $stmt = $koneksi->prepare("INSERT INTO berkas_pemohon (nama_instansi, penanggung_jawab, pekerjaan, alamat, no_hp, bentuk_kegiatan, waktu, tempat, rangka, peserta, berkas, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssi", $nama_instansi, $penanggung_jawab, $pekerjaan, $alamat, $no_hp, $bentuk_kegiatan, $waktu, $tempat, $rangka, $peserta, $berkas, $id_user);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan.'); window.location.href='tambah_berkas.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.location.href='tambah_berkas.php';</script>";
    }

    $stmt->close();
}

// Mendapatkan jumlah berkas pemohon
$berkas_pemohon = get_single_value($koneksi, "SELECT COUNT(*) as total FROM berkas_pemohon");
$total_berkas_pemohon = $berkas_pemohon ? $berkas_pemohon['total'] : 0;

// Mendapatkan jumlah operator
$operator = get_single_value($koneksi, "SELECT COUNT(*) as total FROM user WHERE level = 'operator'");
$total_operator = $operator ? $operator['total'] : 0;

// Mendapatkan jumlah pemohon
$pemohon = get_single_value($koneksi, "SELECT COUNT(*) as total FROM user WHERE level = 'pemohon'");
$total_pemohon = $pemohon ? $pemohon['total'] : 0;

// Mendapatkan jumlah ulasan
$ulasan = get_single_value($koneksi, "SELECT COUNT(*) as total FROM ulasan");
$total_ulasan = $ulasan ? $ulasan['total'] : 0;

// Menutup koneksi
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
        include_once '../include/navbar_adm.php';
        include_once '../include/sidebar_adm.php';
        ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6 d-flex align-items-center">
                            <button onclick="window.history.back();" class="btn btn-secondary mr-3">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                            <h1 class="m-0">Tambah Berkas Pemohon</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Beranda</a></li>
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

            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Formulir Tambah Berkas</h3>
                        </div>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="row">
                                    <!-- Kolom Kiri -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_instansi">Nama Instansi</label>
                                            <input type="text" class="form-control" id="nama_instansi" name="nama_instansi" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="penanggung_jawab">Penanggung Jawab</label>
                                            <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="pekerjaan">Pekerjaan</label>
                                            <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="alamat">Alamat</label>
                                            <input type="text" class="form-control" id="alamat" name="alamat" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="no_hp">No. HP</label>
                                            <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                                        </div>
                                    </div>
                                    <!-- Kolom Kanan -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bentuk_kegiatan">Bentuk Kegiatan</label>
                                            <input type="text" class="form-control" id="bentuk_kegiatan" name="bentuk_kegiatan" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="waktu">Waktu</label>
                                            <input type="text" class="form-control" id="waktu" name="waktu" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tempat">Tempat</label>
                                            <input type="text" class="form-control" id="tempat" name="tempat" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="rangka">Dalam Rangka</label>
                                            <input type="text" class="form-control" id="rangka" name="rangka" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="peserta">Jumlah Peserta</label>
                                            <input type="text" class="form-control" id="peserta" name="peserta" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="surat_permohonan">Unggah Persyaratan SIK (PDF)</label>
                                    <input type="file" class="form-control-file" id="surat_permohonan" name="surat_permohonan" required accept=".pdf">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            <!-- right col -->
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