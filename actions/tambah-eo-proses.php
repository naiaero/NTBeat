<?php
session_start();
include '../config/koneksi.php';

// Verifikasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat'];

    // Cek apakah email sudah terdaftar
    $cek_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $cek_email->bind_param("s", $email);
    $cek_email->execute();
    $res_email = $cek_email->get_result();

    if ($res_email->num_rows > 0) {
        setcookie("flash_msg", "Gagal: Email penyelenggara sudah terdaftar sebelumnya!", time() + 5, "/");
        header("Location: ../admin/kelola-eo.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'eo';
    $foto_default = 'default-avatar.png';

    // Masukkan ke tabel users
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role, alamat, foto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $email, $hashed_password, $role, $alamat, $foto_default);

    if ($stmt->execute()) {
        setcookie("flash_msg", "Akun Event Organizer (EO) berhasil ditambahkan!", time() + 5, "/");
    } else {
        setcookie("flash_msg", "Terjadi kesalahan sistem saat menambah EO.", time() + 5, "/");
    }

    header("Location: ../admin/kelola-eo.php");
    exit();
} else {
    header("Location: ../admin/dashboard.php");
    exit();
}
?>
