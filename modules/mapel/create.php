<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mapel = mysqli_real_escape_string($conn, $_POST['mapel']);

    // Validasi input
    if (empty($mapel)) {
        header("Location: create.php?error=Nama mata pelajaran harus diisi");
        exit();
    }

    // Cek apakah mapel sudah ada
    $check_query = "SELECT id FROM tbl_mapel WHERE mapel = '$mapel'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        header("Location: create.php?error=Mata pelajaran sudah ada");
        exit();
    }

    // Insert data ke database
    $query = "INSERT INTO tbl_mapel (mapel) VALUES ('$mapel')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?success=Data mata pelajaran berhasil ditambahkan");
        exit();
    } else {
        header("Location: create.php?error=Gagal menambahkan data: " . mysqli_error($conn));
        exit();
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tambah Mata Pelajaran Baru</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form action="create.php" method="POST">
                        <div class="mb-3">
                            <label for="mapel" class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" class="form-control" id="mapel" name="mapel" required>
                            <div class="form-text">Masukkan nama mata pelajaran (tidak boleh duplikat)</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>