<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data mapel yang akan diedit
$query = "SELECT * FROM tbl_mapel WHERE id = $id";
$result = mysqli_query($conn, $query);
$mapel = mysqli_fetch_assoc($result);

if (!$mapel) {
    header("Location: index.php?error=Mata pelajaran tidak ditemukan");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_mapel = mysqli_real_escape_string($conn, $_POST['mapel']);

    // Validasi input
    if (empty($new_mapel)) {
        header("Location: edit.php?id=$id&error=Nama mata pelajaran harus diisi");
        exit();
    }

    // Cek apakah mapel sudah ada (kecuali untuk data yang sedang diedit)
    $check_query = "SELECT id FROM tbl_mapel WHERE mapel = '$new_mapel' AND id != $id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        header("Location: edit.php?id=$id&error=Mata pelajaran sudah ada");
        exit();
    }

    // Update data di database
    $query = "UPDATE tbl_mapel SET mapel = '$new_mapel' WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?success=Mata pelajaran berhasil diperbarui");
        exit();
    } else {
        header("Location: edit.php?id=$id&error=Gagal memperbarui data: " . mysqli_error($conn));
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
                    <h4 class="mb-0">Edit Mata Pelajaran</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="mapel" class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" class="form-control" id="mapel" name="mapel" 
                                   value="<?php echo htmlspecialchars($mapel['mapel']); ?>" required>
                            <div class="form-text">Masukkan nama mata pelajaran (tidak boleh duplikat)</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
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