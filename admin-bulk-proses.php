<?php
session_start();
include 'koneksi.php';

// Verifikasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ids']) && is_array($_POST['ids'])) {
    $action_type = $_POST['action_type'];
    
    // Konversi array ID menjadi integer aman
    $ids = array_map('intval', $_POST['ids']);
    $ids_string = implode(',', $ids);

    if ($action_type === 'delete') {
        // Hapus poster gambar jika bukan default-poster.jpg sebelum menghapus dari DB
        $query_get_posters = mysqli_query($conn, "SELECT poster FROM konser WHERE id IN ($ids_string)");
        while ($row = mysqli_fetch_assoc($query_get_posters)) {
            $poster = $row['poster'];
            if ($poster !== 'default-poster.jpg' && !empty($poster)) {
                $file_path = "assets/img/" . $poster;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        $query = "DELETE FROM konser WHERE id IN ($ids_string)";
        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Data konser berhasil dihapus!');
                    window.location.href = 'admin-kelola-konser.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menghapus konser: " . mysqli_error($conn) . "');
                    window.location.href = 'admin-kelola-konser.php';
                  </script>";
        }
    } elseif ($action_type === 'archive') {
        $query = "UPDATE konser SET status = 'Arsip' WHERE id IN ($ids_string)";
        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Konser berhasil diarsipkan!');
                    window.location.href = 'admin-kelola-konser.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal mengarsipkan konser: " . mysqli_error($conn) . "');
                    window.location.href = 'admin-kelola-konser.php';
                  </script>";
        }
    } else {
        header("Location: admin-kelola-konser.php");
        exit();
    }
} else {
    echo "<script>
            alert('Tidak ada konser yang dipilih.');
            window.location.href = 'admin-kelola-konser.php';
          </script>";
}
?>
