<?php
define('BASE_URL', 'http://localhost/sispeg-gaji');
$host = "localhost";
$username = "root";
$password = "";
$database = "db_sispeg";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>