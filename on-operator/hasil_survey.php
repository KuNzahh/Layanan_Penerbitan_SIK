<?php
include '../session_start.php';
include '../include/env.config.php';

// Mendapatkan ID pengguna yang sedang login dari session
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

// Mendapatkan tahun dari filter
$filterYear = $_GET['filterYear'] ?? date('Y');

// Query untuk mendapatkan rata-rata per bulan
$query = "
    SELECT 
        MONTHNAME(tanggal_survei) AS bulan,
        COUNT(*) AS jumlah_responden,
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
$stmt->bind_param("i", $filterYear);
$stmt->execute();
$result = $stmt->get_result();

$survey_data = [];
$chart_labels = [];
$chart_data = [];

while ($row = $result->fetch_assoc()) {
    $survey_data[] = $row;
    $chart_labels[] = $row['bulan'];
    $chart_data[] = $row['rata_rata'];
}

$stmt->close();
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
        <?php include_once '../include/sidebar_opr.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Hasil Survey</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                                <li class="breadcrumb-item active">Survey</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <!-- Filter dan Tabel -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Survey Kepuasan Pelanggan</h5>
                        <div class="float-right">
                            <form method="GET">
                                <select class="form-control" name="filterYear" onchange="this.form.submit()">
                                    <?php for ($year = date('Y'); $year >= 2000; $year--): ?>
                                        <option value="<?= $year ?>" <?= $filterYear == $year ? 'selected' : '' ?>><?= $year ?></option>
                                    <?php endfor; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Jumlah Responden</th>
                                        <th>Rata-rata Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($survey_data)): ?>
                                        <?php foreach ($survey_data as $data): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($data['bulan']) ?></td>
                                                <td><?= htmlspecialchars($data['jumlah_responden']) ?></td>
                                                <td><?= htmlspecialchars($data['rata_rata']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data survei untuk tahun ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <a href="cetak_lapsurvey.php<?= $filterYear ? '?filterYear=' . $filterYear : '' ?>" class="btn btn-success mt-3 float-right">Cetak Laporan Survey</a>
                    </div>
                </div>

                <!-- Grafik Survey -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">Grafik Survey Kepuasan</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="surveyChart" height="300"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include_once '../include/footer.php'; ?>
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
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>

</html>
