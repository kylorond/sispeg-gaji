<?php
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/sispeg-gaji';
session_start();
include '../../config/database.php';

if (isset($_SESSION['username'])) {
    header("Location: ../../index.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM tbl_user WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['username'] = $user['username'];
        
        header("Location: ../../index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - Sistem Informasi Penggajian Pegawai & Guru SMP Kristen Palangka Raya</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 15px;
            margin: auto;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 15px;
        }
        .title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="login-container">
        <div class="logo-container">
            <!-- Replace with your actual logo path -->
            <img src="<?php echo $base_url; ?>/assets/img/logo.jpeg" alt="Logo" class="logo">
            <h5 class="title">Sistem Informasi Penggajian Pegawai & Guru</h5>
            <p class="subtitle">SMP Kristen Palangka Raya</p>
        </div>
        
        <div class="card shadow">
            <div class="card-body p-4">
                <h4 class="mb-4 text-center">Sign in</h4>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Log in</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>