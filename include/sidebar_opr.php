<aside class="main-sidebar sidebar-dark-success elevation-4" style="background-color: #4B4B4B;">
    <!-- Logo Brand -->
    <a href="br_admin.php" class="brand-link d-flex align-items-center justify-content-center">
        <i class="nav-icon fas fa-user mr-3"></i>
        <span class="brand-text font-weight-light"><?= $_SESSION['level'] ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="overflow-y: auto; max-height: 100vh;">
        <!-- Panel Pengguna -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= htmlspecialchars($user_data['gambar'] ?? '../dist/img/avatar5.png') ?>" 
                     class="img-circle elevation-2" alt="Foto Pengguna">
            </div>
            <div class="info">
                <a href="profil.php" class="d-block"><?= $_SESSION['username'] ?></a>
            </div>
        </div>

        <!-- Menu Sidebar -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="br_operator.php" class="nav-link" data-page="br_operator.php">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Beranda</p>
                    </a>
                </li>

                <!-- Master Data -->
                <li class="nav-item" data-parent="pengajuan">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Proses Berkas
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="daftar_berkas.php" class="nav-link" data-page="daftar_berkas.php">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Berkas Baru</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="riwayat.php" class="nav-link" data-page="riwayat.php">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Riwayat</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Pengaturan Sistem -->
                <li class="nav-item" data-parent="pengajuan">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Data Sistem
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="dt_kepala.php" class="nav-link" data-page="dt_kepala.php">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Kepala</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="dt_arsip.php" class="nav-link" data-page="dt_arsip.php">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Arsip</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="dt_kecamatan.php" class="nav-link" data-page="dt_kecamatan.php">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Kecamatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="dt_kegiatan.php" class="nav-link" data-page="dt_kegiatan.php">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Kegiatan</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Laporan -->
                <li class="nav-item" data-parent="pengajuan">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Laporan
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="laporan_arsip.php" class="nav-link" data-page="laporan_arsip.php">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Arsip</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Ulasan -->
                <li class="nav-item">
                    <a href="hasil_survey.php" class="nav-link" data-page="hasil_survey.php">
                        <i class="nav-icon fas fa-comments"></i>
                        <p> Hasil Survey </p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="../include/logout.php" class="nav-link bg-danger" onclick="return confirmLogout();">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.menu sidebar -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const currentPage = window.location.pathname.split("/").pop();
        const navItems = document.querySelectorAll(".nav-item");

        navItems.forEach(item => {
            const links = item.querySelectorAll("a.nav-link[data-page]");

            links.forEach(link => {
                if (link.getAttribute("data-page") === currentPage) {
                    link.classList.add("active");

                    // Buka menu jika link adalah bagian dari submenu
                    const parentItem = item.closest(".nav-item");
                    if (parentItem && parentItem.querySelector(".nav-treeview")) {
                        parentItem.classList.add("menu-open");
                    }
                }
            });
        });
    });

    function confirmLogout() {
        return confirm("Apakah Anda yakin ingin keluar?");
    }
</script>
