<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$email_user = $_SESSION['email'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_user'");
$user_data = mysqli_fetch_assoc($query_user);
$nama_user = $user_data['nama'];
$foto_user = isset($user_data['foto']) ? $user_data['foto'] : '';
$inisial = strtoupper(substr($nama_user, 0, 1));

$foto_path = "../assets/img/" . $foto_user;
$avatar_style = "";
if (!empty($foto_user) && file_exists($foto_path) && $foto_user !== 'default-avatar.png' && $foto_user !== 'tds4.jpg' && $foto_user !== 'logo.png') {
    $avatar_style = "background-image: url('$foto_path'); background-size: cover; background-position: center; color: transparent; border: 1px solid #d4af37;";
}

// Fallback for profile settings page picture preview image
if (empty($foto_user) || !file_exists($foto_path) || $foto_user === 'default-avatar.png') {
    $foto_path = "../assets/img/default-avatar.png";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Administrator - NTBeat</title>
    <link rel="stylesheet" href="../assets/style/admin-style.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <script src="../assets/script/script.js"></script>
</head>
<body class="admin-body">

    <header class="header-user">
        <div class="logo-area" onclick="window.location.href = 'dashboard.php'">
            <img src="../assets/img/logo.png" alt="Logo"> 
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav" onclick="window.location.href = 'profil.php'" style="cursor: pointer;">
            <span style="color: white; font-size: 0.9rem; margin-right: 10px;"><?php echo htmlspecialchars($nama_user); ?></span>
            <div class="avatar-placeholder" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li onclick="window.location.href = 'dashboard.php'">Dashboard</li>
                <li onclick="window.location.href = 'form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'arsip.php'">Arsip Penyelenggaraan</li>
                <li class="active" onclick="window.location.href = 'profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="ps-container">
                <div class="section-header">
                    <h2>Profil Administrator</h2>
                    <p>Kelola data login dan keamanan akun utama sistem NTBeat.</p>
                </div>

                <div class="ps-card">
                    <form class="ps-form" id="admin-profile-form" action="../actions/update-profile-proses.php" method="POST" enctype="multipart/form-data">
                        <div class="ps-avatar-section">
                            <div class="ps-avatar-wrapper" style="position: relative; width: 100px; height: 100px; margin: 0 auto;">
                                <div id="avatar-preview" style="width: 100px; height: 100px; border-radius: 50%; border: 1px solid #333; display: flex; align-items: center; justify-content: center; background-color: #333; color: #d4af37; font-size: 40px; font-weight: bold; <?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
                                <label for="avatar-input" class="ps-edit-icon" style="position: absolute; bottom: 0; right: 0; background: #d4af37; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem; border: 1px solid #333; z-index: 10;">✏️</label>
                                <input type="file" id="avatar-input" name="foto" style="display: none;" accept="image/*" />
                            </div>
                        </div>

                        <div class="ps-form-group">
                            <label for="nama">Nama</label>
                            <input type="text" id="username" name="nama" class="ps-input" value="<?php echo htmlspecialchars($nama_user); ?>" required />
                        </div>

                        <div class="ps-form-group">
                            <label for="email">Alamat Email</label>
                            <input type="email" id="email" class="ps-input" value="<?php echo htmlspecialchars($email_user); ?>" readonly style="background-color: #222; color: #888; cursor: not-allowed;" required />
                        </div>

                        <h3 class="ps-subheading">Ubah Kata Sandi</h3>

                        <div class="ps-form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="ps-input" placeholder="Masukkan password lama" />
                        </div>

                        <div class="ps-form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" id="new_password" name="new_password" class="ps-input" placeholder="Masukkan password baru" />
                        </div>

                        <div class="ps-action-bar">
                            <button type="button" class="btn-ps-cancel" onclick="window.location.href = 'dashboard.php'">
                                Batal
                            </button>
                            <button type="submit" class="btn-ps-save">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <div id="logoutModal" class="modal-overlay">
        <div class="logout-card">
            <h2>⚠️ Konfirmasi Keluar</h2>
            <p>Apakah Anda yakin ingin keluar dari sistem NTBeat?</p>
            <div class="logout-actions">
                <button class="btn-batal" onclick="closeLogoutModal()">Batal</button>
                <button class="btn-yakin" onclick="window.location.href = '../auth/logout.php'">Keluar</button>
            </div>
        </div>
    </div>
</body>
</html>