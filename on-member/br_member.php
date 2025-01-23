<?php
include '../session_start.php'; // Karena session_start.php ada di root folder
include '../include/env.config.php'; // Karena env.config.php ada di folder include

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


// Mendapatkan data pengguna
$user_data = get_user_data($koneksi, $id);

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
        include_once '../include/sidebar_mbr.php';
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header" style="display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center; background: url('../dist/img/polresbg.jpg') no-repeat center center; background-size: cover; flex-direction: column;">
                <div class="card" style="background: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                    <div class="font-center text-center">
                        <h2 class="h4 h2-responsive">Selamat Datang <?= $_SESSION['username'] ?>!</h2>
                        <p class="lead">
                            Kami hadir untuk mempermudah proses penerbitan Surat Izin Keramaian Anda. Dengan layanan online ini,
                            Anda dapat mengajukan, memantau, dan mendapatkan izin dengan cepat, mudah, dan transparan.
                        </p>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Kiri: Google Maps -->
                    <div class="col-md-6">
                        <div class="card" style="width: 100%; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); margin-bottom: 30px;">
                            <div class="card-header"> Lokasi Polres Barito Kuala</div>
                            <!-- Google Maps Embed -->
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m27!1m12!1m3!1d3984.4272028185696!2d114.76206027351412!3d-2.978855139815565!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m12!3e6!4m4!1s0x2de44e4a5f41e2eb%3A0x2bc9630c89f87492!3m2!1d-2.9788604999999997!2d114.7646352!4m5!1s0x2de44e4a5f41e2eb%3A0x2bc9630c89f87492!2sJl.%20Gusti%20M.%20Seman%20No.1%2C%20Ulu%20Benteng%2C%20Kec.%20Marabahan%2C%20Kabupaten%20Barito%20Kuala%2C%20Kalimantan%20Selatan%2070513!3m2!1d-2.9788604999999997!2d114.7646352!5e0!3m2!1sid!2sid!4v1736911771345!5m2!1sid!2sid"
                                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>

                    <!-- Kanan: Visi, Misi, dan Motto -->
                    <div class="col-md-6">
                        <div class="card" style="width: 100%; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); margin-bottom: 30px;">
                            <div class="card-header"> Visi Misi</div>
                            <!-- Visi Section -->
                            <h5 class="card-title" style="font-size: 1.5rem; font-weight: 600; color: #2C3E50;">VISI:</h5>
                            <p style="font-size: 1.25rem; color: #34495E;">
                                Memberikan pelayanan prima di bidang pelayanan kepada masyarakat secara profesional, transparan, dan akuntabel.
                            </p>

                            <!-- Misi Section -->
                            <h5 class="card-title" style="font-size: 1.5rem; font-weight: 600; color: #2C3E50;">MISI:</h5>
                            <ol style="padding-left: 40px; font-size: 1.25rem; color: #34495E;">
                                <li>Melayani sesuai dengan standar operasional.</li>
                                <li>Melayani tanpa ada pungutan selain yang sudah ditentukan dalam PNBP.</li>
                                <li>Melayani dengan ramah, cepat, dan dapat dipertanggungjawabkan.</li>
                            </ol>

                            <!-- Motto Section -->
                            <h5 class="card-title" style="font-size: 1.5rem; font-weight: 600; color: #2C3E50;">MOTTO:</h5>
                            <p style="font-size: 1.25rem; font-style: italic; color: #7F8C8D;">
                                "Melayani Masyarakat adalah Kebanggaan Kami"
                            </p>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.content-header -->
            <section class="content">
                <div class="card mt-3">
                    <div class="card-body">
                        <p class="text-center" style="font-size: 1.5rem; font-weight: 600; color: #2C3E50;">Lengkapi Persyaratan Sebelum Mengajukan Surat Izin Keramaian</p>
                        <div class="row justify-content-center">
                            <!-- Tengah -->
                            <div class="col-md-8">
                                <div class="card border-info shadow-lg" style="border-radius: 15px;">
                                    <div class="card-header text-center" style="background-color: #3498db; color: white;">
                                        <h5 class="card-title mb-0">Syarat Pembuatan SIK</h5>
                                    </div>
                                    <div class="card-body" style="background-color: #ecf6ff; text-align: justify; border-radius: 0 0 15px 15px;">
                                        <ul style="list-style: none; padding-left: 0;">
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Surat Permohonan ditujukan kepada Kapolres Batola dan atau UP.Kasat Intelkam Polres Batola selambat-lambatnya 7 x 24 jam sebelum kegiatan dilaksanakan;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Jadwal Acara;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Daftar Susunan Panitia Penyelenggara;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Daftar Susunan Pengurus Organisasi;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Nama-nama Peserta/Undangan;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Nama-nama Pembicara dan Judul Makalah;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Fotokopi Paspor dan Visa (Bagi Pembicara Warga Negara Asing);
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> AD/ART Organisasi;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Akte Pendirian Organisasi;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Proposal;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Curriculum Vitae (Riwayat Hidup) bagi Pembicara Warga Negara Asing;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Rute yang Dilalui bila Kegiatannya berbentuk Pawai dan atau Karnaval;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Surat Keterangan/Ijin Lokasi Kegiatan;
                                            </li>
                                            <li style="font-size: 1.15rem; padding: 10px 0;">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Surat Rekomendasi Dinas/Instansi terkait.
                                            </li>
                                        </ul>
                                        <div class="text-center">
                                            <a href="form_pengajuan.php" class="btn btn-primary" style="border-radius: 25px; padding: 10px 20px; font-size: 1.1rem; transition: background-color 0.3s ease-in-out;">Ajukan SIK</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Kiri Bawah -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg rounded-4" style="border-left: 4px solid #007bff;">
                                    <div class="card-header" style="background-color: #f7f9fc;">
                                        <h5 class="card-title mb-0 text-center" style="color: #007bff; font-weight: 600;">
                                            <i class="fas fa-info-circle"></i> Panduan Sik Online
                                        </h5>
                                    </div>
                                    <div class="card-body" style="background-color: #e9f7fe; text-align: justify;">
                                        <p style="font-size: 1rem; color: #555;">Mekanisme pembuatan SIK Online untuk mempermudah pengajuan izin secara cepat dan transparan. Ikuti langkah-langkah berikut untuk proses pendaftaran.</p>
                                        <a href="https://youtu.be/mHu9GMfQN3Q?si=7Sx8Toz4_kY0dDGh" class="btn btn-outline-primary mt-3" style="width: 100%;">Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                            <!-- Kanan Bawah -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg rounded-4" style="border-left: 4px solid #28a745; transition: transform 0.3s ease-in-out;">
                                    <div class="card-header" style="background-color: #f7f9fc;">
                                        <h5 class="card-title mb-0 text-center" style="color: #28a745; font-weight: 600; font-size: 1.25rem;">
                                            <i class="fas fa-headset" style="margin-right: 10px; font-size: 1.4rem;"></i> Bantuan
                                        </h5>
                                    </div>
                                    <div class="card-body" style="background-color: #e9f8e4; text-align: justify; padding: 20px;">
                                        <p style="font-size: 1rem; color: #555; line-height: 1.6;">
                                            Jika Anda membutuhkan bantuan lebih lanjut, Anda bisa menghubungi kami di nomor:
                                            <strong style="color: #28a745; font-weight: 600; font-size: 1.5rem;">0811584234214</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- CSS tambahan untuk efek hover -->
                            <style>
                                .card {
                                    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
                                }

                                .card:hover {
                                    transform: scale(0.9);
                                    /* Efek perbesaran saat hover */
                                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                                    /* Menambahkan bayangan */
                                }
                            </style>

                            <!-- CSS Media Queries -->
                            <style>
                                /* Responsif untuk layar kecil */
                                @media (max-width: 576px) {
                                    .font-center {
                                        padding: 10px;
                                    }

                                    .card {
                                        margin: 10px;
                                        padding: 15px;
                                    }

                                    .h2-responsive {
                                        font-size: 1.5rem;
                                        /* Ukuran font lebih kecil pada layar kecil */
                                    }

                                    .lead {
                                        font-size: 1rem;
                                    }
                                }

                                /* Responsif untuk layar medium */
                                @media (max-width: 768px) {
                                    .font-center {
                                        padding: 15px;
                                    }

                                    .card {
                                        margin: 15px;
                                        padding: 20px;
                                    }

                                    .h2-responsive {
                                        font-size: 1.75rem;
                                        /* Ukuran font menyesuaikan dengan layar medium */
                                    }

                                    .lead {
                                        font-size: 1.125rem;
                                    }
                                }
                            </style>

                        </div>
            </section>
        </div>
    </div>
    <!-- /.row (main row) -->
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