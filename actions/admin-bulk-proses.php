<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'eo')) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action_type']) && isset($_POST['ids'])) {
    $action_type = $_POST['action_type'];
    $role = $_SESSION['role'];
    $email_user = $_SESSION['email'];
    $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : (($role === 'admin') ? '../admin/kelola-konser.php' : '../eo/kelola-konser.php');
    
    $ids = array_map('intval', $_POST['ids']);

    if ($action_type === 'delete') {
        foreach ($ids as $id) {
            if ($role === 'admin') {
                $stmt_get = $conn->prepare("SELECT poster FROM konser WHERE id = ?");
                $stmt_get->bind_param("i", $id);
            } else {
                $stmt_get = $conn->prepare("SELECT poster FROM konser WHERE id = ? AND eo_email = ?");
                $stmt_get->bind_param("is", $id, $email_user);
            }
            
            $stmt_get->execute();
            $result = $stmt_get->get_result();
            if ($row = $result->fetch_assoc()) {
                $poster = $row['poster'];
                if ($poster !== 'default-poster.png' && !empty($poster)) {
                    $file_path = "../assets/img/" . $poster;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }

            if ($role === 'admin') {
                $stmt_delete = $conn->prepare("DELETE FROM konser WHERE id = ?");
                $stmt_delete->bind_param("i", $id);
            } else {
                $stmt_delete = $conn->prepare("DELETE FROM konser WHERE id = ? AND eo_email = ?");
                $stmt_delete->bind_param("is", $id, $email_user);
            }
            $stmt_delete->execute();
        }
        setcookie("flash_msg", "Data konser berhasil dihapus!", time() + 5, "/");
        header("Location: $redirect_url");
        exit();
    } elseif ($action_type === 'archive') {
        foreach ($ids as $id) {
            if ($role === 'admin') {
                $stmt_archive = $conn->prepare("UPDATE konser SET status = 'Arsip' WHERE id = ?");
                $stmt_archive->bind_param("i", $id);
            } else {
                $stmt_archive = $conn->prepare("UPDATE konser SET status = 'Arsip' WHERE id = ? AND eo_email = ?");
                $stmt_archive->bind_param("is", $id, $email_user);
            }
            $stmt_archive->execute();
        }
        setcookie("flash_msg", "Konser berhasil diarsipkan!", time() + 5, "/");
        header("Location: $redirect_url");
        exit();
    }
} else {
    setcookie("flash_msg", "Tidak ada data yang dipilih atau aksi tidak valid.", time() + 5, "/");
    $redirect_url = ($_SESSION['role'] === 'admin') ? '../admin/kelola-konser.php' : '../eo/kelola-konser.php';
    header("Location: $redirect_url");
    exit();
}
?>
