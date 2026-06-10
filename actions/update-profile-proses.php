<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

$email = $_SESSION['email'];
$role = $_SESSION['role'];
if ($role === 'admin') {
    $redirect_url = '../admin/profil.php';
} else if ($role === 'eo') {
    $redirect_url = '../eo/profil.php';
} else {
    $redirect_url = '../user/profil.php';
}

// Ambil data POST
$nama = trim($_POST['nama']);
$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

// Ambil data user dari database saat ini
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    setcookie("flash_msg", "User tidak ditemukan.", time() + 5, "/");
    header("Location: $redirect_url");
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
                $old_file = '../assets/img/' . $user['foto'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            // Generate nama file baru yang unik
            $newFileName = md5(time() . $originalName) . '.' . $fileExtension;
            $dest_path = '../assets/img/' . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $foto_name = $newFileName;
            } else {
                setcookie("flash_msg", "Gagal mengunggah foto ke direktori server.", time() + 5, "/");
                header("Location: $redirect_url");
                exit();
            }
        } else {
            setcookie("flash_msg", "Format file tidak didukung. Harap unggah gambar (jpg, jpeg, png, gif).", time() + 5, "/");
            header("Location: $redirect_url");
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
        setcookie("flash_msg", "Gagal mengunggah foto profil: $error_msg", time() + 5, "/");
        header("Location: $redirect_url");
        exit();
    }
}

$stmt_update = null;

// Proses Update Password (jika admin/customer ingin mengganti password)
if (!empty($new_password)) {
    // Validasi apakah password saat ini dimasukkan
    if (empty($current_password)) {
        setcookie("flash_msg", "Harap masukkan password saat ini untuk mengonfirmasi perubahan kata sandi.", time() + 5, "/");
        header("Location: $redirect_url");
        exit();
    }

    // Verifikasi password saat ini
    if (password_verify($current_password, $user['password'])) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_update = $conn->prepare("UPDATE users SET nama = ?, foto = ?, password = ? WHERE email = ?");
        $stmt_update->bind_param("ssss", $nama, $foto_name, $hashed_new_password, $email);
    } else {
        setcookie("flash_msg", "Password saat ini salah. Perubahan kata sandi gagal.", time() + 5, "/");
        header("Location: $redirect_url");
        exit();
    }
} else {
    $stmt_update = $conn->prepare("UPDATE users SET nama = ?, foto = ? WHERE email = ?");
    $stmt_update->bind_param("sss", $nama, $foto_name, $email);
}

// Eksekusi update ke database
if ($stmt_update->execute()) {
    // Perbarui data nama dan foto di Session agar langsung tampil di header & sidebar
    if ($stmt_update->affected_rows === 0) {
        setcookie("flash_msg", "Tidak ada perubahan data yang dilakukan.", time() + 5, "/");
    } else {
        $_SESSION['nama'] = $nama;
        $_SESSION['foto'] = $foto_name;
        setcookie("flash_msg", "Profil berhasil diperbarui!", time() + 5, "/");
        
        // Cek update alamat
        if (isset($_POST['alamat']) && $_SESSION['role'] === 'eo') {
            $alamat = $_POST['alamat'];
            $stmt_al = $conn->prepare("UPDATE users SET alamat = ? WHERE email = ?");
            $stmt_al->bind_param("ss", $alamat, $email);
            $stmt_al->execute();
        }
    }
    header("Location: $redirect_url");
} else {
    setcookie("flash_msg", "Gagal memperbarui profil di database: " . mysqli_error($conn), time() + 5, "/");
    header("Location: $redirect_url");
}
?>
