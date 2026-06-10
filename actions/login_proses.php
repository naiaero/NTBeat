<?php
session_start();
include '../config/koneksi.php'; // Memanggil jembatan database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menangkap email dan password yang diketik user
    $email = trim($_POST['email']);
    $hashed_password = $_POST['password'];

    // Mencari adakah user dengan email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();


    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if(password_verify($hashed_password, $user['password'])) {
            // Cek status aktif/nonaktif
            if (isset($user['status']) && $user['status'] == 'nonaktif') {
                setcookie("flash_msg", "Akses Ditolak: Akun Anda sedang dinonaktifkan oleh Administrator.", time() + 5, "/");
                header("Location: ../auth/login.php");
                exit();
            }
            $_SESSION['email'] = $user['email'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] == 'admin') {
                setcookie("flash_msg", "Selamat datang, Administrator!", time() + 5, "/");
                header("Location: ../admin/dashboard.php");
                exit();
            } else if ($user['role'] == 'eo') {
                setcookie("flash_msg", "Selamat datang, Event Organizer!", time() + 5, "/");
                header("Location: ../eo/dashboard.php");
                exit();
            } else if ($user['role'] == 'customer') {
                setcookie("flash_msg", "Login Berhasil! Selamat datang di NTBeat.", time() + 5, "/");
                header("Location: ../user/beranda.php");
                exit();
            }
        } else {
            setcookie("flash_msg", "Email atau Kata Sandi salah. Silakan coba lagi!", time() + 5, "/");
            header("Location: ../auth/login.php");
            exit();
        }
    } else {
        // Kalau datanya nggak ketemu (email/password salah)
        setcookie("flash_msg", "Email atau Kata Sandi salah. Silakan coba lagi!", time() + 5, "/");
        header("Location: ../auth/login.php");
        exit();
    }
}
?>