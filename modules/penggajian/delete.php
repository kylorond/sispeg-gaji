<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

// Check if ID parameter exists
if (!isset($_GET['id'])) {
    header("Location: index.php?error=ID tidak valid");
    exit();
}

$id = $_GET['id'];

// Delete data
$query = "DELETE FROM tbl_penggajian WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header("Location: index.php?success=Data penggajian berhasil dihapus");
} else {
    header("Location: index.php?error=Gagal menghapus data: " . mysqli_error($conn));
}

exit();
?>