<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

    // Ambil data lama untuk poster dan tiket_terjual
    $stmt_old = $conn->prepare("SELECT poster, tiket_terjual, status FROM konser WHERE id = ?");
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $query_old = $stmt_old->get_result();
    if ($query_old->num_rows == 0) {
        setcookie("flash_msg", "Konser tidak ditemukan!", time() + 5, "/");
        header("Location: ../admin/kelola-konser.php");
        exit();
    }
    $old_data = mysqli_fetch_assoc($query_old);
    $poster = $old_data['poster'];
    $tiket_terjual = intval($old_data['tiket_terjual']);
    $current_status = $old_data['status'];

    // Proses upload gambar poster baru jika ada
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $target_dir  = "../assets/img/";
            
            // Buat direktori jika belum ada
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $file_name   = basename($_FILES["poster"]["name"]);
            $file_ext    = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validasi tipe file
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_ext, $allowed_extensions)) {
                // Beri nama unik agar tidak bentrok
                $unique_name = "poster_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
                $target_file = $target_dir . $unique_name;

                if (move_uploaded_file($_FILES["poster"]["tmp_name"], $target_file)) {
                    // Hapus file lama jika bukan default
                    if ($poster !== 'default-poster.png' && !empty($poster)) {
                        $old_file = $target_dir . $poster;
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    $poster = $unique_name;
                } else {
                    setcookie("flash_msg", "Gagal memindahkan file poster baru ke folder tujuan.", time() + 5, "/");
                    header("Location: ../admin/edit-konser.php?id=$id");
                    exit();
                }
            } else {
                setcookie("flash_msg", "Format file poster tidak didukung. Harap unggah gambar (jpg, jpeg, png, gif).", time() + 5, "/");
                header("Location: ../admin/edit-konser.php?id=$id");
                exit();
            }
        } else {
            $error_code = $_FILES['poster']['error'];
            $error_msg = "Unknown upload error.";
            switch ($error_code) {
                case UPLOAD_ERR_INI_SIZE:
                    $error_msg = "Ukuran file melebihi batas upload_max_filesize di php.ini.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error_msg = "Ukuran file melebihi batas MAX_FILE_SIZE di form HTML.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_msg = "File hanya terunggah sebagian.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_msg = "Folder temp PHP hilang/tidak ada.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_msg = "Gagal menulis file ke disk server.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error_msg = "Ekstensi PHP membatalkan proses upload.";
                    break;
            }
            setcookie("flash_msg", "Gagal mengunggah poster baru: $error_msg", time() + 5, "/");
            header("Location: ../admin/edit-konser.php?id=$id");
            exit();
        }
    }

    // Tentukan status berdasarkan kapasitas baru dan tiket yang sudah terjual
    // Jangan ubah status jika konser sudah diarsipkan ('Arsip') atau selesai ('Selesai')
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

    // Update ke database
    $stmt_update = $conn->prepare("UPDATE konser SET 
                nama_konser = ?, 
                lineup = ?, 
                tanggal = ?, 
                waktu = ?, 
                lokasi = ?, 
                deskripsi = ?, 
                harga = ?, 
                kapasitas = ?, 
                poster = ?, 
                status = ? 
              WHERE id = ?");
    $stmt_update->bind_param("ssssssdissi", $nama_konser, $lineup, $tanggal, $waktu, $lokasi, $deskripsi, $harga, $kapasitas, $poster, $status, $id);

    if ($stmt_update->execute()) {
        setcookie("flash_msg", "Konser berhasil diperbarui!", time() + 5, "/");
        header("Location: ../admin/kelola-konser.php");
    } else {
        setcookie("flash_msg", "Gagal memperbarui konser: " . mysqli_error($conn), time() + 5, "/");
        header("Location: ../admin/edit-konser.php?id=$id");
    }
} else {
    header("Location: ../admin/kelola-konser.php");
    exit();
}
?>