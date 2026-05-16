<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Konser - NTBeat</title>
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
                <li onclick="window.location.href = 'admin-form-konser.php'">Tambah Acara Baru</li>
                <li class="active" onclick="window.location.href = 'admin-kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'admin-arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'admin-profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header">
                <h2>Pusat Kendali Konser</h2>
                <p>Pilih satu atau beberapa konser untuk melakukan aksi massal.</p>
            </div>

            <div class="bulk-action-bar">
                <div class="selected-count">
                    <span id="count-display">0</span> Data Terpilih
                </div>
                <div class="action-buttons">
                    <button class="btn-universal edit" id="btn-edit-bulk" disabled>Edit Detail</button>
                    <button class="btn-universal archive" id="btn-archive-bulk" disabled>Arsipkan</button>
                    <button class="btn-universal delete" id="btn-delete-bulk" disabled>Hapus Data</button>
                </div>
            </div>

            <div class="table-box">
                <table class="admin-table manage-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="check-all"></th>
                            <th>Poster</th>
                            <th>Nama Konser</th>
                            <th>Tanggal & Lokasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="concert-list">
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" value="MSW-001"></td>
                            <td><div class="mini-poster"></div></td>
                            <td><strong>Mataram Sound Wave</strong><br><small>ID: MSW-001</small></td>
                            <td>15 Mei 2026<br><small>Selaparang</small></td>
                            <td><span class="badge-safe">Terbit</span></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" value="SJN-002"></td>
                            <td><div class="mini-poster" style="background-color: #444;"></div></td>
                            <td><strong>Senggigi Jazz Night</strong><br><small>ID: SJN-002</small></td>
                            <td>20 Juni 2026<br><small>Pantai Senggigi</small></td>
                            <td><span class="badge-safe">Terbit</span></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" value="FBS-003"></td>
                            <td><div class="mini-poster" style="background-color: #555;"></div></td>
                            <td><strong>Festival Budaya Sasak</strong><br><small>ID: FBS-003</small></td>
                            <td>12 Juli 2026<br><small>Lapangan Mataram</small></td>
                            <td><span class="badge-urgent" style="color: #f1c40f;">Draft</span></td>
                        </tr>
                    </tbody>
                </table>
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