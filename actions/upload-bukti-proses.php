<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_FILES['bukti_bayar'])) {
    $order_id = $_POST['order_id'];
    $user_email = $_SESSION['email'];

    // Pastikan folder ada
    $target_dir = "../assets/img/bukti_bayar/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file = $_FILES['bukti_bayar'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    // Validasi file upload
    if ($file_error === 0) {
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($file_ext, $allowed_ext)) {
            if ($file_size <= 5000000) { // Max 5MB
                // Buat nama file unik
                $new_file_name = "bukti_" . $order_id . "_" . time() . "." . $file_ext;
                $target_file = $target_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    // Simpan nama file ke database
                    $stmt = $conn->prepare("UPDATE pesanan SET bukti_bayar = ?, status_bayar = 'Menunggu Verifikasi' WHERE order_id = ? AND user_email = ? AND status_bayar = 'Pending'");
                    $stmt->bind_param("sss", $new_file_name, $order_id, $user_email);
                    
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        setcookie("flash_msg", "Bukti pembayaran berhasil diunggah! Menunggu verifikasi.", time() + 5, "/");
                    } else {
                        // Jika gagal update DB, hapus file
                        unlink($target_file);
                        setcookie("flash_msg", "Gagal menyimpan data bukti pembayaran. Pesanan mungkin sudah tidak aktif.", time() + 5, "/");
                    }
                } else {
                    setcookie("flash_msg", "Terjadi kesalahan saat mengunggah file gambar.", time() + 5, "/");
                }
            } else {
                setcookie("flash_msg", "Ukuran gambar terlalu besar! Maksimal 5MB.", time() + 5, "/");
            }
        } else {
            setcookie("flash_msg", "Format file tidak didukung! Gunakan JPG, JPEG, atau PNG.", time() + 5, "/");
        }
    } else {
        setcookie("flash_msg", "Terdapat error pada file yang diunggah.", time() + 5, "/");
    }

    header("Location: ../user/tiket-saya.php");
    exit();
} else {
    header("Location: ../user/beranda.php");
    exit();
}
?>
