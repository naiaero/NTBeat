<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $nama_konser = mysqli_real_escape_string($conn, $_POST['nama_konser']);
    $lineup      = mysqli_real_escape_string($conn, $_POST['lineup']);
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $waktu       = mysqli_real_escape_string($conn, $_POST['waktu']);
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
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
                    echo "<script>
                            alert('Gagal memindahkan file poster ke folder tujuan.');
                            window.history.back();
                          </script>";
                    exit();
                }
            } else {
                echo "<script>
                        alert('Format file poster tidak didukung. Harap unggah gambar (jpg, jpeg, png, gif).');
                        window.history.back();
                      </script>";
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
            echo "<script>
                    alert('Gagal mengunggah poster: $error_msg');
                    window.history.back();
                  </script>";
            exit();
        }
    }

    // Insert ke database
    $query = "INSERT INTO konser (nama_konser, lineup, tanggal, waktu, lokasi, deskripsi, harga, kapasitas, tiket_terjual, poster, status) 
              VALUES ('$nama_konser', '$lineup', '$tanggal', '$waktu', '$lokasi', '$deskripsi', $harga, $kapasitas, 0, '$poster', 'Tersedia')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Konser berhasil ditambahkan!');
                window.location.href = '../admin/kelola-konser.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan konser: " . mysqli_error($conn) . "');
                window.location.href = '../admin/form-konser.php';
              </script>";
    }
} else {
    header("Location: ../admin/form-konser.php");
    exit();
}
?>
