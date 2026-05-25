<?php
session_start();
include '../config/koneksi.php'; // Hubungkan ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Cek format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Format email tidak valid!');
                window.location.href='../auth/forgot-password.php';
              </script>";
        exit();
    }

    // Cek apakah email terdaftar di database
    $query = "SELECT email FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        echo "<script>
                alert('Email tidak terdaftar. Silakan periksa kembali email Anda!');
                window.location.href='../auth/forgot-password.php';
              </script>";
        exit();
    }

    // Validasi kesamaan password
    if ($password !== $confirm_password) {
        echo "<script>
                alert('Kata sandi baru dan konfirmasi kata sandi tidak cocok!');
                window.location.href='../auth/forgot-password.php';
              </script>";
        exit();
    }

    // Validasi panjang password
    if (strlen($password) < 8) {
        echo "<script>
                alert('Kata sandi harus minimal 8 karakter!');
                window.location.href='../auth/forgot-password.php';
              </script>";
        exit();
    }

    // Hash password baru
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update password di database
    $update_query = "UPDATE users SET password='$hashed_password' WHERE email='$email'";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>
                alert('Kata sandi berhasil diperbarui! Silakan login menggunakan kata sandi baru Anda.');
                window.location.href='../auth/login.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Terjadi kesalahan saat memperbarui kata sandi. Silakan coba lagi!');
                window.location.href='../auth/forgot-password.php';
              </script>";
        exit();
    }
} else {
    // Jika diakses langsung tanpa POST
    header("Location: ../auth/forgot-password.php");
    exit();
}
?>
