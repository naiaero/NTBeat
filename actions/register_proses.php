<?php
session_start();
include '../config/koneksi.php'; // Sambungkan ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setcookie("flash_msg", "Format email tidak valid!", time() + 5, "/");
        header("Location: ../auth/register.php");
        exit(); // Hentikan script agar tidak lanjut mengecek ke database
    }

    // Cek apakah email sudah pernah dipakai daftar sebelumnya
    $stmt_cek = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt_cek->bind_param("s", $email);
    $stmt_cek->execute();
    $cek_email = $stmt_cek->get_result();
    
    if (mysqli_num_rows($cek_email) > 0) {
        setcookie("flash_msg", "Email ini sudah terdaftar! Silakan gunakan email lain atau langsung Login.", time() + 5, "/");
        header("Location: ../auth/register.php");
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';

        // Masukkan data user baru ke database (Otomatis role-nya jadi 'customer')
        $stmt_insert = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $nama, $email, $hashed_password, $role);

        if ($stmt_insert->execute()) {
            setcookie("flash_msg", "Pendaftaran berhasil! Silakan Login menggunakan akun barumu.", time() + 5, "/");
            header("Location: ../auth/login.php");
        } else {
            setcookie("flash_msg", "Gagal menyimpan data: " . mysqli_error($conn), time() + 5, "/");
            header("Location: ../auth/register.php");
        }
    }
}
?>