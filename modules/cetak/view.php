<!-- <?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Query untuk mengambil detail penggajian
$query = "SELECT 
            pg.id as id_penggajian,
            pg.kode_penggajian, 
            pg.status, 
            pg.total_gaji,
            pg.tanggal,
            pg.keterangan,
            u.nama,
            u.nip,
            j.jabatan,
            j.gaji_pokok
          FROM tbl_penggajian pg
          JOIN tbl_user u ON pg.user_id = u.id
          JOIN tbl_jabatan j ON u.jabatan_id = j.id
          WHERE pg.id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: index.php?error=Data tidak ditemukan");
    exit();
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Detail Data Penggajian</h4>
                </div>
                <div class="card-body">
                    <a href="index.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5>Informasi Pegawai</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Nama</th>
                                            <td><?php echo $data['nama']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>NIP</th>
                                            <td><?php echo $data['nip'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jabatan</th>
                                            <td><?php echo $data['jabatan']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Gaji Pokok</th>
                                            <td>Rp <?php echo number_format($data['gaji_pokok'], 0, ',', '.'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5>Detail Penggajian</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Kode Penggajian</th>
                                            <td><?php echo $data['kode_penggajian']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td><?php echo date('d F Y', strtotime($data['tanggal'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td><?php echo $data['status']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Gaji</th>
                                            <td>Rp <?php echo number_format($data['total_gaji'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td><?php echo $data['keterangan'] ?? '-'; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?> -->