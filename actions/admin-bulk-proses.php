<?php
session_start();
include '../config/koneksi.php';

// Verifikasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ids']) && is_array($_POST['ids'])) {
    $action_type = $_POST['action_type'];
    $source_page = isset($_POST['source_page']) ? $_POST['source_page'] : 'kelola-konser.php';
    $redirect_url = "../admin/" . $source_page;
    
    // Konversi array ID menjadi integer aman
    $ids = array_map('intval', $_POST['ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    if ($action_type === 'delete') {
        // Hapus poster gambar jika bukan default-poster.png sebelum menghapus dari DB
        $stmt_get = $conn->prepare("SELECT poster FROM konser WHERE id IN ($placeholders)");
        $stmt_get->bind_param($types, ...$ids);
        $stmt_get->execute();
        $query_get_posters = $stmt_get->get_result();
        while ($row = $query_get_posters->fetch_assoc()) {
            $poster = $row['poster'];
            if ($poster !== 'default-poster.png' && !empty($poster)) {
                $file_path = "../assets/img/" . $poster;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        $stmt_delete = $conn->prepare("DELETE FROM konser WHERE id IN ($placeholders)");
        $stmt_delete->bind_param($types, ...$ids);
        if ($stmt_delete->execute()) {
            setcookie("flash_msg", "Data konser berhasil dihapus!", time() + 5, "/");
            header("Location: $redirect_url");
            exit();
        } else {
            setcookie("flash_msg", "Gagal menghapus konser: " . mysqli_error($conn), time() + 5, "/");
            header("Location: $redirect_url");
            exit();
        }
    } elseif ($action_type === 'archive') {
        $stmt_archive = $conn->prepare("UPDATE konser SET status = 'Arsip' WHERE id IN ($placeholders)");
        $stmt_archive->bind_param($types, ...$ids);
        if ($stmt_archive->execute()) {
            setcookie("flash_msg", "Konser berhasil diarsipkan!", time() + 5, "/");
            header("Location: $redirect_url");
            exit();
        } else {
            setcookie("flash_msg", "Gagal mengarsipkan konser: " . mysqli_error($conn), time() + 5, "/");
            header("Location: $redirect_url");
            exit();
        }
    } else {
        header("Location: $redirect_url");
        exit();
    }
} else {
    $source_page = isset($_POST['source_page']) ? $_POST['source_page'] : 'kelola-konser.php';
    $redirect_url = "../admin/" . $source_page;
    setcookie("flash_msg", "Tidak ada konser yang dipilih.", time() + 5, "/");
    header("Location: $redirect_url");
    exit();
}
?>
