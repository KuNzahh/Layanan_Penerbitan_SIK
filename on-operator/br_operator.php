<?php
include '../session_start.php';
include '../include/env.config.php';

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

// Fungsi untuk mendapatkan satu nilai dari database
function get_single_value($koneksi, $query, $types = "", $params = [])
{
    $stmt = $koneksi->prepare($query);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_assoc();
    $stmt->close();
    return $value;
}

// Mendapatkan data pengguna
$user_data = get_user_data($koneksi, $id);

// Mendapatkan jumlah data
$total_berkas_pemohon = get_single_value($koneksi, "SELECT COUNT(*) as total FROM berkas_pemohon")['total'];
$total_operator = get_single_value($koneksi, "SELECT COUNT(*) as total FROM user WHERE level = 'operator'")['total'];
$total_pemohon = get_single_value($koneksi, "SELECT COUNT(*) as total FROM user WHERE level = 'pemohon'")['total'];
$total_survey = get_single_value($koneksi, "SELECT COUNT(*) as total FROM survey")['total'];

// Simulasi Target untuk Progress Bar
$target_berkas = 200;
$target_operator = 50;
$target_pemohon = 500;
$target_survey = 1000;

// Query data untuk grafik rata-rata
$query = "
    SELECT 
        MONTHNAME(tanggal_survei) AS bulan,
        ROUND(AVG(
            (CASE pertanyaan1 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END +
                CASE pertanyaan2 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END +
                CASE pertanyaan3 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END +
                CASE pertanyaan4 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END +
                CASE pertanyaan5 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END +
                CASE pertanyaan6 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END +
                CASE pertanyaan7 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END +
                CASE pertanyaan8 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END +
                CASE pertanyaan9 
                    WHEN 'Sangat Tidak Puas' THEN 1
                    WHEN 'Tidak Puas' THEN 2
                    WHEN 'Cukup Puas' THEN 3
                    WHEN 'Puas' THEN 4
                    WHEN 'Sangat Puas' THEN 5
                END
                ) / 9), 2) AS rata_rata
        FROM survey
        WHERE YEAR(tanggal_survei) = ?
        GROUP BY MONTH(tanggal_survei)
        ORDER BY MONTH(tanggal_survei) ASC
";

$stmt = $koneksi->prepare($query);
$filterYear = date('Y'); // Tahun aktif
$stmt->bind_param("i", $filterYear);
$stmt->execute();
$result = $stmt->get_result();

$chart_labels = [];
$chart_data = [];

while ($row = $result->fetch_assoc()) {
    $chart_labels[] = $row['bulan'];
    $chart_data[] = $row['rata_rata'];
}

$stmt->close();
mysqli_close($koneksi);
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

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                    <div class=" col-sm-12 text-center">
                            <h1 class="m-0">Selamat Datang, <?= $_SESSION['username'] ?>......</h1>
                            <h3 class="m-0"> "Selamat datang di sistem penerbitan Surat Izin Keramaian (SIK). Di halaman ini, Anda dapat memantau kinerja proses penerbitan SIK secara real-time dan mendapatkan ringkasan informasi terkini."</h3>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Statistik -->
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8">
                            <div class="small-box bg-info">
                                <div class="inner text-center">
                                    <h3><?= $total_berkas_pemohon ?></h3>
                                    <p>Berkas Pemohon</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="daftar_berkas.php" class="small-box-footer">Lihat Berkas <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-8">
                            <div class="small-box bg-danger">
                                <div class="inner text-center">
                                    <h3><?= $total_survey ?></h3>
                                    <p>Survey</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="hasil_survey.php" class="small-box-footer">Lihat Survey <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Grafik dan Progress Bar -->
                <section class="content">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <!-- Grafik -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="text-center">Grafik Rata-rata Nilai Kepuasan</h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="surveyChart" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info Bars -->
                                <div class="col-md-6">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-primary text-white">
                                            <h4 class="text-center mb-0"><strong>Pembaruan Sistem</strong></h4>
                                        </div>
                                        <div class="card-body" style="font-size: 16px; line-height: 1.8;">
                                            <ul style="list-style-type: none; padding-left: 0;">
                                                <li>✔ <strong>Pastikan</strong> semua pengajuan berkas telah diperiksa sebelum disetujui.</li>
                                                <li>✔ <strong>Gunakan</strong> fitur pencarian untuk mempermudah menemukan data tertentu.</li>
                                                <li>✔ <strong>Cek</strong> notifikasi berkala untuk mengetahui pembaruan status terbaru.</li>
                                            </ul>
                                            <br>
                                            <strong>Catatan:</strong> Sistem ini dilengkapi dengan fitur pelaporan yang memudahkan Anda merekap data untuk keperluan evaluasi bulanan.
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </section>

        </div>
        </section>
    </div>

    <?php include_once '../include/footer.php'; ?>
    </div>

    <?php include_once '../include/script.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?= json_encode($chart_labels) ?>;
        const data = <?= json_encode($chart_data) ?>;

        const ctx = document.getElementById('surveyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rata-rata Nilai Kepuasan',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nilai Rata-rata'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>