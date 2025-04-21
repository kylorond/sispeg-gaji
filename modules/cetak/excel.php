<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

// Query with JOIN to tbl_jabatan
$query = "SELECT p.*, j.jabatan 
          FROM tbl_penggajian p
          LEFT JOIN tbl_jabatan j ON p.nama = j.nama";
$result = mysqli_query($conn, $query);
$penggajian = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penggajian_".date('Ymd').".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penggajian</title>
    <style>
        .table-excel {
            border-collapse: collapse;
            width: 100%;
        }
        .table-excel th, .table-excel td {
            border: 1px solid #000;
            padding: 5px;
        }
        .table-excel th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td colspan="7" style="text-align: center; font-size: 16pt; font-weight: bold;">LAPORAN DATA PENGAJIAN</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">SMP KRISTEN KOTA PALANGKA RAYA</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">Jl. Tambun Bungai No. 15, Langkai Kec. Pahandut,</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">Kota Palangka Raya, Kalimantan Tengah 74874</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr>
            <td colspan="7">&nbsp;</td>
        </tr>
    </table>

    <table class="table-excel">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Gaji</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Status</th>
                <th>Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($penggajian as $row): ?>
            <tr>
                <td class="text-center"><?php echo $no++; ?></td>
                <td><?php echo $row['kode_penggajian']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['jabatan'] ?? '-'; ?></td>
                <td class="text-center"><?php echo $row['status']; ?></td>
                <td class="text-right"><?php echo 'Rp ' . number_format($row['total_gaji'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>