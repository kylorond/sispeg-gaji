<?php
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/sispeg-gaji';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Penggajian Pegawai & Guru SMP Kristen Palangka Raya</title>
    <link rel="icon" href="<?php echo $base_url; ?>/assets/img/logo.jpeg">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="ml-auto">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    SMP Kristen Palangka Raya
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>/modules/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>