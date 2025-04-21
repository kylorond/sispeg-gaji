<?php
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/sispeg-gaji';
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin';

// Get the current page URL
$current_url = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar">
    <div class="sidebar-header text-center">
        <img src="<?php echo $base_url; ?>/assets/img/logo.jpeg" alt="Logo" class="logo">
        <h3>PENGGAJIAN</h3>
        <div class="user-info mt-2">
            <small class="text-muted">Logged in as</small>
            <h6><?php echo htmlspecialchars($nama); ?></h6>
        </div>
    </div>
    <ul class="list-unstyled components">
        <li class="<?php echo strpos($current_url, 'sispeg-gaji/index.php') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>/index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>
        <li class="<?php echo strpos($current_url, '/modules/user/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>/modules/user/index.php"><i class="bi bi-people-fill"></i> Data Pegawai</a>
        </li>
        <li class="<?php echo strpos($current_url, '/modules/jabatan/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>/modules/jabatan/index.php"><i class="bi bi-award-fill"></i> Data Jabatan</a>
        </li>
        <li class="<?php echo strpos($current_url, '/modules/mapel/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>/modules/mapel/index.php"><i class="bi bi-book-fill"></i> Data Mapel</a>
        </li>
        <li class="<?php echo strpos($current_url, '/modules/penggajian/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>/modules/penggajian/index.php"><i class="bi bi-cash-stack"></i> Data Penggajian</a>
        </li>
        <li class="<?php echo strpos($current_url, '/modules/cetak/') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $base_url; ?>/modules/cetak/index.php"><i class="bi bi-file-earmark-text"></i> Laporan</a>
        </li>
    </ul>
    
    <div class="sidebar-footer p-3">
        <a href="<?php echo $base_url; ?>/modules/auth/logout.php" class="btn btn-danger btn-sm w-100">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>