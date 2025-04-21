<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

$query = "SELECT * FROM tbl_user";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

require('../../assets/fpdf/fpdf.php');

class PDF extends FPDF {
    function Header() {
        // Company Letterhead
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Data Pegawai', 0, 1, 'C');
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
        $this->Cell(0, 10, 'DATA PEGAWAI', 0, 1, 'C');
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
        // Adjusted column widths (total width = 190mm)
        $w = array(15, 70, 80, 25); // No, Nama, Alamat, No. HP
        
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
            $this->Cell($w[0], 6, $no++, 'LR', 0, 'C');
            $this->Cell($w[1], 6, $row['nama'], 'LR', 0, 'L');
            $this->Cell($w[2], 6, $row['alamat'], 'LR', 0, 'L');
            $this->Cell($w[3], 6, $row['nohp'], 'LR', 0, 'C');
            $this->Ln();
        }
        
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// Column headings (without empty column)
$header = array('No', 'Nama', 'Alamat', 'No. HP');

// Create PDF instance
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->ImprovedTable($header, $users);

// Output PDF
$pdf->Output('D', 'Data_Pegawai_' . date('Ymd') . '.pdf');
?>