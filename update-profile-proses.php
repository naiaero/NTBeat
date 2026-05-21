<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$role = $_SESSION['role'];

// Ambil data POST
$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

// Ambil data user dari database saat ini
$query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    echo "<script>
            alert('User tidak ditemukan.');
            window.history.back();
          </script>";
    exit();
}

$foto_name = isset($user['foto']) ? $user['foto'] : 'default-avatar.png';

// Proses Upload Foto (jika ada file baru yang dipilih)
if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $originalName = $_FILES['foto']['name'];
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Validasi ekstensi gambar
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            // Hapus foto lama dari disk jika bukan foto default
            if (!empty($user['foto']) && $user['foto'] !== 'default-avatar.png' && $user['foto'] !== 'tds4.jpg' && $user['foto'] !== 'logo.png') {
                $old_file = 'assets/img/' . $user['foto'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            // Generate nama file baru yang unik
            $newFileName = md5(time() . $originalName) . '.' . $fileExtension;
            $dest_path = 'assets/img/' . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $foto_name = $newFileName;
            } else {
                echo "<script>
                        alert('Gagal mengunggah foto ke direktori server.');
                        window.history.back();
                      </script>";
                exit();
            }
        } else {
            echo "<script>
                    alert('Format file tidak didukung. Harap unggah gambar (jpg, jpeg, png, gif).');
                    window.history.back();
                  </script>";
            exit();
        }
    } else {
        $error_code = $_FILES['foto']['error'];
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
                alert('Gagal mengunggah foto profil: $error_msg');
                window.history.back();
              </script>";
        exit();
    }
}

// Susun query update bidang dasar
$update_fields = "nama = '$nama', foto = '$foto_name'";

// Proses Update Password (jika admin/customer ingin mengganti password)
if (!empty($new_password)) {
    // Validasi apakah password saat ini dimasukkan
    if (empty($current_password)) {
        echo "<script>
                alert('Harap masukkan password saat ini untuk mengonfirmasi perubahan kata sandi.');
                window.history.back();
              </script>";
        exit();
    }

    // Verifikasi password saat ini
    if (password_verify($current_password, $user['password'])) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_fields .= ", password = '$hashed_new_password'";
    } else {
        echo "<script>
                alert('Password saat ini salah. Perubahan kata sandi gagal.');
                window.history.back();
              </script>";
        exit();
    }
}

// Eksekusi update ke database
$sql = "UPDATE users SET $update_fields WHERE email = '$email'";
if (mysqli_query($conn, $sql)) {
    // Perbarui data nama dan foto di Session agar langsung tampil di header & sidebar
    $_SESSION['nama'] = $nama;
    $_SESSION['foto'] = $foto_name;
    
    // Tentukan arah kembali berdasarkan role pengguna
    $redirect_url = ($role === 'admin') ? 'admin-profil.php' : 'profil.php';
    
    echo "<script>
            alert('Profil berhasil diperbarui!');
            window.location.href = '$redirect_url';
          </script>";
} else {
    echo "<script>
            alert('Gagal memperbarui profil di database: " . mysqli_error($conn) . "');
            window.history.back();
          </script>";
}
?>
