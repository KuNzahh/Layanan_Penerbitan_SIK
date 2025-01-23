<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="" class="nav-link">Home</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <!-- Admin Notifications -->
        <li class="nav-item dropdown">
            <span class="nav-link">
                <?php
                date_default_timezone_set('Asia/Jakarta'); // Set zona waktu ke WIB

                $hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
                $bulan = array(
                    1 => 'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                );

                $hariIni = $hari[date('w')];
                $tanggal = date('d');
                $bulanIni = $bulan[date('n')];
                $tahun = date('Y');
                $waktu = date('H:i:s');

                echo "$hariIni, $tanggal $bulanIni $tahun $waktu";
                ?>
            </span>
        </li>
    </ul>

</nav>
<!-- /.navbar -->