<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: modules/auth/login.php");
    exit();
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Dashboard</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Selamat datang, <?php echo $_SESSION['nama']; ?>!</h5>
                    <p class="card-text">Sistem Informasi Penggajian Pegawai dan Guru SMP Kristen Palangka Raya</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Data Guru & Pegawai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                include 'config/database.php';
                                $query = "SELECT COUNT(*) as total FROM tbl_user";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['total'];
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Data Jabatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $query = "SELECT COUNT(*) as total FROM tbl_jabatan";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['total'];
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-award-fill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Data Penggajian</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $query = "SELECT COUNT(*) as total FROM tbl_penggajian";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['total'];
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>