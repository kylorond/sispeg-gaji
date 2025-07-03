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
    $tgl_gajian = $_POST['tgl_gajian'];
    
    // Validate inputs
    if (empty($kode_penggajian) || empty($user_id) || empty($status) || empty($total_gaji)) {
        header("Location: edit.php?id=$id&error=Semua field wajib harus diisi");
        exit();
    }
    
    // Additional validation for date when status is "Sudah Dibayar"
    if ($status === 'Sudah Dibayar' && empty($tgl_gajian)) {
        header("Location: edit.php?id=$id&error=Tanggal gajian wajib diisi ketika status Sudah Dibayar");
        exit();
    }
    
    // Validate date not in the future
    if (!empty($tgl_gajian)) {
        $today = date('Y-m-d');
        if ($tgl_gajian > $today) {
            header("Location: edit.php?id=$id&error=Tanggal gajian tidak boleh melebihi hari ini");
            exit();
        }
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
    
    // Update data - include tgl_gajian in the query
    $query = "UPDATE tbl_penggajian 
              SET kode_penggajian = '".mysqli_real_escape_string($conn, $kode_penggajian)."', 
                  nama = '".mysqli_real_escape_string($conn, $nama)."', 
                  status = '".mysqli_real_escape_string($conn, $status)."', 
                  total_gaji = '".mysqli_real_escape_string($conn, $total_gaji)."',
                  tgl_gajian = ".($status === 'Sudah Dibayar' ? "'".mysqli_real_escape_string($conn, $tgl_gajian)."'" : "NULL")."
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
                            <label for="tgl_gajian" class="form-label">Tanggal Gajian</label>
                            <input type="date" class="form-control" id="tgl_gajian" name="tgl_gajian" 
                                   value="<?php echo $payroll_data['status'] == 'Sudah Dibayar' ? htmlspecialchars($payroll_data['tgl_gajian']) : ''; ?>" 
                                   max="<?php echo date('Y-m-d'); ?>">
                            <div id="dateFeedback" class="invalid-feedback">Tanggal gajian wajib diisi ketika status Sudah Dibayar dan tidak boleh melebihi hari ini</div>
                        </div>

                        <div class="mb-3">
                            <label for="total_gaji" class="form-label">Total Gaji</label>
                            <input type="number" class="form-control" id="total_gaji" name="total_gaji" 
                                   value="<?php echo htmlspecialchars($payroll_data['total_gaji']); ?>" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
<<<<<<< HEAD
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
=======
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
>>>>>>> 61115a8a539878391d3bee564fe51f920711aa00
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

// Handle date field based on status selection
const statusSelect = document.getElementById('status');
const dateInput = document.getElementById('tgl_gajian');
const today = new Date().toISOString().split('T')[0];

// Set max date to today
dateInput.max = today;

// Initialize field state based on current status
if (statusSelect.value === 'Sudah Dibayar') {
    dateInput.required = true;
    dateInput.disabled = false;
} else {
    dateInput.required = false;
    dateInput.disabled = true;
}

statusSelect.addEventListener('change', function() {
    if (this.value === 'Sudah Dibayar') {
        dateInput.required = true;
        dateInput.disabled = false;
    } else {
        dateInput.required = false;
        dateInput.disabled = true;
        dateInput.value = '';
    }
});

// Form submission validation
document.getElementById('penggajianForm').addEventListener('submit', function(e) {
    const invalidKode = document.getElementById('kode_penggajian').classList.contains('is-invalid');
    const status = document.getElementById('status').value;
    const dateValue = document.getElementById('tgl_gajian').value;
    const today = new Date().toISOString().split('T')[0];
    
    let isValid = true;
    
    // Check for duplicate kode
    if (invalidKode) {
        isValid = false;
        alert('Tidak dapat menyimpan karena Kode Penggajian sudah digunakan');
    }
    
    // Check if date is required and valid
    if (status === 'Sudah Dibayar') {
        if (!dateValue) {
            document.getElementById('tgl_gajian').classList.add('is-invalid');
            document.getElementById('dateFeedback').style.display = 'block';
            isValid = false;
        } else if (dateValue > today) {
            document.getElementById('tgl_gajian').classList.add('is-invalid');
            document.getElementById('dateFeedback').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('tgl_gajian').classList.remove('is-invalid');
            document.getElementById('dateFeedback').style.display = 'none';
        }
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
