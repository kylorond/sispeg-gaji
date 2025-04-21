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

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Data_Pegawai_".date('Ymd').".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Pegawai</title>
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
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td colspan="4" style="text-align: center; font-size: 16pt; font-weight: bold;">DATA PEGAWAI</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">SMP KRISTEN KOTA PALANGKA RAYA</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">Jl. Tambun Bungai No. 15, Langkai Kec. Pahandut,</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">Kota Palangka Raya, Kalimantan Tengah 74874</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
    </table>

    <table class="table-excel">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No. HP</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($users as $row): ?>
            <tr>
                <td class="text-center"><?php echo $no++; ?></td>
                <td class="text-left"><?php echo $row['nama']; ?></td>
                <td class="text-left"><?php echo $row['alamat']; ?></td>
                <td class="text-center"><?php echo $row['nohp']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>