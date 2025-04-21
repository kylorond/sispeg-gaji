<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

// Ambil daftar user dari tbl_user
$users_query = "SELECT id, nama FROM tbl_user ORDER BY nama";
$users_result = mysqli_query($conn, $users_query);

// Ambil data jabatan yang akan diedit
if (isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $edit_query = "SELECT * FROM tbl_jabatan WHERE id = $edit_id";
    $edit_result = mysqli_query($conn, $edit_query);
    
    if (mysqli_num_rows($edit_result) == 0) {
        header("Location: index.php?error=Data jabatan tidak ditemukan");
        exit();
    }
    
    $jabatan_data = mysqli_fetch_assoc($edit_result);
} else {
    header("Location: index.php?error=ID jabatan tidak valid");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    
    // Ambil nama pegawai berdasarkan ID yang dipilih
    $nama_query = "SELECT nama FROM tbl_user WHERE id = $id";
    $nama_result = mysqli_query($conn, $nama_query);
    $user_data = mysqli_fetch_assoc($nama_result);
    $nama = mysqli_real_escape_string($conn, $user_data['nama']);

    // Validasi input
    if (empty($id) || empty($jabatan) || empty($nama)) {
        header("Location: edit.php?id=$edit_id&error=Semua field harus diisi");
        exit();
    }

    // Update data di database
    $query = "UPDATE tbl_jabatan SET id = '$id', nama = '$nama', jabatan = '$jabatan' WHERE id = $edit_id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?success=Data jabatan berhasil diperbarui");
        exit();
    } else {
        header("Location: edit.php?id=$edit_id&error=Gagal memperbarui data: " . mysqli_error($conn));
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
                    <h4 class="mb-0">Edit Jabatan</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form action="edit.php?id=<?php echo $edit_id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="id" class="form-label">Nama Pegawai</label>
                            <select class="form-select" id="id" name="id" required>
                                <option value="">-- Pilih Pegawai --</option>
                                <?php 
                                mysqli_data_seek($users_result, 0);
                                while ($user = mysqli_fetch_assoc($users_result)): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo ($user['id'] == $jabatan_data['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['nama']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" 
                                   value="<?php echo htmlspecialchars($jabatan_data['jabatan']); ?>" required>
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