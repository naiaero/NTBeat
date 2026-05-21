<?php
$host     = "localhost";
$user     = "root"; // Username bawaan XAMPP
$password = "";     // Password bawaan XAMPP (kosong)
$db       = "ntbeat"; // Nama database yang tadi kamu buat

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>