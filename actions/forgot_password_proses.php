<?php
session_start();
include '../config/koneksi.php'; // Hubungkan ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Cek format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setcookie("flash_msg", "Format email tidak valid!", time() + 5, "/");
        header("Location: ../auth/forgot-password.php");
        exit();
    }

    // Cek apakah email terdaftar di database
    $stmt = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        setcookie("flash_msg", "Email tidak terdaftar. Silakan periksa kembali email Anda!", time() + 5, "/");
        header("Location: ../auth/forgot-password.php");
        exit();
    }

    // Validasi kesamaan password
    if ($password !== $confirm_password) {
        setcookie("flash_msg", "Kata sandi baru dan konfirmasi kata sandi tidak cocok!", time() + 5, "/");
        header("Location: ../auth/forgot-password.php");
        exit();
    }

    // Validasi panjang password
    if (strlen($password) < 8) {
        setcookie("flash_msg", "Kata sandi harus minimal 8 karakter!", time() + 5, "/");
        header("Location: ../auth/forgot-password.php");
        exit();
    }

    // Hash password baru
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update password di database
    $stmt_update = $conn->prepare("UPDATE users SET password=? WHERE email=?");
    $stmt_update->bind_param("ss", $hashed_password, $email);

    if ($stmt_update->execute()) {
        setcookie("flash_msg", "Kata sandi berhasil diperbarui! Silakan login menggunakan kata sandi baru Anda.", time() + 5, "/");
        header("Location: ../auth/login.php");
        exit();
    } else {
        setcookie("flash_msg", "Terjadi kesalahan saat memperbarui kata sandi. Silakan coba lagi!", time() + 5, "/");
        header("Location: ../auth/forgot-password.php");
        exit();
    }
} else {
    // Jika diakses langsung tanpa POST
    header("Location: ../auth/forgot-password.php");
    exit();
}
?>
