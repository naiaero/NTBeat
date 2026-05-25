<?php
$host     = "localhost";
$user     = "root"; // Username bawaan XAMPP
$password = "";     // Password bawaan XAMPP (kosong)
$db       = "ntbeat"; // Nama database 

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Atur timezone sesuai wilayah NTB (WITA - Asia/Makassar)
date_default_timezone_set('Asia/Makassar');

// Auto-update status konser menjadi 'Selesai' jika tanggal/waktu sudah terlewati
$tanggal_sekarang = date('Y-m-d');
$waktu_sekarang = date('H:i:s');
$update_sql = "UPDATE konser 
               SET status = 'Selesai' 
               WHERE status NOT IN ('Arsip', 'Selesai') 
                 AND (tanggal < '$tanggal_sekarang' OR (tanggal = '$tanggal_sekarang' AND waktu < '$waktu_sekarang'))";
mysqli_query($conn, $update_sql);
?>