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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_penggajian = $_POST['kode_penggajian'];
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    $total_gaji = $_POST['total_gaji'];
    
    // Validate inputs
    if (empty($kode_penggajian) || empty($user_id) || empty($status) || empty($total_gaji)) {
        header("Location: create.php?error=Semua field harus diisi");
        exit();
    }
    
    // Get user name from tbl_user
    $user_query = "SELECT nama FROM tbl_user WHERE id = $user_id";
    $user_result = mysqli_query($conn, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);
    $nama = $user_data['nama'];
    
    // Check for duplicate kode_penggajian only
    $check_kode_query = "SELECT id FROM tbl_penggajian WHERE kode_penggajian = '".mysqli_real_escape_string($conn, $kode_penggajian)."'";
    $kode_result = mysqli_query($conn, $check_kode_query);
    
    if (mysqli_num_rows($kode_result) > 0) {
        header("Location: create.php?error=Kode penggajian sudah digunakan");
        exit();
    }
    
    // Insert data
    $query = "INSERT INTO tbl_penggajian (kode_penggajian, nama, status, total_gaji) 
              VALUES ('".mysqli_real_escape_string($conn, $kode_penggajian)."', 
                     '".mysqli_real_escape_string($conn, $nama)."', 
                     '".mysqli_real_escape_string($conn, $status)."', 
                     '".mysqli_real_escape_string($conn, $total_gaji)."')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?success=Data penggajian berhasil ditambahkan");
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
                    <h4 class="mb-0">Tambah Penggajian Baru</h4>
                </div>
                <div class="card-body">
                    <form action="create.php" method="POST" id="penggajianForm">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_GET['error']; ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="kode_penggajian" class="form-label">Kode Penggajian</label>
                            <input type="text" class="form-control" id="kode_penggajian" name="kode_penggajian" required>
                            <div id="kodeFeedback" class="invalid-feedback">Kode penggajian sudah digunakan</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Nama Pegawai</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Pilih Pegawai</option>
                                <?php 
                                // Reset pointer to beginning of result set if needed
                                if (mysqli_num_rows($users_result) > 0) {
                                    mysqli_data_seek($users_result, 0);
                                }
                                while ($user = mysqli_fetch_assoc($users_result)): 
                                ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['nama']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Belum Dibayar">Belum Dibayar</option>
                                <option value="Sudah Dibayar">Sudah Dibayar</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="total_gaji" class="form-label">Total Gaji</label>
                            <input type="number" class="form-control" id="total_gaji" name="total_gaji" required>
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

<script>
// Client-side validation for duplicate kode_penggajian only
document.getElementById('kode_penggajian').addEventListener('blur', function() {
    const kode = this.value;
    if (kode) {
        fetch('check_duplicate.php?type=kode&value=' + encodeURIComponent(kode))
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

// Form submission validation - only check for duplicate kode
document.getElementById('penggajianForm').addEventListener('submit', function(e) {
    const invalidKode = document.getElementById('kode_penggajian').classList.contains('is-invalid');
    if (invalidKode) {
        e.preventDefault();
        alert('Tidak dapat menyimpan karena Kode Penggajian sudah digunakan');
    }
});
</script>

<?php include '../../includes/footer.php'; ?>