<?php
session_start();
include 'koneksi.php';

// Membatasi akses hanya untuk admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit_update'])) {
    $id          = intval($_POST['id']);
    $nama_konser = mysqli_real_escape_string($conn, $_POST['nama_konser']);
    $lineup      = mysqli_real_escape_string($conn, $_POST['lineup']);
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $waktu       = mysqli_real_escape_string($conn, $_POST['waktu']);
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $harga       = floatval($_POST['harga']);
    $kapasitas   = intval($_POST['kapasitas']);

    // Ambil data lama untuk poster dan tiket_terjual
    $query_old = mysqli_query($conn, "SELECT poster, tiket_terjual, status FROM konser WHERE id = $id");
    if (mysqli_num_rows($query_old) == 0) {
        echo "<script>
                alert('Konser tidak ditemukan!');
                window.location.href = 'admin-kelola-konser.php';
              </script>";
        exit();
    }
    $old_data = mysqli_fetch_assoc($query_old);
    $poster = $old_data['poster'];
    $tiket_terjual = intval($old_data['tiket_terjual']);
    $current_status = $old_data['status'];

    // Proses upload gambar poster baru jika ada
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $target_dir  = "assets/img/";
            
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
                    echo "<script>
                            alert('Gagal memindahkan file poster baru ke folder tujuan.');
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
                    alert('Gagal mengunggah poster baru: $error_msg');
                    window.history.back();
                  </script>";
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
    $query = "UPDATE konser SET 
                nama_konser = '$nama_konser', 
                lineup = '$lineup', 
                tanggal = '$tanggal', 
                waktu = '$waktu', 
                lokasi = '$lokasi', 
                harga = $harga, 
                kapasitas = $kapasitas, 
                poster = '$poster', 
                status = '$status' 
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Konser berhasil diperbarui!');
                window.location.href = 'admin-kelola-konser.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memperbarui konser: " . mysqli_error($conn) . "');
                window.location.href = 'admin-edit-konser.php?id=$id';
              </script>";
    }
} else {
    header("Location: admin-kelola-konser.php");
    exit();
}
?>