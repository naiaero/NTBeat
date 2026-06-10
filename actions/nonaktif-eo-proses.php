<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['email']) && isset($_GET['aksi'])) {
    $email_eo = $_GET['email'];
    $aksi = $_GET['aksi'];
    
    $status_baru = ($aksi == 'nonaktifkan') ? 'nonaktif' : 'aktif';
    $msg = ($aksi == 'nonaktifkan') ? "Akun EO berhasil dinonaktifkan!" : "Akun EO berhasil diaktifkan kembali!";
    
    $stmt = $conn->prepare("UPDATE users SET status=? WHERE email=? AND role='eo'");
    $stmt->bind_param("ss", $status_baru, $email_eo);
    if ($stmt->execute()) {
        setcookie("flash_msg", $msg, time() + 5, "/");
    } else {
        setcookie("flash_msg", "Gagal merubah status EO.", time() + 5, "/");
    }
}
header("Location: ../admin/kelola-eo.php");
exit();
?>
