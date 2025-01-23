<?php
// Memanggil pustaka FPDF
require('../fpdf/fpdf.php');

// Membuat kelas PDF yang merupakan subclass dari FPDF
class PDF extends FPDF
{
    // Konstruktor untuk mengatur ukuran halaman
    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        // Memanggil konstruktor induk FPDF dengan orientasi, unit, dan ukuran halaman
        parent::__construct($orientation, $unit, $size);

        // Mengatur margin kiri, kanan, dan atas menjadi 2.54 cm (1 inci)
        $this->SetMargins(21, 21, 21); // Margin kiri, kanan, dan atas
    }

    // Fungsi untuk menambahkan Header ke setiap halaman
    function Header()
    {
        // Cek apakah ini adalah halaman pertama
        if ($this->PageNo() == 1) {
            // Mengatur font untuk header
            $this->SetFont('Arial', '', 11);
            // Menambahkan informasi judul di kiri halaman tetapi teks rata tengah dalam halaman
            $this->Cell(85, 5, 'KEPOLISIAN NEGARA REPUBLIK INDONESIA', 0, 1, 'C');
            $this->Cell(85, 5, 'DAERAH KALIMANTAN SELATAN', 0, 1, 'C');
            $this->Cell(85, 5, 'RESOR BARITO KUALA', 0, 1, 'C');
            $this->SetFont('Arial', '', 10);
            $this->Cell(85, 5, 'Jl. Gusti M. Seman No. 1 Marabahan 70511', 'B', 1, 'C');
            // Menambahkan spasi setelah header
            $this->Ln(5);

            // Mengatur margin kiri, kanan, dan atas
            $margin = 21; // Margin kiri dan kanan dalam mm
            $logoWidth = 23; // Lebar logo dalam mm
            $pageWidth = $this->GetPageWidth(); // Lebar halaman

            // Lebar area yang tersedia di antara margin kiri dan kanan
            $usableWidth = $pageWidth - (2 * $margin);

            // Menghitung posisi X agar logo berada di tengah area yang tersedia di antara margin
            $xPosition = $margin + (($usableWidth - $logoWidth) / 2);

            // Menambahkan gambar logo di bawah informasi judul pada posisi Y saat ini
            $this->Image('../dist/img/logo.jpeg', $xPosition, $this->GetY(), $logoWidth);

            // Menambahkan jarak vertikal setelah logo
            $this->Ln(23);
        }
    }



    // Fungsi untuk menambahkan isi atau body dari dokumen PDF
    function Body()
    {
        // Mengatur judul dokumen
        $this->SetFont('Arial', 'B', 12);

        // Mengatur teks judul dan lebar teks
        $text = 'SURAT - IJIN';

        // Tentukan panjang garis bawah sesuai kebutuhan
        $lineWidth = 79; // Panjang garis bawah (dalam mm), bisa disesuaikan

        // Mengatur posisi X agar teks berada di tengah halaman
        $this->SetX(($this->GetPageWidth() - $lineWidth) / 2);

        // Membuat sel dengan panjang garis yang sudah diatur dan teks berada di tengah sel tersebut
        $this->Cell($lineWidth, 4, $text, 'B', 1, 'C'); // Opsi 'B' menambahkan garis bawah sepanjang $lineWidth

        // Mengatur font untuk konten berikutnya
        $this->SetFont('Arial', '', 11);
        $this->Ln(1);

        // Menambahkan nomor dokumen, ditampilkan di tengah halaman
        $this->Cell(0, 4, 'Nomor: SI/   / VIII /YAN.2.1./ 2024 / Intelkam', 0, 1, 'C');
        $this->Ln(3);


        $this->SetFont('Arial', '', 11);
        $this->Cell(
            10,
            6.5,
            'Pertimbangan        :',
            0,
            0
        );
        $this->Ln(1); // Pindah ke baris berikutnya


        $pertimbangan_texts = [
            "Bahwa telah dipenuhi hal yang merupakan persyaratan formal dalam hal ini kegiatan yang diajukan oleh pemohon.",
            "Bahwa kegiatan yang akan dilaksanakan dimaksud perlu diketahui oleh pihak Kepolisian Negara Republik Indonesia untuk dapat dilakukan pemantauan situasi.",
            "Bahwa kegiatan yang akan dilaksanakan ini mungkin tidak menimbulkan kerawanan kamtibmas terutama di lingkungan tempat atau lokasi kegiatan dilaksanakan."
        ];

        // Set the starting X position for the bullet points
        $this->SetX(60); // Adjust the X position after "Pertimbangan:"

        foreach ($pertimbangan_texts as $index => $text) {
            $this->Cell(5, 5, ($index + 1) . '.', 0, 0); // Nomor urut
            $this->MultiCell(0, 5, $text, 0, 'J');
            $this->SetX(60); // Reset posisi X untuk teks selanjutnya
        }
        $this->Ln(5);


        $this->Cell(
            10,
            6.5,
            'Dasar                     :',
            0,
            0
        );
        $this->Ln(1); // Pindah ke baris berikutnya

        // Memasukkan file koneksi database dan sesi
        include "../include/env.config.php";
        include "../session_start.php";

        // Pastikan $id_berkas diatur dengan benar. Contoh: ambil dari parameter GET atau POST
        $id_berkas = $_GET['id_berkas'] ?? null; // Sesuaikan metode pengambilan ID sesuai kebutuhan

        // Periksa apakah $id_berkas valid
        if (!$id_berkas) {
            die("ID berkas tidak ditemukan atau tidak valid.");
        }

        // Inisialisasi $dasar_value agar tidak undefined
        $dasar_value = '';

        // Jalankan query untuk mengambil data kolom dasar
        $query = "
            SELECT 
                b.nama_instansi, 
                b.penanggung_jawab, 
                b.pekerjaan, 
                b.alamat, 
                b.no_hp, 
                b.tgl_kegiatan, 
                CONCAT(b.tempat, ' Kec. ', k.nm_kecamatan, '') AS tempat_dengan_kecamatan, 
                b.rangka, 
                b.peserta, 
                b.dasar, 
                b.tanggal_surat,
                kg.nm_kegiatan AS nm_kegiatan
            FROM berkas_pemohon b
            LEFT JOIN kecamatan k ON b.kecamatan_id = k.id_kecamatan
            LEFT JOIN kegiatan kg ON b.kegiatan_id = kg.id_kegiatan
            WHERE b.id_berkas = ?
        ";
        $stmt = $koneksi->prepare($query);

        if ($stmt === false) {
            die("Kesalahan dalam query: " . $koneksi->error);
        }

        $stmt->bind_param("i", $id_berkas);
        $stmt->execute();
        $result = $stmt->get_result();

        // Ambil nilai dari kolom dasar
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $dasar_value = $row['dasar']; // Ambil nilai kolom dasar
        } else {
            echo "Tidak ada data ditemukan untuk id_berkas: " . $id_berkas; // Debug output jika tidak ada hasil
        }

        // Masukkan nilai ke dalam array
        $dasar_texts = [
            "Undang-undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia.",
            "Peraturan Pemerintah Nomor 60 Tahun 2017 Tentang Tata Cara Penerbitan Izin dan Pengawasan Kegiatan Keramaian, serta Kegiatan Masyarakat lainnya.",
            "Peraturan Presiden Nomor 54 Tahun 2022 Tentang Perubahan Kedua Atas Peraturan Presiden Nomor 52 Tahun 2010 Tentang Susunan Organisasi dan Tata Kerja Kepolisian Negara Republik Indonesia.",
            "Peraturan Kepala Kepolisian Negara Republik Indonesia Nomor 2 Tahun 2013 Tentang Tata Cara Perizinan dan Pemberitahuan Kegiatan Masyarakat.",
            "Peraturan Kepolisian Negara Republik Indonesia Nomor 7 Tahun 2023 Tentang Teknis perizinan, pengawasan, dan Tindakan Kepolisian pada kegiatan Keramaian umum pada kegiatan Masyarakat lainnya.",
            "Surat Telegram Kapolda Kalsel Nomor: STR/578/V/YAN.2.1/2022 Tanggal 6 Juni 2022 Tentang Tata cara penerbitan Surat Izin Keramaian.",
            $dasar_value // Ambil otomatis dari kolom dasar
        ];

        // Set the starting X position for the bullet points
        $this->SetX(60); // Adjust the X position after "Pertimbangan:"

        foreach ($dasar_texts as $index => $text) {
            $this->Cell(5, 5, ($index + 1) . '.', 0, 0); // Nomor urut
            $this->MultiCell(
                0,
                5,
                $text,
                0,
                'J'
            );
            $this->SetX(60); // Reset posisi X untuk teks selanjutnya
        }

        $this->Ln(5);

        $this->Cell(
            10,
            6.5,
            'Mengingat            :',
            0,
            0
        );
        $this->Ln(1); // Pindah ke baris berikutnya

        $kebijaksanaan_texts = [
            "Kebijaksanaan pemerintah berhubungan dengan ketentuan perundang-undangan yang berlaku untuk kegiatan Masyarakat.",
        ];

        // Set the starting X position for the bullet points
        $this->SetX(61); // Adjust the X position after "Pertimbangan:"

        foreach ($kebijaksanaan_texts as $index => $text) {
            $this->MultiCell(
                0,
                5,
                $text,
                0,
                'J'
            );
            $this->SetX(61); // Reset posisi X untuk teks selanjutnya
        }

        $this->Ln(5);

        $this->AddPage();

        //Halaman 2
        // Mengatur judul dokumen
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(
            0,
            4,
            'MEMBERIKAN IJIN',
            0,
            1,
            'C'
        );
        $this->Ln(2);

        $this->SetFont('Arial', '', 11);

        // Data tabel informasi kegiatan
        $dasar_texts = [
            "Kepada" => "", // Jika tidak diambil dari database, tetap kosong
            "Nama Instansi" => $row['nama_instansi'],
            "Penanggung Jawab" => $row['penanggung_jawab'],
            "Pekerjaan" => $row['pekerjaan'],
            "Alamat" => $row['alamat'],
            "No. HP" => $row['no_hp'],
            "Untuk" => "Penyelenggaraan Kegiatan sebagai berikut :",
            "1. Bentuk/Macam" => $row['nm_kegiatan'], // Jika ini berbeda, sesuaikan kolomnya
            "2. Waktu" => $row['tgl_kegiatan'],
            "3. Tempat" => $row['tempat_dengan_kecamatan'],
            "4. Dalam Rangka" => $row['rangka'],
            "5. Peserta" => $row['peserta']
        ];


        // Perulangan untuk menampilkan setiap pasangan kunci-nilai
        foreach ($dasar_texts as $key => $text) {
            // Cek apakah kunci adalah salah satu dari 5 data yang ingin diatur posisi awalnya
            if (in_array($key, ["1. Bentuk/Macam", "2. Waktu", "3. Tempat", "4. Dalam Rangka", "5. Peserta"])) {
                $this->SetX(66); // Mengatur posisi awal khusus untuk 5 data ini
            }

            // Menampilkan kunci (misalnya "Nama Instansi", "Penanggung Jawab", dll.)
            $this->Cell(40, 5, $key, 0, 0);
            // Menampilkan tanda ":" untuk memisahkan label dan nilai
            $this->Cell(
                5,
                5,
                ':',
                0,
                0
            );
            // Menampilkan nilai yang panjang menggunakan MultiCell agar teks dibungkus dengan rapi
            $this->MultiCell(
                0,    // Lebar sel menyesuaikan sisa lebar halaman
                5,    // Tinggi setiap baris teks
                $text, // Teks yang akan ditampilkan
                0,    // Tidak ada border
                ''    // Menyusun teks secara justify
            );
            // Pindah ke baris berikutnya setelah setiap pasangan kunci-nilai
            $this->Ln(1);
        }
        $this->Ln(3);

        $this->SetFont(
            'Arial',
            '',
            11
        );

        // Data tabel informasi kegiatan
        $catatan_texts = [
            "Dengan Catatan" => "", // Jika tidak diambil dari database, tetap kosong
            "1. " => "Penanggung jawab wajib menaati ketentuan-ketentuan sebagai berikut:",
            "a." => "Wajib menjaga keamanan dan ketertiban dalam kegiatan tersebut.",
            "b." => "Wajib mencegah supaya para peserta tidak melakukan kegiatan-kegiatan lain yang bertentangan ataupun menyimpang dari tujuan kegiatan dan tidak melanggar hukum; jika melanggar akan dicabut izinnya/diberhentikan.",
            "c." => "Wajib lapor dalam 3 x 24 jam sebelum kegiatan dilaksanakan pada Kepolisian setempat.",
            "d." => "Wajib menaati ketentuan-ketentuan lain yang diberikan oleh pejabat setempat sehubungan dengan kegiatan yang akan dilaksanakan.",
            "2." => "Bilamana terdapat penyimpangan dan melakukan pelanggaran tindak pidana terhadap hukum yang berlaku, Petugas Kepolisian/Keamanan dapat membubarkan/menghentikan atau mengambil tindakan lain berdasarkan ketentuan hukum yang berlaku.",
            "3." => "Surat izin ini diberikan kepada yang berkepentingan untuk dipergunakan sebagaimana mestinya, kecuali dalam hal terdapat kekeliruan akan diadakan ralat seperlunya.",
            "4." => "Setelah selesai kegiatan, maka penanggung jawab wajib melaporkan hasilnya kepada Kepolisian setempat yang mengeluarkan izin selambat-lambatnya satu minggu setelah kegiatan."
        ];


        // Perulangan untuk menampilkan setiap pasangan kunci-nilai
        foreach ($catatan_texts as $key => $text) {
            // Cek apakah kunci adalah salah satu dari 5 data yang ingin diatur posisi awalnya
            if (in_array($key, ["a.", "b.", "c.", "d."])) {
                $this->SetX(31); // Mengatur posisi awal khusus untuk 5 data ini
            }

            // Periksa jika kunci adalah "Dengan Catatan" untuk menambahkan garis bawah hanya pada teks
            if ($key === "Dengan Catatan") {
                // Hitung lebar teks "Dengan Catatan" saja (tanpa ":")
                $width = $this->GetStringWidth($key) + 2; // Menambahkan sedikit padding

                // Simpan posisi X dan Y saat ini
                $x = $this->GetX();
                $y = $this->GetY();

                // Tampilkan teks "Dengan Catatan" tanpa border
                $this->Cell($width, 8, $key, 0, 0);

                // Tampilkan tanda ":" setelah teks
                $this->Cell(5, 6, ':', 0, 1);

                // Tambahkan garis bawah (underline) hanya di bawah teks "Dengan Catatan"
                $this->Line($x, $y + 5.5, $x + $width, $y + 5.5); // Sesuaikan Y agar lebih dekat dengan teks
            } else {
                // Menampilkan kunci biasa tanpa garis bawah
                $this->Cell(10, 5, $key, 0, 0);
                $this->MultiCell(
                    0,    // Lebar sel menyesuaikan sisa lebar halaman
                    5,    // Tinggi setiap baris teks
                    $text, // Teks yang akan ditampilkan
                    0,    // Tidak ada border
                    'J'    // Menyusun teks secara justify
                );
            }
            // Pindah ke baris berikutnya setelah setiap pasangan kunci-nilai
            $this->Ln(1);
        }


        // Menambahkan bagian Tanggal dan Penandatanganan di kanan dengan teks rata tengah
        $this->Ln(5);

        // Mengatur posisi X untuk 50mm dari kanan halaman
        $this->SetX($this->GetPageWidth() - 71); // Margin kanan 50mm

        // Tentukan posisi X yang sama untuk kedua teks
        $positionX = $this->GetPageWidth() - 110.6; // Margin kanan 50mm

        // Atur posisi X sebelum mencetak teks pertama
        $this->SetX($positionX);
        $this->Cell(
            30,
            4,
            'Dikeluarkan di',
            0,
            0,
            'L'
        ); // Teks sebelum ':'
        $this->Cell(4, 4, ':', 0, 0, 'L');               // Tanda ':'
        $this->Cell(16, 4, 'Marabahan', 0, 1, 'L');      // Teks setelah ':'

        // Atur posisi X sebelum mencetak teks kedua
        $this->SetX($positionX);
        $this->Cell(
            30,
            4,
            'Pada Tanggal',
            0,
            0,
            'L'
        );   // Teks sebelum ':'
        $this->Cell(4, 4, ':', 0, 0, 'L');               // Tanda ':'
        // Ambil id_berkas dari parameter atau sesi (sesuaikan sesuai kebutuhan)
        $id_berkas = $_GET['id_berkas'] ?? 1; // Ganti 1 dengan id_berkas yang sesuai atau ambil dari input

        // Ambil data tanggal_surat dari tabel berkas_pemohon berdasarkan id_berkas
        $query = "SELECT tanggal_surat FROM berkas_pemohon WHERE id_berkas = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id_berkas);
        $stmt->execute();
        $stmt->bind_result($tanggal_surat);
        $stmt->fetch();
        $stmt->close();

        // Format tanggal jika diperlukan (misalnya, dari format YYYY-MM-DD ke format '23 Agustus 2024')
        if ($tanggal_surat) {
            // Ubah format tanggal jika perlu
            $formatted_date = date("d F Y", strtotime($tanggal_surat));

            // Menampilkan tanggal di PDF
            $this->SetFont('Arial', '', 11); // Font normal
            $this->Cell(
                16,
                4,
                $formatted_date,
                0,
                1,
                'L'
            );
        } else {
            echo "<script>alert('Data tanggal surat tidak ditemukan.');</script>";
        }



        // Menambahkan jarak (Ln) setelah dua baris teks pertama
        $this->Ln(2);

        // Menyimpan posisi X dan Y saat ini
        $x = $this->GetX();
        $y = $this->GetY();

        // Mengatur teks yang akan digarisbawahi
        $text = 'a.n. KAPOLRES BARITO KUALA POLDA KALSEL';
        $width = $this->GetStringWidth($text) + 2; // Menambahkan padding kecil agar garis lebih pas

        // Mengatur posisi X agar teks berada di tengah, dengan offset ke kanan jika diperlukan
        $offset = 39.6; // Tambahkan nilai offset ke kanan sesuai kebutuhan
        $x_center = ($this->GetPageWidth() - $width) / 2 + $offset;

        // Mengatur panjang garis secara manual (misalnya, lebih panjang dari teks)
        $line_length = $width + 0; // Panjang garis yang diatur secara manual, sesuaikan nilai '20' sesuai kebutuhan

        // Menggambar garis di atas teks (posisi Y dikurangi sedikit untuk mengatur garis di atas teks)
        $this->Line($x_center, $y - 1, $x_center + $line_length, $y - 1); // Menggunakan panjang garis yang diatur manual

        // Menampilkan teks di posisi yang diatur
        $this->SetX($x_center);
        $this->Cell(
            $width,
            4,
            $text,
            0,
            1,
            'C'
        );


        $this->Cell(245, 4, 'KEPALA SATUAN INTELKAM', 0, 1, 'C');
        $this->Ln(20);

        // Mengatur font menjadi bold sebelum mencetak teks
        $this->SetFont('Arial', 'B', 11); // 'B' untuk bold dan 11 untuk ukuran font
        // Ambil data terbaru dari tabel kepala
        $query = "SELECT nama, pangkat FROM kepala kepala ORDER BY id_kepala DESC LIMIT 1";
        $stmt = $koneksi->prepare($query);
        $stmt->execute();
        $stmt->bind_result(
            $nama,
            $pangkat
        );
        $stmt->fetch();
        $stmt->close();

        // Tampilkan data di PDF jika data ditemukan
        if ($nama && $pangkat) {
            // Menampilkan nama di PDF
            $this->SetFont('Arial', 'B', 11); // Bold untuk nama
            $this->Cell(
                245,
                7,
                $nama,
                0,
                1,
                'C'
            );
        } else {
            echo "<script>alert('Data kepala tidak ditemukan.');</script>";
        }


        // Mengatur kembali font ke normal jika diperlukan
        $this->SetFont('Arial', '', 11); // '' untuk font normal
        // Menyimpan posisi X dan Y saat ini
        $x = $this->GetX();
        $y = $this->GetY();

        // Tampilkan data di PDF jika data ditemukan
        if ($pangkat) {
            // Mengatur teks yang akan digarisbawahi
            $text = $pangkat; // Mengambil nilai dari field pangkat
            $width = $this->GetStringWidth($text) + 2; // Menambahkan padding kecil agar garis lebih pas

            // Mengatur posisi X agar teks berada di tengah, dengan offset ke kanan jika diperlukan
            $offset = 39.6; // Tambahkan nilai offset ke kanan sesuai kebutuhan
            $x_center = ($this->GetPageWidth() - $width) / 2 + $offset;

            // Mengatur panjang garis secara manual (misalnya, lebih panjang dari teks)
            $line_length = $width + 0; // Panjang garis yang diatur secara manual

            // Menyimpan posisi Y saat ini untuk menggambar garis
            $y = $this->GetY();

            // Menggambar garis di atas teks (posisi Y dikurangi sedikit untuk mengatur garis di atas teks)
            $this->Line($x_center, $y - 1, $x_center + $line_length, $y - 1); // Menggunakan panjang garis yang diatur manual

            // Menampilkan teks di posisi yang diatur
            $this->SetX($x_center);
            $this->Cell(
                $width,
                4,
                $text,
                0,
                1,
                'C'
            );
        } else {
            echo "<script>alert('Data pangkat tidak ditemukan.');</script>";
        }


        $this->Ln(-11);

        // Menambahkan header tembusan
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, 6, 'Tembusan :', 0, 1, 'L'); // Teks "Tembusan" di kiri dengan tanda ":"

        // Menentukan panjang teks "Tembusan"
        $tembusan_text = 'Tembusan';
        $width = $this->GetStringWidth($tembusan_text) + 2; // Tambahkan padding sedikit

        // Menyimpan posisi X dan Y saat ini
        $x = $this->GetX();
        $y = $this->GetY() - 1; // Naikkan sedikit agar garis lebih dekat ke teks

        // Menambahkan garis bawah di bawah teks "Tembusan" saja
        $this->Line($x, $y, $x + $width, $y); // Gambar garis dari posisi X ke lebar teks "Tembusan"

        // Data tembusan
        $tembusan_texts = [
            "1. Kapolres Barito Kuala",
            "2. Kabagops Polres Barito Kuala",
            "3. Kapolsek Marabahan Kota"
        ];

        // Perulangan untuk menampilkan setiap baris tembusan
        foreach ($tembusan_texts as $text) {
            // Menambahkan spasi di depan untuk indentasi jika diperlukan
            $this->Cell(5, 4, '', 0, 0); // Spasi kosong untuk indentasi
            $this->Cell(3, 6, $text, 0, 1, 'L'); // Teks tembusan dengan alignment kiri
        }

        // Garis bawah untuk item terakhir jika dibutuhkan
        $this->Ln(0);
        $this->SetX(22); // Atur posisi X ke margin kiri
        $this->Cell(62, 0, '', 'B', 0, 'L'); // Garis bawah dengan panjang 80mm (sesuaikan panjang sesuai kebutuhan)

    }
}


// Mengatur ukuran halaman yang diinginkan (misalnya 'A4')
$paperSize = 'A4';

// Membuat objek PDF baru dengan ukuran halaman khusus
$pdf = new PDF('P', 'mm', $paperSize);
$pdf->AddPage();

// Menjalankan fungsi Body untuk menambahkan isi dokumen
$pdf->Body();

// Menampilkan file PDF ke browser
$pdf->Output();
