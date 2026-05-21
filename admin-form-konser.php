<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$email_user = $_SESSION['email'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_user'");
$user_data = mysqli_fetch_assoc($query_user);
$nama_user = $user_data['nama'];
$foto_user = isset($user_data['foto']) ? $user_data['foto'] : '';
$inisial = strtoupper(substr($nama_user, 0, 1));

$foto_path = "assets/img/" . $foto_user;
$avatar_style = "";
if (!empty($foto_user) && file_exists($foto_path) && $foto_user !== 'default-avatar.png' && $foto_user !== 'tds4.jpg' && $foto_user !== 'logo.png') {
    $avatar_style = "background-image: url('$foto_path'); background-size: cover; background-position: center; color: transparent; border: 1px solid #d4af37;";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Konser - Admin NTBeat</title>
    <link rel="stylesheet" href="assets/style/admin-style.css">
    <link rel="stylesheet" href="assets/style/style.css">
    <script src="assets/script/script.js"></script>
</head>
<body class="admin-body">

    <header class="header-user">
        <div class="logo-area" onclick="window.location.href='admin-dashboard.php'" style="cursor: pointer;">
            <img src="assets/img/logo.png" alt="Logo"> 
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav" onclick="window.location.href = 'admin-profil.php'" style="cursor: pointer;">
            <span style="color: white; font-size: 0.9rem; margin-right: 10px;"><?php echo htmlspecialchars($nama_user); ?></span>
            <div class="avatar-placeholder" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li onclick="window.location.href = 'admin-dashboard.php'">Dashboard</li>
                <li class="active" onclick="window.location.href = 'admin-form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'admin-kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'admin-arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'admin-profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header">
                <h2>Formulir Data Konser</h2>
                <p>Silakan isi detail informasi konser dengan lengkap dan akurat.</p>
            </div>

            <div class="ps-card">
                <form action="admin-tambah-proses.php" method="POST" enctype="multipart/form-data" class="ps-form" id="concert-form">
                    <div class="ps-avatar-section">
                        <div class="poster-upload-wrapper">
                            <!-- Kotak tempat gambar akan dirender oleh JavaScript -->
                            <div class="poster-preview" id="imagePreview">
                                <span>Preview Poster</span>
                            </div>
                            
                            <!-- Label hanya bertugas sebagai tombol pemicu klik -->
                            <label for="poster-input" class="ps-edit-icon">
                                📸
                            </label>
                            
                             <!-- Input file ditaruh di luar secara mandiri -->
                             <input type="file" id="poster-input" name="poster" style="display: none;" accept="image/*">
                        </div>
                    </div>
                    <p style="text-align: center; color: #888; font-size: 0.8rem; margin-bottom: 30px;">
                        Rekomendasi ukuran: 3:4 (Portrait)
                    </p>

                    <div class="ps-form-group">
                        <label>Nama Event</label>
                        <input type="text" name="nama_konser" class="ps-input" placeholder="Contoh: Mataram Sound Wave 2026" required>
                    </div>

                    <div class="ps-form-group" style="align-items: flex-start;">
                        <label>Line-up Artis</label>
                        <textarea name="lineup" class="ps-input" rows="3" placeholder="Sebutkan nama-nama artis (pisahkan dengan koma)"></textarea>
                    </div>

                    <div class="form-row-double">
                        <div class="ps-form-group">
                            <label>Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal" class="ps-input" required>
                        </div>
                        <div class="ps-form-group">
                            <label>Waktu Pelaksanaan</label>
                            <input type="time" name="waktu" class="ps-input" required>
                        </div>
                    </div>

                    <div class="ps-form-group">
                        <label>Lokasi / Venue</label>
                        <input type="text" name="lokasi" class="ps-input" placeholder="Contoh: Eks Bandara Selaparang" required>
                    </div>

                    <h3 class="ps-subheading">Kapasitas & Penjualan</h3>

                    <div class="form-row-double">
                        <div class="ps-form-group">
                            <label>Harga Tiket (Rp)</label>
                            <input type="number" name="harga" class="ps-input" placeholder="Contoh: 150000" required>
                        </div>
                        <div class="ps-form-group">
                            <label>Total Kapasitas</label>
                            <input type="number" name="kapasitas" class="ps-input" placeholder="Contoh: 1000" required>
                        </div>
                    </div>

                    <div class="ps-action-bar">
                        <button type="button" class="btn-ps-cancel" onclick="history.back()">Batal</button>
                        <button type="submit" class="btn-ps-save" name="submit">Simpan Data Konser</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <div id="logoutModal" class="modal-overlay">
        <div class="logout-card">
            <h2>⚠️ Konfirmasi Keluar</h2>
            <p>Apakah Anda yakin ingin keluar dari sistem NTBeat?</p>

            <div class="logout-actions">
                <button class="btn-batal" onclick="closeLogoutModal()">Batal</button>
                <button class="btn-yakin" onclick="window.location.href = 'logout.php'">Keluar</button>
            </div>
        </div>
    </div>
</body>
</html>