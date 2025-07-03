<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

require('../../assets/fpdf/fpdf.php');

// Function to format date in Indonesian format
function formatIndonesianDate($date) {
    if (empty($date) || $date == '0000-00-00') return '-';
    
    $monthNames = array(
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $dateParts = explode('-', $date);
    if (count($dateParts) !== 3) return $date;
    
    $day = $dateParts[2];
    $month = $dateParts[1];
    $year = $dateParts[0];
    
    // Remove leading zero from day
    $day = ltrim($day, '0');
    
    return $day . ' ' . $monthNames[(int)$month - 1] . ' ' . $year;
}

// Query with JOIN to tbl_jabatan
$query = "SELECT p.*, j.jabatan 
          FROM tbl_penggajian p
          LEFT JOIN tbl_jabatan j ON p.nama = j.nama";
$result = mysqli_query($conn, $query);
$penggajian = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Create PDF
class PDF extends FPDF {
    function Header() {
        // Company Letterhead
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Laporan Data Penggajian', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 6, 'SMP KRISTEN KOTA PALANGKA RAYA', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Jl. Tambun Bungai No. 15, Langkai Kec. Pahandut,', 0, 1, 'C');
        $this->Cell(0, 5, 'Kota Palangka Raya, Kalimantan Tengah 74874', 0, 1, 'C');
        
        // Add line below letterhead
        $this->SetLineWidth(0.5);
        $this->Line(10, 40, 200, 40);
        $this->SetLineWidth(0.2);
        
        // Report title
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'LAPORAN DATA PENGAJIAN', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        $this->Ln(5);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    function ImprovedTable($header, $data) {
        // Column widths (adjusted to fit within 190mm width)
        // Total width = 190mm (A4 width is 210mm, minus 10mm margins each side)
        $w = array(10, 25, 40, 40, 25, 25, 25); // Adjusted widths to accommodate new column
        
        // Header
        $this->SetFont('Arial', 'B', 10);
        for($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        }
        $this->Ln();
        
        // Data
        $this->SetFont('Arial', '', 10);
        $no = 1;
        foreach($data as $row) {
            $gaji = 'Rp ' . number_format($row['total_gaji'], 0, ',', '.');
            $jabatan = $row['jabatan'] ?? '-';
            $tgl_gajian = formatIndonesianDate($row['tgl_gajian']);
            
            $this->Cell($w[0], 6, $no++, 'LR', 0, 'C');
            $this->Cell($w[1], 6, $row['kode_penggajian'], 'LR', 0, 'L');
            $this->Cell($w[2], 6, $row['nama'], 'LR', 0, 'L');
            $this->Cell($w[3], 6, $jabatan, 'LR', 0, 'L');
            $this->Cell($w[4], 6, $row['status'], 'LR', 0, 'C');
            $this->Cell($w[5], 6, $tgl_gajian, 'LR', 0, 'C');
            $this->Cell($w[6], 6, $gaji, 'LR', 0, 'R');
            $this->Ln();
        }
        
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// Column headings (now includes Tanggal Gajian)
$header = array('No', 'Kode Gaji', 'Nama', 'Jabatan', 'Status', 'Tgl Gajian', 'Total Gaji');

// Create PDF instance
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->ImprovedTable($header, $penggajian);

// Output PDF
$pdf->Output('D', 'Laporan_Penggajian_' . date('Ymd') . '.pdf');
?>