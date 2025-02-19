<?php
require('../fpdf/fpdf.php'); // Pastikan path FPDF benar
include '../include/env.config.php'; // Koneksi ke database

class PDF extends FPDF
{
    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4') // Landscape
    {
        parent::__construct($orientation, $unit, $size);
        $this->SetMargins(15, 15, 15); // Margin kiri, kanan, dan atas
    }

    // Header laporan
    function Header()
    {
        if ($this->PageNo() == 1) {
            $logoWidth = 23;
            $xPosition = 20; // Menentukan posisi X di dekat margin kiri halaman
            $this->Image('../dist/img/logo.jpeg', $xPosition, $this->GetY(), $logoWidth);
            $this->Ln(5); // Memberikan sedikit jarak setelah logo

            $this->SetFont('Arial', 'B', 12); // Menonjolkan kop surat dengan font tebal dan lebih besar
            $this->SetX($xPosition + $logoWidth + 10); // Memberikan jarak cukup antara logo dan teks kop surat
            $this->Cell(0, 5, 'KEPOLISIAN NEGARA REPUBLIK INDONESIA', 0, 1, 'L');
            $this->SetX($xPosition + $logoWidth + 10);
            $this->Cell(0, 5, 'DAERAH KALIMANTAN SELATAN', 0, 1, 'L');
            $this->SetX($xPosition + $logoWidth + 10);
            $this->Cell(0, 5, 'RESOR BARITO KUALA', 0, 1, 'L');

            $this->SetFont('Arial', '', 10); // Mengatur font normal untuk alamat
            $this->SetX($xPosition + $logoWidth + 10); // Menjaga posisi alamat tetap sejajar dengan kop surat
            $this->Cell(0, 5, 'Jl. Gusti M. Seman No. 1 Marabahan 70511', 'B', 1, 'L');
            $this->Ln(10);
        }
    }

    // Body laporan
    function Body($data, $filterMonth, $totalSik)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'LAPORAN ARSIP SURAT IZIN KERAMAIAN', 0, 1, 'C');
        $this->SetX(100); // Margin kiri 30 mm
        $this->Cell(96, 10, '', 'T', 0, 'L');
        $this->Ln(2);
        $this->SetFont('Arial', '', 10);

        // Keterangan bulan
        $this->Ln(5);

        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, "Laporan ini menyajikan jumlah arsip Surat Izin Keramaian (SIK) yang diterbitkan.", 0, 'J');
        $this->Ln(2);


        // Header tabel
        $this->SetFillColor(200, 220, 255);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 8, 'No', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Nomor Arsip', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Nama Instansi', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Penanggung Jawab', 1, 0, 'C', true);
        $this->Cell(65, 8, 'Nama Kegiatan', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Kecamatan', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Tanggal Surat', 1, 1, 'C', true);

        // Isi tabel
        $this->SetFont('Arial', '', 8);
        foreach ($data as $index => $row) {
            $this->Cell(10, 8, $index + 1, 1, 0, 'C');
            $this->Cell(25, 8, $row['id_arsip'], 1, 0, 'C');
            $this->Cell(40, 8, $row['nama_instansi'], 1, 0, 'L');
            $this->Cell(40, 8, $row['penanggung_jawab'], 1, 0, 'L');
            $this->Cell(65, 8, $row['nm_kegiatan'], 1, 0, 'L');
            $this->Cell(40, 8, $row['nm_kecamatan'], 1, 0, 'L');
            $this->Cell(30, 8, date('d-m-Y', strtotime($row['tanggal_surat'])), 1, 1, 'C');
        }

        $this->Cell(22, 8, 'Total SIK Terbit: ' . $totalSik, 0, 1, 'C');


        $this->SetFont('Arial', '', 12);
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
        $this->Cell(16, 4, 'Marabahan', 0, 1, 'L');

        $this->SetX($positionX);
        $this->Cell(
            30,
            4,
            'Pada Tanggal',
            0,
            0,
            'L'
        ); // Teks sebelum ':'
        $this->Cell(50, 6, ':  ' . date('d F Y'), 0, 1, 'L');

        $this->SetX($positionX);
        $this->Cell(80, 0, '', 'T', 0, 'L');
        $this->Ln(2);


        $this->SetX($positionX);
        $this->Cell(50, 3, 'a.n. KAPOLRES BARITO KUALA POLDA KALSEL', 0, 1, 'L');

        $this->SetX($positionX);
        $this->Cell(50, 5, 'KEPALA SATUAN INTELKAM', 0, 1, 'L');
        $this->Ln(15);

        // Ambil nama dan pangkat dari database
        global $koneksi;
        $stmt = $koneksi->prepare("SELECT nama, pangkat FROM kepala ORDER BY id_kepala DESC LIMIT 1");
        $stmt->execute();
        $stmt->bind_result($nama, $pangkat);
        $stmt->fetch();
        $stmt->close();

        if ($nama && $pangkat) {
            $this->Ln(5);
            $this->SetFont('Arial', 'B', 11);
            $this->SetX($positionX);
            $this->Cell(70, 6, $nama, 0, 1, 'L');


            // Garis bawah untuk tanda tangan
            $this->SetX($positionX);
            $this->Cell(80, 0, '', 'T', 0, 'L');
            $this->SetFont('Arial', '', 11);
            $this->SetX($positionX);
            $this->Cell(50, 6, $pangkat, 0, 1, 'L');
        }

        // Mengatur kembali font ke normal jika diperlukan
        $this->SetFont('Arial', '', 11); // '' untuk font normal
        // Menyimpan posisi X dan Y saat ini
        $x = $this->GetX();
        $y = $this->GetY();
    }
}

// Ambil data dari database
$filterMonth = $_GET['filterMonth'] ?? null; // Filter bulan (format: YYYY-MM)
$query = "
    SELECT 
        arsip.id_arsip,
        berkas_pemohon.nama_instansi,
        berkas_pemohon.penanggung_jawab,
        kegiatan.nm_kegiatan,
        kecamatan.nm_kecamatan,
        berkas_pemohon.tanggal_surat
    FROM arsip
    INNER JOIN berkas_pemohon ON arsip.berkas_id = berkas_pemohon.id_berkas
    INNER JOIN kegiatan ON berkas_pemohon.kegiatan_id = kegiatan.id_kegiatan
    INNER JOIN kecamatan ON berkas_pemohon.kecamatan_id = kecamatan.id_kecamatan
";

// Tambahkan filter berdasarkan bulan jika ada
if ($filterMonth) {
    $query .= " WHERE DATE_FORMAT(berkas_pemohon.tanggal_surat, '%Y-%m') = '$filterMonth'";
}

$result = $koneksi->query($query);
$data = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total SIK
$totalSik = count($data);

// Inisialisasi PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->Body($data, $filterMonth, $totalSik);
$pdf->Output();
