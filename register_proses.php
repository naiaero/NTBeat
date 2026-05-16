<?php
session_start();
include 'koneksi.php'; // Sambungkan ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Cek apakah email sudah pernah dipakai daftar sebelumnya
    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>
                alert('Email ini sudah terdaftar! Silakan gunakan email lain atau langsung Login.');
                window.location.href='register.php';
              </script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Masukkan data user baru ke database (Otomatis role-nya jadi 'user')
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashed_password', 'user')";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Pendaftaran berhasil! Silakan Login menggunakan akun barumu.');
                    window.location.href='login.php';
                  </script>";
        } else {
            echo "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}
?>