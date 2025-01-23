<?php
include '../session_start.php'; // Untuk memulai session
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $tanggal_survei = $_POST['tanggal_survei'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $pendidikan = $_POST['pendidikan'] ?? '';
    $pekerjaan = $_POST['pekerjaan'] ?? '';
    $responses = [];

    // Validasi tanggal survei
    if (empty($tanggal_survei)) {
        echo "<script>alert('Tanggal survei harus diisi.'); window.history.back();</script>";
        exit;
    }

    // Ambil jawaban pertanyaan
    for ($i = 1; $i <= 9; $i++) {
        $responses[] = $_POST['question_' . $i] ?? '';
    }

    // Hitung rata-rata jawaban
    $skor_mapping = [
        'Sangat Tidak Puas' => 1,
        'Tidak Puas' => 2,
        'Cukup Puas' => 3,
        'Puas' => 4,
        'Sangat Puas' => 5
    ];
    $scores = array_map(function ($response) use ($skor_mapping) {
        return $skor_mapping[$response] ?? 0;
    }, $responses);
    $rata_rata = array_sum($scores) / count($scores);

    // Validasi data sebelum dimasukkan ke database
    if (empty($jenis_kelamin) || empty($pendidikan) || empty($pekerjaan) || in_array('', $responses, true)) {
        echo "<script>alert('Harap isi semua data dengan lengkap.'); window.history.back();</script>";
        exit;
    }

    // Masukkan data ke dalam database
    $stmt = $koneksi->prepare("
        INSERT INTO survey (
            user_id, tanggal_survei, jenis_kelamin, pendidikan, pekerjaan, 
            pertanyaan1, pertanyaan2, pertanyaan3, 
            pertanyaan4, pertanyaan5, pertanyaan6, 
            pertanyaan7, pertanyaan8, pertanyaan9, rata_rata
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssssssssssss",
        $id,
        $tanggal_survei,
        $jenis_kelamin,
        $pendidikan,
        $pekerjaan,
        $responses[0],
        $responses[1],
        $responses[2],
        $responses[3],
        $responses[4],
        $responses[5],
        $responses[6],
        $responses[7],
        $responses[8],
        $rata_rata
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data survei berhasil disimpan.'); window.location.href='terimakasih.php';</script>";
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
        <?php
        include_once '../include/navbar.php';
        include_once '../include/sidebar_mbr.php';
        ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Survey Kepuasan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                                <li class="breadcrumb-item active">Survey Kepuasan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-primary">Survey Kepuasan</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="tanggal_survei">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal_survei" name="tanggal_survei" required>
                            </div>

                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="pendidikan">Pendidikan</label>
                                <select class="form-control" id="pendidikan" name="pendidikan" required>
                                    <option value="">Pilih</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Sarjana">Sarjana</option>
                                    <option value="Magister">Magister</option>
                                    <option value="Doktor">Doktor</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="pekerjaan">Pekerjaan</label>
                                <select class="form-control" id="pekerjaan" name="pekerjaan" required>
                                    <option value="">Pilih</option>
                                    <option value="PNS">PNS</option>
                                    <option value="Swasta">Swasta</option>
                                    <option value="Wirausaha">Wirausaha</option>
                                    <option value="Pelajar/Mahasiswa">Pelajar/Mahasiswa</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <h5 class="mt-4">Pendapat Responden Tentang Pelayanan</h5>
                            <?php
                            $questions = [
                                "Bagaimana pendapat Saudara tentang kesesuaian fitur aplikasi dengan kebutuhan pengguna?",
                                "Bagaimana kemudahan Saudara dalam menggunakan antarmuka aplikasi ini?",
                                "Bagaimana pendapat Saudara tentang kecepatan sistem dalam memproses data atau dokumen?",
                                "Bagaimana pendapat Saudara tentang transparansi informasi yang disediakan dalam sistem?",
                                "Bagaimana pendapat Saudara tentang akurasi data atau dokumen yang dihasilkan oleh sistem?",
                                "Bagaimana pendapat Saudara tentang respon tim dukungan dalam menyelesaikan masalah teknis?",
                                "Bagaimana pendapat Saudara tentang kemudahan akses terhadap layanan penerbitan secara online?",
                                "Bagaimana pendapat Saudara tentang keamanan data pengguna di dalam sistem?",
                                "Bagaimana pendapat Saudara tentang kemampuan aplikasi dalam memberikan notifikasi atau pembaruan secara tepat waktu?"
                            ];

                            foreach ($questions as $index => $question) {
                                echo '
                                <div class="form-group">
                                    <label for="question_' . ($index + 1) . '">' . ($index + 1) . '. ' . $question . '</label>
                                    <select class="form-control" id="question_' . ($index + 1) . '" name="question_' . ($index + 1) . '" required>
                                        <option value="">Pilih Jawaban</option>
                                        <option value="Sangat Tidak Puas">Sangat Tidak Puas</option>
                                        <option value="Tidak Puas">Tidak Puas</option>
                                        <option value="Cukup Puas">Cukup Puas</option>
                                        <option value="Puas">Puas</option>
                                        <option value="Sangat Puas">Sangat Puas</option>
                                    </select>
                                </div>
                                ';
                            }
                            ?>

                            <button type="submit" class="btn btn-primary btn-block">Kirim</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include_once '../include/footer.php'; ?>
</body>

</html>