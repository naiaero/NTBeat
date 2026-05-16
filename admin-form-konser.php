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
        <div class="logo-area">
            <img src="assets/img/logo.png" alt="Logo"> 
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav">
            <span style="color: white; font-size: 0.9rem;">Administrator</span>
            <div class="avatar-placeholder" onclick="openProfileModal()">A</div>
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
                <form action="#" class="ps-form" id="concert-form">
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
                            <input type="file" id="poster-input" hidden accept="image/*">
                        </div>
                    </div>
                    <!-- <div class="ps-avatar-section">
                        <div class="poster-upload-wrapper">
                            <div class="poster-preview" id="imagePreview">
                                <span>Preview Poster</span>
                            </div>
                            <label for="poster-input" class="ps-edit-icon">
                                📸
                                <input type="file" id="poster-input" hidden accept="image/*">
                            </label>
                        </div>
                    </div> -->
                    <p style="text-align: center; color: #888; font-size: 0.8rem; margin-bottom: 30px;">
                        Rekomendasi ukuran: 3:4 (Portrait)
                    </p>

                    <div class="ps-form-group">
                        <label>Nama Event</label>
                        <input type="text" class="ps-input" placeholder="Contoh: Mataram Sound Wave 2026" required>
                    </div>

                    <div class="ps-form-group" style="align-items: flex-start;">
                        <label>Line-up Artis</label>
                        <textarea class="ps-input" rows="3" placeholder="Sebutkan nama-nama artis (pisahkan dengan koma)"></textarea>
                    </div>

                    <div class="form-row-double">
                        <div class="ps-form-group">
                            <label>Tanggal Pelaksanaan</label>
                            <input type="date" class="ps-input" required>
                        </div>
                        <div class="ps-form-group">
                            <label>Lokasi / Venue</label>
                            <input type="text" class="ps-input" placeholder="Contoh: Eks Bandara Selaparang" required>
                        </div>
                    </div>

                    <h3 class="ps-subheading">Kapasitas & Penjualan</h3>

                    <div class="form-row-double">
                        <div class="ps-form-group">
                            <label>Harga Tiket (Rp)</label>
                            <input type="number" class="ps-input" placeholder="Contoh: 150000" required>
                        </div>
                        <div class="ps-form-group">
                            <label>Total Kapasitas</label>
                            <input type="number" class="ps-input" placeholder="Contoh: 1000" required>
                        </div>
                    </div>

                    <div class="ps-action-bar">
                        <button type="button" class="btn-ps-cancel" onclick="history.back()">Batal</button>
                        <button type="submit" class="btn-ps-save">Simpan Data Konser</button>
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
                <button class="btn-yakin" onclick="window.location.href = 'index.php'">Keluar</button>
            </div>
        </div>
    </div>

    <div id="profileModal" class="modal-overlay">
        <div class="profile-card" style="background-color: #1e1e1e; padding: 40px; border-radius: 20px; text-align: center; width: 400px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);">
            <div class="profile-img" style="width: 100px; height: 100px; background-color: #555; border-radius: 50%; margin: 0 auto 20px; display: flex; justify-content: center; align-items: center; font-size: 40px; color: white;">S</div>
            <h2 style="color: white;">Nama Pengguna</h2>

            <div class="profile-info" style="text-align: left; margin-top: 20px; color: white;">
                <div class="info-row" style="margin-bottom: 15px; display: flex;">
                    <span class="label" style="width: 120px; font-weight: bold; color: #ccc;">Email</span>
                    <span>: nama@gmail.com</span>
                </div>
                <div class="info-row" style="margin-bottom: 15px; display: flex; align-items: center;">
                    <span class="label" style="width: 120px; font-weight: bold; color: #ccc;">Password</span>
                    <span>: **********</span>
                    <button type="button" class="toggle-btn" onclick="togglePassword()" style="background: none; border: 1px solid #555; color: #ccc; cursor: pointer; margin-left: 20px; font-size: 11px; padding: 2px 6px; border-radius: 4px;">Lihat</button>
                </div>
            </div>

            <button class="btn-back" onclick="closeProfileModal()" style="background-color: #a32424; color: white; border: none; padding: 10px 30px; border-radius: 8px; margin-top: 20px; cursor: pointer; float: right;">Back</button>
        </div>
    </div>
</body>
</html>