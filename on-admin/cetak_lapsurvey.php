<?php
require('../fpdf/fpdf.php'); // Pastikan path FPDF benar
include '../include/env.config.php'; // Koneksi database

class PDF extends FPDF
{
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



    // Bagian Body
    function Body($header, $data)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Laporan Survey Kepuasan Pemohon', 0, 1, 'C');
        $middleX = ($this->GetPageWidth() / 2) - 50; // Garis sepanjang 100 mm, jadi titik awal dikurangi setengah panjang
        $this->Line($middleX, $this->GetY(), $middleX + 100, $this->GetY());
        $this->Ln(5); // Tambahkan jarak setelah garis jika diperlukan


        $this->Ln(2);

        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, "Laporan ini menyajikan hasil survei kepuasan Pemohon untuk berbagai layanan yang diberikan oleh Kepolisian Resor Barito Kuala. Hasil survei ini didasarkan pada data yang terkumpul selama tahun tertentu dan diolah menjadi nilai rata-rata per bulan.", 0, 'J');
        $this->Ln(2);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Hasil Survei Kepuasan Pemohon', 0, 1, 'C');
        $this->Ln(2);

        // Lebar kolom tabel
        $w = [60, 60, 60];
        // Header tabel
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C');
        }
        $this->Ln();

        // Isi tabel
        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            $this->Cell($w[0], 8, $row['bulan'], 1, 0, 'L');
            $this->Cell($w[1], 8, $row['jumlah_responden'], 1, 0, 'C');
            $this->Cell($w[2], 8, number_format($row['rata_rata'], 2), 1, 0, 'C');
            $this->Ln();
        }

        $this->Ln(5);
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, "Hasil ini menunjukkan tingkat kepuasan masyarakat terhadap pelayanan yang diberikan. Dengan nilai rata-rata yang tinggi, diharapkan pelayanan Kepolisian dapat terus ditingkatkan agar tetap memenuhi harapan masyarakat.", 0, 'J');
        $this->Ln(5);
        $this->MultiCell(0, 10, "Kami berkomitmen untuk terus mendengarkan masukan dari masyarakat dan menggunakan data survei ini untuk merancang langkah-langkah perbaikan yang diperlukan. Terima kasih atas partisipasi masyarakat dalam memberikan penilaian pada survei ini.", 0, 'J');
    }

    // Footer laporan
    function Footer()
    {
        $this->Ln(10);
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
    }
}

// Ambil data dari database
$filterYear = $_GET['filterYear'] ?? date('Y');
$query = "
    SELECT 
        MONTHNAME(tanggal_survei) AS bulan,
        COUNT(*) AS jumlah_responden,
        ROUND(AVG(
            (CASE pertanyaan1 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END + 
             CASE pertanyaan2 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END + 
             CASE pertanyaan3 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END +
             CASE pertanyaan4 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END +
             CASE pertanyaan5 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END +
             CASE pertanyaan6 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END +
             CASE pertanyaan7 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END +
             CASE pertanyaan8 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END +
             CASE pertanyaan9 
                WHEN 'Sangat Puas' THEN 5 
                WHEN 'Puas' THEN 4 
                WHEN 'Cukup Puas' THEN 3 
                WHEN 'Tidak Puas' THEN 2 
                WHEN 'Sangat Tidak Puas' THEN 1 
                ELSE 0 END) / 9), 2) AS rata_rata
    FROM survey
    WHERE YEAR(tanggal_survei) = ?
    GROUP BY MONTH(tanggal_survei)
    ORDER BY MONTH(tanggal_survei) ASC
";

$stmt = $koneksi->prepare($query);
$stmt->bind_param('i', $filterYear);
$stmt->execute();
$result = $stmt->get_result();
$survey_data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Header tabel
$header = ['Bulan', 'Jumlah Responden', 'Rata-rata Nilai Kepuasan'];

// Buat PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->Body($header, $survey_data);
$pdf->Output();
