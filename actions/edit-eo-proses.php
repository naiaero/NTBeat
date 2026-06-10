<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_lama = $_POST['email_lama'];
    $nama = $_POST['nama'];
    $email_baru = $_POST['email'];
    $alamat = $_POST['alamat'];
    $password = $_POST['password'];

    // Cek apakah email_baru beda dan sudah terdaftar
    if ($email_baru !== $email_lama) {
        $cek_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $cek_email->bind_param("s", $email_baru);
        $cek_email->execute();
        if ($cek_email->get_result()->num_rows > 0) {
            setcookie("flash_msg", "Gagal: Email baru tersebut sudah terdaftar pada pengguna lain!", time() + 5, "/");
            header("Location: ../admin/kelola-eo.php");
            exit();
        }
    }

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, alamat=?, password=? WHERE email=?");
        $stmt->bind_param("sssss", $nama, $email_baru, $alamat, $hashed, $email_lama);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, alamat=? WHERE email=?");
        $stmt->bind_param("ssss", $nama, $email_baru, $alamat, $email_lama);
    }

    if ($stmt->execute()) {
        setcookie("flash_msg", "Data Event Organizer berhasil diperbarui!", time() + 5, "/");
    } else {
        setcookie("flash_msg", "Terjadi kesalahan saat menyimpan perubahan data.", time() + 5, "/");
    }
    
    header("Location: ../admin/kelola-eo.php");
    exit();
}
?>
