<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

// Get user data
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT * FROM tbl_user WHERE id = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header("Location: index.php?error=User tidak ditemukan");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = !empty($_POST['password']) ? md5(mysqli_real_escape_string($conn, $_POST['password'])) : null;

    // Check for duplicate nama (excluding current user)
    $check_nama = "SELECT id FROM tbl_user WHERE nama = '$nama' AND id != $id";
    $nama_result = mysqli_query($conn, $check_nama);
    
    if (mysqli_num_rows($nama_result) > 0) {
        header("Location: edit.php?id=$id&error=Nama sudah digunakan oleh user lain");
        exit();
    }

    // Check for duplicate username (excluding current user)
    $check_username = "SELECT id FROM tbl_user WHERE username = '$username' AND id != $id";
    $username_result = mysqli_query($conn, $check_username);
    
    if (mysqli_num_rows($username_result) > 0) {
        header("Location: edit.php?id=$id&error=Username sudah digunakan");
        exit();
    }

    // Check for duplicate password if provided (excluding current user)
    if ($password) {
        $check_password = "SELECT id FROM tbl_user WHERE password = '$password' AND id != $id";
        $password_result = mysqli_query($conn, $check_password);
        
        if (mysqli_num_rows($password_result) > 0) {
            header("Location: edit.php?id=$id&error=Password sudah digunakan oleh user lain");
            exit();
        }
    }

    // Update query
    $query = "UPDATE tbl_user SET 
              nama = '$nama',
              alamat = '$alamat',
              nohp = '$nohp',
              username = '$username'";
    
    // Add password update if provided
    if ($password) {
        $query .= ", password = '$password'";
    }
    
    $query .= " WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?success=Data pegawai berhasil diperbarui");
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
                    <h4 class="mb-0">Edit User</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" id="userForm">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                            <div id="namaFeedback" class="invalid-feedback">Nama sudah digunakan</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" 
                                      rows="3" required><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nohp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="nohp" name="nohp" 
                                   value="<?php echo htmlspecialchars($user['nohp']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                            <div id="usernameFeedback" class="invalid-feedback">Username sudah digunakan</div>
                        </div>
                        
                        <!-- <div class="mb-3">
                            <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div id="passwordFeedback" class="invalid-feedback">Password sudah digunakan</div>
                        </div> -->
                        
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

<script>
// Client-side validation for duplicate nama
document.getElementById('nama').addEventListener('blur', function() {
    const nama = this.value;
    const currentId = <?php echo $id; ?>;
    
    if (nama) {
        fetch(`check_duplicate_user.php?type=nama&value=${encodeURIComponent(nama)}&current_id=${currentId}`)
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
    const currentId = <?php echo $id; ?>;
    
    if (username) {
        fetch(`check_duplicate_user.php?type=username&value=${encodeURIComponent(username)}&current_id=${currentId}`)
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
    const currentId = <?php echo $id; ?>;
    
    if (password) {
        // Hash the password with MD5 (same as server-side)
        const hashedPassword = md5(password);
        fetch(`check_duplicate_user.php?type=password&value=${encodeURIComponent(hashedPassword)}&current_id=${currentId}`)
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