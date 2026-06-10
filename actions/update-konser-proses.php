<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'eo')) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['submit_update'])) {
    $id          = intval($_POST['id']);
    $nama_konser = trim($_POST['nama_konser']);
    $lineup      = trim($_POST['lineup']);
    $tanggal     = trim($_POST['tanggal']);
    $waktu       = trim($_POST['waktu']);
    $lokasi      = trim($_POST['lokasi']);
    $deskripsi   = trim($_POST['deskripsi']);
    $harga       = floatval($_POST['harga']);
    $kapasitas   = intval($_POST['kapasitas']);

    $role = $_SESSION['role'];
    $email_user = $_SESSION['email'];
    $redirect_dir = ($role === 'admin') ? '../admin/' : '../eo/';

    if ($role === 'admin') {
        $stmt_old = $conn->prepare("SELECT poster, tiket_terjual, status FROM konser WHERE id = ?");
        $stmt_old->bind_param("i", $id);
    } else {
        $stmt_old = $conn->prepare("SELECT poster, tiket_terjual, status FROM konser WHERE id = ? AND eo_email = ?");
        $stmt_old->bind_param("is", $id, $email_user);
    }
    
    $stmt_old->execute();
    $query_old = $stmt_old->get_result();
    if ($query_old->num_rows == 0) {
        setcookie("flash_msg", "Konser tidak ditemukan!", time() + 5, "/");
        header("Location: " . $redirect_dir . "kelola-konser.php");
        exit();
    }
    $old_data = mysqli_fetch_assoc($query_old);
    $poster = $old_data['poster'];
    $tiket_terjual = intval($old_data['tiket_terjual']);
    $current_status = $old_data['status'];

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $target_dir  = "../assets/img/";
        $file_name   = basename($_FILES["poster"]["name"]);
        $file_ext    = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed_extensions)) {
            $unique_name = "poster_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
            $target_file = $target_dir . $unique_name;

            if (move_uploaded_file($_FILES["poster"]["tmp_name"], $target_file)) {
                if ($poster !== 'default-poster.png' && !empty($poster)) {
                    $old_file = $target_dir . $poster;
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                $poster = $unique_name;
            } else {
                setcookie("flash_msg", "Gagal memindahkan file poster.", time() + 5, "/");
                header("Location: " . $redirect_dir . "edit-konser.php?id=$id");
                exit();
            }
        }
    }

    if ($current_status !== 'Arsip' && $current_status !== 'Selesai') {
        $sisa = $kapasitas - $tiket_terjual;
        if ($sisa <= 0) {
            $status = 'Habis';
        } elseif ($sisa <= 150 || $tiket_terjual >= $kapasitas * 0.85) {
            $status = 'Hampir Habis';
        } else {
            $status = 'Tersedia';
        }
    } else {
        $status = $current_status;
    }

    if ($role === 'admin') {
        $stmt_update = $conn->prepare("UPDATE konser SET nama_konser=?, lineup=?, tanggal=?, waktu=?, lokasi=?, deskripsi=?, harga=?, kapasitas=?, poster=?, status=? WHERE id=?");
        $stmt_update->bind_param("ssssssdissi", $nama_konser, $lineup, $tanggal, $waktu, $lokasi, $deskripsi, $harga, $kapasitas, $poster, $status, $id);
    } else {
        $stmt_update = $conn->prepare("UPDATE konser SET nama_konser=?, lineup=?, tanggal=?, waktu=?, lokasi=?, deskripsi=?, harga=?, kapasitas=?, poster=?, status=? WHERE id=? AND eo_email=?");
        $stmt_update->bind_param("ssssssdissis", $nama_konser, $lineup, $tanggal, $waktu, $lokasi, $deskripsi, $harga, $kapasitas, $poster, $status, $id, $email_user);
    }

    if ($stmt_update->execute()) {
        setcookie("flash_msg", "Konser berhasil diperbarui!", time() + 5, "/");
        header("Location: " . $redirect_dir . "kelola-konser.php");
    } else {
        setcookie("flash_msg", "Gagal memperbarui konser.", time() + 5, "/");
        header("Location: " . $redirect_dir . "edit-konser.php?id=$id");
    }
} else {
    header("Location: ../auth/login.php");
    exit();
}
?>