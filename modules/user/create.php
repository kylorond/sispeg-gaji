<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5(mysqli_real_escape_string($conn, $_POST['password']));
    
    // Check if username already exists
    $check_username = "SELECT id FROM tbl_user WHERE username = '$username'";
    $username_result = mysqli_query($conn, $check_username);
    
    if (mysqli_num_rows($username_result) > 0) {
        header("Location: create.php?error=Username sudah digunakan");
        exit();
    }
    
    // Check if nama already exists
    $check_nama = "SELECT id FROM tbl_user WHERE nama = '$nama'";
    $nama_result = mysqli_query($conn, $check_nama);
    
    if (mysqli_num_rows($nama_result) > 0) {
        header("Location: create.php?error=Nama sudah terdaftar");
        exit();
    }
    
    // Check if password already exists (hashed)
    $check_password = "SELECT id FROM tbl_user WHERE password = '$password'";
    $password_result = mysqli_query($conn, $check_password);
    
    if (mysqli_num_rows($password_result) > 0) {
        header("Location: create.php?error=Password sudah digunakan oleh user lain");
        exit();
    }
    
    // Insert new user
    $query = "INSERT INTO tbl_user (nama, alamat, nohp, username, password) 
              VALUES ('$nama', '$alamat', '$nohp', '$username', '$password')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?success=Data pegawai berhasil ditambahkan");
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
                    <h4 class="mb-0">Tambah User Baru</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" id="userForm">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                            <div id="namaFeedback" class="invalid-feedback">Nama sudah terdaftar</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nohp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="nohp" name="nohp" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div id="usernameFeedback" class="invalid-feedback">Username sudah digunakan</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div id="passwordFeedback" class="invalid-feedback">Password sudah digunakan</div>
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
// Client-side validation for duplicate nama
document.getElementById('nama').addEventListener('blur', function() {
    const nama = this.value;
    if (nama) {
        fetch('check_duplicate_user.php?type=nama&value=' + encodeURIComponent(nama))
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

// Client-side validation for duplicate username
document.getElementById('username').addEventListener('blur', function() {
    const username = this.value;
    if (username) {
        fetch('check_duplicate_user.php?type=username&value=' + encodeURIComponent(username))
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    this.classList.add('is-invalid');
                    document.getElementById('usernameFeedback').style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    document.getElementById('usernameFeedback').style.display = 'none';
                }
            });
    }
});

// Client-side validation for duplicate password
document.getElementById('password').addEventListener('blur', function() {
    const password = this.value;
    if (password) {
        // Hash the password with MD5 (same as server-side)
        const hashedPassword = md5(password);
        fetch('check_duplicate_user.php?type=password&value=' + encodeURIComponent(hashedPassword))
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    this.classList.add('is-invalid');
                    document.getElementById('passwordFeedback').style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    document.getElementById('passwordFeedback').style.display = 'none';
                }
            });
    }
});

// Simple MD5 function for client-side hashing
function md5(string) {
    // This is a simplified version - in production, use a proper MD5 library
    // For demo purposes only
    return CryptoJS.MD5(string).toString();
}

// Form submission validation
document.getElementById('userForm').addEventListener('submit', function(e) {
    const invalidInputs = this.querySelectorAll('.is-invalid');
    if (invalidInputs.length > 0) {
        e.preventDefault();
        alert('Tidak dapat menyimpan karena terdapat data duplikat');
    }
});
</script>

<!-- Include CryptoJS for MD5 hashing -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<?php include '../../includes/footer.php'; ?>