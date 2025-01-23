<?php
require('../fpdf/fpdf.php'); // Pastikan path FPDF benar
include '../include/env.config.php'; // Koneksi ke database

class PDF extends FPDF
{
    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->SetMargins(21, 21, 21); // Margin kiri, kanan, dan atas
    }

    // Header laporan
    function Header()
    {
        if ($this->PageNo() == 1) {
            $this->SetFont('Arial', '', 11);
            $this->Cell(85, 5, 'KEPOLISIAN NEGARA REPUBLIK INDONESIA', 0, 1, 'C');
            $this->Cell(85, 5, 'DAERAH KALIMANTAN SELATAN', 0, 1, 'C');
            $this->Cell(85, 5, 'RESOR BARITO KUALA', 0, 1, 'C');
            $this->SetFont('Arial', '', 10);
            $this->Cell(85, 5, 'Jl. Gusti M. Seman No. 1 Marabahan 70511', 'B', 1, 'C');
            $this->Ln(5);

            $logoWidth = 23;
            $xPosition = ($this->GetPageWidth() - $logoWidth) / 2;
            $this->Image('../dist/img/logo.jpeg', $xPosition, $this->GetY(), $logoWidth);
            $this->Ln(25);
        }
    }

    // Body laporan
    function Body($data, $total, $filterMonth)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'LAPORAN JUMLAH SIK BERDASARKAN JENIS KEGIATAN', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 0, '', 'T', 0, 'L');

        $this->Ln(5);

        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, "Laporan ini menyajikan jumlah Surat Izin Keramaian (SIK) yang diterbitkan berdasarkan Jenis Kegiatan.", 0, 'J');
        $this->Ln(2);

        // Header tabel
        $this->SetFillColor(200, 220, 255);
        $this->Cell(10, 10, 'No', 1, 0, 'C', true);
        $this->Cell(90, 10, 'Kegiatan', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Jumlah SIK', 1, 1, 'C', true);

        // Isi tabel
        $this->SetFont('Arial', '', 10);
        foreach ($data as $index => $row) {
            $this->Cell(10, 10, $index + 1, 1, 0, 'C');
            $this->Cell(90, 10, $row['nm_kegiatan'], 1, 0, 'L');
            $this->Cell(40, 10, $row['jumlah_sik'], 1, 1, 'C');
        }

        // Total jumlah SIK
        $this->Cell(100, 10, 'Total', 1, 0, 'R');
        $this->Cell(40, 10, $total, 1, 1, 'C');

                // Tambahkan bagian footer ke body
                $this->Ln(50); // Jarak dari tabel ke bagian footer
                $this->SetFont('Arial', '', 11);
        
                // Bagian kanan bawah untuk tanggal dan tanda tangan
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
                $this->Cell(50, 6, 'a.n. KAPOLRES BARITO KUALA POLDA KALSEL', 0, 1, 'L');
                
                $this->SetX($positionX);
                $this->Cell(50, 6, 'KEPALA SATUAN INTELKAM', 0, 1, 'L');
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
        
                
        
                 // Garis bawah untuk tanda tangan
        
                // Bagian kiri bawah untuk tembusan
                $this->SetY(-50); // Posisi vertikal untuk tembusan
                $this->SetFont('Arial', '', 11);
                $this->SetX(21);
                $this->Cell(0, 6, 'Tembusan :', 0, 1, 'L');
                $this->SetX(21);
                $this->Cell(0, 6, '1. Kapolres Barito Kuala', 0, 1, 'L');
                $this->SetX(21);
                $this->Cell(0, 6, '2. Kabagops Polres Barito Kuala', 0, 1, 'L');
                $this->SetX(21);
                $this->Cell(0, 6, '3. Kapolsek Marabahan Kota', 0, 1, 'L');
        
                // Garis bawah untuk tembusan
                $this->SetX(21);
                $this->Cell(62, 0, '', 'B', 0, 'L'); // Garis bawah untuk tembusan
    }
}

// Ambil data dari database
$filterMonth = $_GET['filterMonth'] ?? null;
$query = "
    SELECT kegiatan.nm_kegiatan, COUNT(persyaratan.id_syarat) AS jumlah_sik
    FROM persyaratan
    INNER JOIN berkas_pemohon ON persyaratan.berkas_id = berkas_pemohon.id_berkas
    INNER JOIN kegiatan ON berkas_pemohon.kegiatan_id = kegiatan.id_kegiatan
";

if ($filterMonth) {
    $query .= " WHERE DATE_FORMAT(persyaratan.tanggal_terbit, '%Y-%m') = '$filterMonth'";
}
$query .= " GROUP BY kegiatan.nm_kegiatan";

$result = $koneksi->query($query);
$data = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total SIK
$total = 0;
foreach ($data as $row) {
    $total += $row['jumlah_sik'];
}

// Inisialisasi PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->Body($data, $total, $filterMonth);
$pdf->Output();
