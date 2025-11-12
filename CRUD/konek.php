<?php
// konek.php - MENGHUBUNGKAN KE DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db = "gunung_berapi";

$conn = mysqli_connect($host, $user, $pass, $db);

if($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset untuk mencegah SQL injection
mysqli_set_charset($conn, "utf8mb4");

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>

