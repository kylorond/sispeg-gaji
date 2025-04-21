<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

$id = $_GET['id'];

// Prevent deleting admin account
if ($id == 1) {
    header("Location: index.php?error=Tidak dapat menghapus akun admin utama");
    exit();
}

// Delete query
$query = "DELETE FROM tbl_user WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header("Location: index.php?success=Data pegawai berhasil dihapus");
} else {
    header("Location: index.php?error=Gagal menghapus data");
}
?>