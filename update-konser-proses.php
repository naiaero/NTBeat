<?php
include 'koneksi.php';

if (isset($_POST['submit_update'])) {
    // 1. Ambil data dari form input
    $id = $_POST['id'];
    $nama_konser = $_POST['nama_konser'];
    // ... ambil data lainnya

    // 2. Jalankan query update ke database
    $query = "UPDATE konser SET nama_konser = '$nama_konser' WHERE id = '$id'";
    $hasil = mysqli_query($koneksi, $query);

    // 3. DI SINI KUNCINYA: Jika berhasil, baru arahkan kembali ke halaman kelola konser
    if ($hasil) {
        header("Location: admin-kelola-konser.php");
        exit(); // Selalu gunakan exit setelah header redirect
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}
?>