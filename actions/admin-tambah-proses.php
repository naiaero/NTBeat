<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

    // Default poster jika tidak upload
    $poster = 'default-poster.png';

    // Proses upload gambar poster
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
                    $poster = $unique_name;
                } else {
                    setcookie("flash_msg", "Gagal memindahkan file poster ke folder tujuan.", time() + 5, "/");
                    header("Location: ../admin/form-konser.php");
                    exit();
                }
            } else {
                setcookie("flash_msg", "Format file poster tidak didukung. Harap unggah gambar (jpg, jpeg, png, gif).", time() + 5, "/");
                header("Location: ../admin/form-konser.php");
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
            setcookie("flash_msg", "Gagal mengunggah poster: $error_msg", time() + 5, "/");
            header("Location: ../admin/form-konser.php");
            exit();
        }
    }

    // Insert ke database
    $status = 'Tersedia';
    $tiket_terjual = 0;
    
    $stmt = $conn->prepare("INSERT INTO konser (nama_konser, lineup, tanggal, waktu, lokasi, deskripsi, harga, kapasitas, tiket_terjual, poster, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdiiss", $nama_konser, $lineup, $tanggal, $waktu, $lokasi, $deskripsi, $harga, $kapasitas, $tiket_terjual, $poster, $status);

    if ($stmt->execute()) {
        setcookie("flash_msg", "Konser berhasil ditambahkan!", time() + 5, "/");
        header("Location: ../admin/kelola-konser.php");
    } else {
        setcookie("flash_msg", "Gagal menambahkan konser: " . mysqli_error($conn), time() + 5, "/");
        header("Location: ../admin/form-konser.php");
    }
} else {
    header("Location: ../admin/form-konser.php");
    exit();
}
?>
