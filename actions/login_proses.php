<?php
session_start();
include '../config/koneksi.php'; // Memanggil jembatan database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menangkap email dan password yang diketik user
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $hashed_password = $_POST['password'];

    // Mencari adakah user dengan email
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);


    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if(password_verify($hashed_password, $user['password'])) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] == 'admin') {
                echo "<script>
                        alert('Selamat datang, Administrator!'); 
                        window.location.href='../admin/dashboard.php';
                    </script>";
            } else if ($user['role'] == 'customer') {
                echo "<script>
                        alert('Login Berhasil! Selamat datang di NTBeat.'); 
                        window.location.href='../user/beranda.php';
                    </script>";
            }
        } else {
            echo "<script>
                    alert('Email atau Kata Sandi salah. Silakan coba lagi!'); 
                    window.location.href='../auth/login.php';
                  </script>";
        }
    } else {
        // Kalau datanya nggak ketemu (email/password salah)
            echo "<script>
                    alert('Email atau Kata Sandi salah. Silakan coba lagi!'); 
                    window.location.href='../auth/login.php';
                </script>";
    }
}
?>