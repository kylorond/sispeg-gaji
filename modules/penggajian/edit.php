<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

// Fetch users from tbl_user
$users_query = "SELECT id, nama FROM tbl_user";
$users_result = mysqli_query($conn, $users_query);

// Get the ID from URL parameter
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: index.php?error=ID penggajian tidak valid");
    exit();
}

// Fetch the existing payroll data
$payroll_query = "SELECT * FROM tbl_penggajian WHERE id = $id";
$payroll_result = mysqli_query($conn, $payroll_query);
$payroll_data = mysqli_fetch_assoc($payroll_result);

if (!$payroll_data) {
    header("Location: index.php?error=Data penggajian tidak ditemukan");
    exit();
}

// Fetch the user ID based on the name in payroll data
$user_query = "SELECT id FROM tbl_user WHERE nama = '".mysqli_real_escape_string($conn, $payroll_data['nama'])."'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$current_user_id = $user_data ? $user_data['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_penggajian = $_POST['kode_penggajian'];
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    $total_gaji = $_POST['total_gaji'];
    
    // Validate inputs
    if (empty($kode_penggajian) || empty($user_id) || empty($status) || empty($total_gaji)) {
        header("Location: edit.php?id=$id&error=Semua field harus diisi");
        exit();
    }
    
    // Get user name from tbl_user
    $user_query = "SELECT nama FROM tbl_user WHERE id = $user_id";
    $user_result = mysqli_query($conn, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);
    $nama = $user_data['nama'];
    
    // Check for duplicate kode_penggajian (excluding current record)
    $check_kode_query = "SELECT id FROM tbl_penggajian 
                        WHERE kode_penggajian = '".mysqli_real_escape_string($conn, $kode_penggajian)."' 
                        AND id != $id";
    $kode_result = mysqli_query($conn, $check_kode_query);
    
    if (mysqli_num_rows($kode_result) > 0) {
        header("Location: edit.php?id=$id&error=Kode penggajian sudah digunakan");
        exit();
    }
    
    // Check for duplicate nama (excluding current record)
    $check_nama_query = "SELECT id FROM tbl_penggajian 
                        WHERE nama = '".mysqli_real_escape_string($conn, $nama)."' 
                        AND id != $id";
    $nama_result = mysqli_query($conn, $check_nama_query);
    
    // if (mysqli_num_rows($nama_result) > 0) {
    //     header("Location: edit.php?id=$id&error=Sudah ada penggajian untuk nama ini");
    //     exit();
    // }
    
    // Update data
    $query = "UPDATE tbl_penggajian 
              SET kode_penggajian = '".mysqli_real_escape_string($conn, $kode_penggajian)."', 
                  nama = '".mysqli_real_escape_string($conn, $nama)."', 
                  status = '".mysqli_real_escape_string($conn, $status)."', 
                  total_gaji = '".mysqli_real_escape_string($conn, $total_gaji)."'
              WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?success=Data penggajian berhasil diperbarui");
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
                    <h4 class="mb-0">Edit Penggajian</h4>
                </div>
                <div class="card-body">
                    <form action="edit.php?id=<?php echo $id; ?>" method="POST" id="penggajianForm">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="kode_penggajian" class="form-label">Kode Penggajian</label>
                            <input type="text" class="form-control" id="kode_penggajian" name="kode_penggajian" 
                                   value="<?php echo htmlspecialchars($payroll_data['kode_penggajian']); ?>" required>
                            <div id="kodeFeedback" class="invalid-feedback">Kode penggajian sudah digunakan</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Nama Pegawai</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Pilih Pegawai</option>
                                <?php 
                                // Reset pointer to beginning of result set
                                mysqli_data_seek($users_result, 0);
                                while ($user = mysqli_fetch_assoc($users_result)): 
                                ?>
                                    <option value="<?php echo $user['id']; ?>" 
                                        <?php echo ($user['id'] == $current_user_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['nama']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <!-- Menghapus feedback untuk duplikat -->
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Belum Dibayar" <?php echo ($payroll_data['status'] == 'Belum Dibayar') ? 'selected' : ''; ?>>Belum Dibayar</option>
                                <option value="Sudah Dibayar" <?php echo ($payroll_data['status'] == 'Sudah Dibayar') ? 'selected' : ''; ?>>Sudah Dibayar</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="total_gaji" class="form-label">Total Gaji</label>
                            <input type="number" class="form-control" id="total_gaji" name="total_gaji" 
                                   value="<?php echo htmlspecialchars($payroll_data['total_gaji']); ?>" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>mbali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side validation for duplicate kode_penggajian
document.getElementById('kode_penggajian').addEventListener('blur', function() {
    const kode = this.value;
    const currentId = <?php echo $id; ?>;
    
    if (kode) {
        fetch(`check_duplicate.php?type=kode&value=${encodeURIComponent(kode)}&current_id=${currentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    this.classList.add('is-invalid');
                    document.getElementById('kodeFeedback').style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    document.getElementById('kodeFeedback').style.display = 'none';
                }
            });
    }
});

// Client-side validation for duplicate nama
document.getElementById('user_id').addEventListener('change', function() {
    const userId = this.value;
    const currentId = <?php echo $id; ?>;
    
    if (userId) {
        fetch(`check_duplicate.php?type=nama&value=${encodeURIComponent(userId)}&current_id=${currentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    this.classList.add('is-invalid');
                    document.getElementById('namaFeedback').style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    document.getElementById('namaFeedback').style.display = 'none';
                }
            });
    }
});

// Form submission validation
document.getElementById('penggajianForm').addEventListener('submit', function(e) {
    const invalidInputs = this.querySelectorAll('.is-invalid');
    if (invalidInputs.length > 0) {
        e.preventDefault();
        alert('Tidak dapat menyimpan karena terdapat data duplikat');
    }
});
</script>

<?php include '../../includes/footer.php'; ?>