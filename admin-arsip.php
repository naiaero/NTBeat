<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Acara - NTBeat</title>
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
            <div class="avatar-placeholder" onclick="openProfileModal()>A</div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li onclick="window.location.href = 'admin-dashboard.php'">Dashboard</li>
                <li onclick="window.location.href = 'admin-form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'admin-kelola-konser.php'">Kelola Data Konser</li>
                <li class="active" onclick="window.location.href = 'admin-arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'admin-profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header">
                <h2>Arsip Penyelenggaraan</h2>
                <p>Pilih acara untuk melihat laporan detail penjualan dan statistik masa lalu.</p>
            </div>

            <div class="bulk-action-bar">
                <div class="selected-count">
                    <span id="count-display">0</span> Data Terpilih
                </div>
                <div class="action-buttons">
                    <button class="btn-universal archive" id="btn-report-bulk" disabled>Lihat Laporan Kolektif</button>
                </div>
            </div>

            <div class="table-box archive-container">
                <table class="admin-table archive-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="check-all"></th>
                            <th>Poster</th>
                            <th>Nama Konser</th>
                            <th>Tanggal Terlaksana</th>
                            <th>Total Penjualan</th>
                            <th>Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="archive-list">
                        <tr class="past-event">
                            <td><input type="checkbox" class="row-checkbox" value="LSF-2025"></td>
                            <td><div class="mini-poster archived"></div></td>
                            <td>
                                <strong>Lombok Summer Fest 2025</strong><br>
                                <small>ID: LSF-2025</small>
                            </td>
                            <td>12 Agustus 2025</td>
                            <td>985 / 1000 Tiket</td>
                            <td><span class="badge-archived">Selesai</span></td>
                        </tr>
                        <tr class="past-event">
                            <td><input type="checkbox" class="row-checkbox" value="SNH-2025"></td>
                            <td><div class="mini-poster archived" style="background-color: #333;"></div></td>
                            <td>
                                <strong>Sumbawa Night Harmony</strong><br>
                                <small>ID: SNH-2025</small>
                            </td>
                            <td>05 Januari 2025</td>
                            <td>500 / 500 Tiket</td>
                            <td><span class="badge-archived">Sold Out</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="archive-footer">
                <p>Data di halaman ini bersifat permanen dan digunakan untuk keperluan audit keuangan organisasi.</p>
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