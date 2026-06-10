<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'eo')) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $nama_konser = trim($_POST['nama_konser']);
    $lineup      = trim($_POST['lineup']);
    $tanggal     = trim($_POST['tanggal']);
    $waktu       = trim($_POST['waktu']);
    $lokasi      = trim($_POST['lokasi']);
    $deskripsi   = trim($_POST['deskripsi']);
    $harga       = floatval($_POST['harga']);
    $kapasitas   = intval($_POST['kapasitas']);
    
    $role = $_SESSION['role'];
    $eo_email = $_SESSION['email']; 
    $redirect_dir = ($role === 'admin') ? '../admin/' : '../eo/';

    // Proses upload poster
    $poster = 'default-poster.png'; // default
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../assets/img/";
        $file_name = basename($_FILES["poster"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_extensions)) {
            $unique_name = "poster_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
            $target_file = $target_dir . $unique_name;
            
            if (move_uploaded_file($_FILES["poster"]["tmp_name"], $target_file)) {
                $poster = $unique_name;
            }
        }
    }

    $status = 'Tersedia';
    
    $stmt = $conn->prepare("INSERT INTO konser (nama_konser, lineup, tanggal, waktu, lokasi, deskripsi, harga, kapasitas, tiket_terjual, poster, status, eo_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?)");
    $stmt->bind_param("ssssssdisss", $nama_konser, $lineup, $tanggal, $waktu, $lokasi, $deskripsi, $harga, $kapasitas, $poster, $status, $eo_email);

    if ($stmt->execute()) {
        setcookie("flash_msg", "Konser berhasil ditambahkan!", time() + 5, "/");
        header("Location: " . $redirect_dir . "kelola-konser.php");
    } else {
        setcookie("flash_msg", "Gagal menambahkan konser: " . mysqli_error($conn), time() + 5, "/");
        header("Location: " . $redirect_dir . "form-konser.php");
    }
} else {
    header("Location: ../auth/login.php");
    exit();
}
?>
