<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Administrator - NTBeat</title>
    <link rel="stylesheet" href="assets/style/admin-style.css">
    <link rel="stylesheet" href="assets/style/style.css">
    <script src="assets/script/script.js"></script>
</head>
<body class="admin-body">

    <header class="header-user">
        <div class="logo-area" onclick="window.location.href = 'admin-dashboard.php'">
            <img src="assets/img/logo.png" alt="Logo"> 
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav">
            <span style="color: white; font-size: 0.9rem;">Administrator</span>
            <div class="avatar-placeholder">A</div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li onclick="window.location.href = 'admin-dashboard.php'">Dashboard</li>
                <li onclick="window.location.href = 'admin-form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'admin-kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'admin-arsip.php'">Arsip Penyelenggaraan</li>
                <li class="active" onclick="window.location.href = 'admin-profil.php'">Pengaturan Profil</li>
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
                    <div class="ps-avatar-section">
                        <div class="ps-avatar-wrapper">
                            <div class="avatar-placeholder" style="width: 100px; height: 100px; font-size: 2.5rem; border-radius: 50%; margin: 0 auto;">A</div>
                        </div>
                    </div>

                    <form class="ps-form" id="admin-profile-form" action="#">
                        <div class="ps-form-group">
                            <label for="username">Username Admin</label>
                            <input type="text" id="username" class="ps-input" value="admin_ntbeat" required />
                        </div>

                        <div class="ps-form-group">
                            <label for="email">Alamat Email</label>
                            <input type="email" id="email" class="ps-input" value="admin@ntbeat.com" required />
                        </div>

                        <h3 class="ps-subheading">Ubah Kata Sandi</h3>

                        <div class="ps-form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" id="current_password" class="ps-input" placeholder="Masukkan password lama admin" />
                        </div>

                        <div class="ps-form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" id="new_password" class="ps-input" placeholder="Masukkan password baru admin" />
                        </div>

                        <div class="ps-action-bar">
                            <button type="button" class="btn-ps-cancel" onclick="window.location.href = 'admin-dashboard.php'">
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
                <button class="btn-yakin" onclick="window.location.href = 'index.php'">Keluar</button>
            </div>
        </div>
    </div>
</body>
</html>