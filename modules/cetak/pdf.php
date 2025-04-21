<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

require('../../assets/fpdf/fpdf.php');

// Query with JOIN to tbl_jabatan (same as in index.php)
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
        // Column widths (adjusted to fit within 190mm width, matching the line above)
        // Left and right margins are 10mm each (total 20mm), so table width = 190mm
        $w = array(10, 30, 50, 50, 25, 25); // Total width = 190mm
        
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
            
            $this->Cell($w[0], 6, $no++, 'LR', 0, 'C');
            $this->Cell($w[1], 6, $row['kode_penggajian'], 'LR', 0, 'L');
            $this->Cell($w[2], 6, $row['nama'], 'LR', 0, 'L');
            $this->Cell($w[3], 6, $jabatan, 'LR', 0, 'L');
            $this->Cell($w[4], 6, $row['status'], 'LR', 0, 'C');
            $this->Cell($w[5], 6, $gaji, 'LR', 0, 'R');
            $this->Ln();
        }
        
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// Column headings (removed the empty action column)
$header = array('No', 'Kode Gaji', 'Nama', 'Jabatan', 'Status', 'Total Gaji');

// Create PDF instance
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->ImprovedTable($header, $penggajian);

// Output PDF
$pdf->Output('D', 'Laporan_Penggajian_' . date('Ymd') . '.pdf');
?>