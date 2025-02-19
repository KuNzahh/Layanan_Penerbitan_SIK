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
$user_data = get_user_data($koneksi, $id);

// Mendapatkan data kegiatan untuk dropdown
$kegiatan_query = "SELECT id_kegiatan, nm_kegiatan FROM kegiatan";
$kegiatan_result = $koneksi->query($kegiatan_query);

// Mendapatkan data kecamatan untuk dropdown
$kecamatan_query = "SELECT id_kecamatan, nm_kecamatan FROM kecamatan";
$kecamatan_result = $koneksi->query($kecamatan_query);

// Menangani pengiriman form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil input dan melakukan sanitasi
    $nama_instansi = htmlspecialchars($_POST['nama_instansi'], ENT_QUOTES, 'UTF-8');
    $penanggung_jawab = htmlspecialchars($_POST['penanggung_jawab'], ENT_QUOTES, 'UTF-8');
    $pekerjaan = htmlspecialchars($_POST['pekerjaan'], ENT_QUOTES, 'UTF-8');
    $alamat = htmlspecialchars($_POST['alamat'], ENT_QUOTES, 'UTF-8');
    $no_hp = htmlspecialchars($_POST['no_hp'], ENT_QUOTES, 'UTF-8');
    $kegiatan_id = $_POST['kegiatan_id'];
    // Validasi input tanggal 'waktu'
    $waktu = isset($_POST['waktu']) && !empty($_POST['waktu']) ? date('Y-m-d H:i:s', strtotime($_POST['waktu'])) : null;
    $tgl_kegiatan = htmlspecialchars($_POST['tgl_kegiatan'], ENT_QUOTES, 'UTF-8');
    $tempat = htmlspecialchars($_POST['tempat'], ENT_QUOTES, 'UTF-8');
    $kecamatan_id = $_POST['kecamatan_id'];
    $rangka = htmlspecialchars($_POST['rangka'], ENT_QUOTES, 'UTF-8');
    $jumlah_peserta = $_POST['jumlah_peserta'];
    $berkas = null;

    // Validasi file
    if (isset($_FILES['berkas']) && $_FILES['berkas']['error'] == 0) {
        $upload_dir = '../Berkas/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['berkas']['tmp_name'];
        $file_name = basename($_FILES['berkas']['name']);
        $target_file = $upload_dir . $file_name;

        // Validasi ekstensi file
        $allowed_extensions = ['pdf'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            echo "<script>alert('Hanya file PDF yang diperbolehkan.'); window.history.back();</script>";
            exit;
        }

        if (move_uploaded_file($file_tmp, $target_file)) {
            $berkas = $target_file;
        } else {
            echo "<script>alert('Gagal mengunggah file.'); window.history.back();</script>";
            exit;
        }
    }

    // Simpan data ke database
    $stmt = $koneksi->prepare("
        INSERT INTO berkas_pemohon 
        (user_id, nama_instansi, penanggung_jawab, pekerjaan, alamat, no_hp, kegiatan_id, waktu, tgl_kegiatan, tempat, kecamatan_id, rangka, peserta, berkas) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)

    ");
    $stmt->bind_param(
        "issssssissssis",
        $id,
        $nama_instansi,
        $penanggung_jawab,
        $pekerjaan,
        $alamat,
        $no_hp,
        $kegiatan_id,
        $waktu,
        $tgl_kegiatan,
        $tempat,
        $kecamatan_id,
        $rangka,
        $jumlah_peserta,
        $berkas
    );
    


    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan.'); window.location.href='status_riwayat.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.history.back();</script>";
    }
    $stmt->close();
}

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
        <?php include_once '../include/sidebar_mbr.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Formulir Pengajuan SIK</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                                <li class="breadcrumb-item active">Formulir</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-primary">Lengkapi berkas di bawah ini</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_instansi">Nama Instansi</label>
                                        <input type="text" class="form-control" id="nama_instansi" name="nama_instansi" placeholder="Nama Instansi" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="penanggung_jawab">Penanggung Jawab</label>
                                        <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab" placeholder="Penanggung Jawab" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="pekerjaan">Pekerjaan</label>
                                        <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" placeholder="Pekerjaan" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat">Alamat</label>
                                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_hp">No. Hp</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="No. Hp" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="kegiatan_id">Bentuk Kegiatan</label>
                                        <select class="form-control" id="kegiatan_id" name="kegiatan_id" required>
                                            <option value="">-- Pilih Kegiatan --</option>
                                            <?php while ($row = $kegiatan_result->fetch_assoc()): ?>
                                                <option value="<?= $row['id_kegiatan'] ?>"><?= htmlspecialchars($row['nm_kegiatan']) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tgl_kegiatan">Waktu</label>
                                        <input type="text" class="form-control" id="tgl_kegiatan" name="tgl_kegiatan" placeholder="Waktu Keramaian" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="tempat">Tempat</label>
                                        <input type="text" class="form-control" id="tempat" name="tempat" placeholder="Tempat" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="kecamatan_id">Kecamatan</label>
                                        <select class="form-control" id="kecamatan_id" name="kecamatan_id" required>
                                            <option value="">-- Pilih Kecamatan --</option>
                                            <?php while ($row = $kecamatan_result->fetch_assoc()): ?>
                                                <option value="<?= $row['id_kecamatan'] ?>"><?= htmlspecialchars($row['nm_kecamatan']) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="rangka">Dalam Rangka</label>
                                        <input type="text" class="form-control" id="rangka" name="rangka" placeholder="Dalam Rangka" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="jumlah_peserta">Jumlah Peserta</label>
                                        <input type="number" class="form-control" id="jumlah_peserta" name="jumlah_peserta" placeholder="Jumlah Peserta" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="berkas">Unggah Persyaratan SIK (PDF)</label>
                                <input type="file" class="form-control-file" id="berkas" name="berkas" accept=".pdf" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

    </div>
    <?php include_once '../include/footer.php'; ?>
    <?php include_once '../include/script.php'; ?>
    <script>
        document.querySelector('form').onsubmit = function() {
            var waktuInput = document.getElementById('waktu');
            var waktu = new Date(waktuInput.value);
            var formattedDate = waktu.toISOString().split('T')[0]; // Format: yyyy-mm-dd
            waktuInput.value = formattedDate;
        }
    </script>

</body>

</html>