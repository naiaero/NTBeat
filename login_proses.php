<?php
session_start();
include 'koneksi.php'; // Memanggil jembatan database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menangkap email dan password yang diketik user
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Mengubah password jadi kode rahasia (MD5) biar cocok sama database
    $password_md5 = md5($password); 

    // Mencari adakah user dengan email & password tersebut?
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password_md5'";
    $result = mysqli_query($conn, $query);

    // Kalau datanya ketemu
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Simpan data di "ingatan" browser (session)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];

        // Cek dia Admin atau User biasa?
        if ($user['role'] == 'admin') {
            echo "<script>
                    alert('Selamat datang, Administrator!'); 
                    window.location.href='admin-dashboard.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Login Berhasil! Selamat datang di NTBeat.'); 
                    window.location.href='halaman-user.php'; 
                  </script>";
        }
    } else {
        // Kalau datanya nggak ketemu (email/password salah)
        echo "<script>
                alert('Email atau Kata Sandi salah. Silakan coba lagi!'); 
                window.location.href='login.php';
              </script>";
    }
}
?>