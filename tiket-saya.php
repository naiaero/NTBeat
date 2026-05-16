<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tiket Saya & Riwayat - NTBeat</title>
    <link rel="stylesheet" href="assets/style/style.css" />
    <script src="assets/script/script.js"></script>
  </head>
  <body>
    <nav class="header-user">
      <div class="logo-area" onclick="window.location.href = 'halaman-user.php'">
        <img src="assets/img/logo.png" alt="NTBeat Logo" />
        <label>NTBeat</label>
      </div>
      <div class="user-profile-nav">
        <span>Halo, Salsabila!</span>
        <div class="avatar-placeholder" onclick="openProfileModal()">S</div>
      </div>
    </nav>

    <div class="dashboard-layout">
      <aside class="sidebar">
        <ul class="sidebar-menu">
          <li onclick="window.location.href = 'halaman-awal.php'">
            📅 Daftar Konser
          </li>
          <li class="active">🎟️ Tiket Saya & Riwayat</li>

          <li onclick="window.location.href = 'profil.php'">⚙️ Profil Akun</li>

          <li onclick="openLogoutModal()">🚪 Keluar</li>
        </ul>
      </aside>

      <main class="content-area">
        <div class="tickets-split-view">
          <div class="ticket-column">
            <div class="section-header">
              <h2>Tiket Aktif Saya</h2>
              <p>Tunjukkan e-ticket pada petugas saat hari H.</p>
            </div>

            <div class="e-ticket-list">
              <div class="e-ticket-card">
                <div class="ticket-main-info">
                  <div class="ticket-header">
                    <span class="badge-active">Tiket Aktif</span>
                    <span class="order-id">#NTB-2605-88XQ</span>
                  </div>
                  <h3>Symphony of Lombok</h3>
                  <p class="ticket-detail">📅 Jumat, 15 Mei 2026</p>
                  <p class="ticket-detail">📍 Taman Budaya NTB, Mataram</p>
                  <p class="ticket-detail">👤 Nama: Salsabila Nailafahdi</p>
                </div>

                <div class="ticket-qr-section">
                  <div class="qr-placeholder">
                    <span>[QR Code]</span>
                  </div>
                  <button class="btn-unduh">Unduh PDF</button>
                </div>
              </div>
            </div>
          </div>

          <div class="ticket-column">
            <div class="section-header">
              <h2>Riwayat Pembelian</h2>
              <p>Daftar acara yang sudah pernah dihadiri.</p>
            </div>

            <div class="e-ticket-list">
              <div class="e-ticket-card expired-ticket">
                <div class="ticket-main-info">
                  <div class="ticket-header">
                    <span class="badge-expired">Selesai</span>
                    <span class="order-id">#NTB-2512-12AA</span>
                  </div>
                  <h3>NCT Dream Live on Screen</h3>
                  <p class="ticket-detail">📅 Sabtu, 20 Desember 2025</p>
                  <p class="ticket-detail">📍 Epicentrum Mall Atrium</p>
                  <p class="ticket-detail">👤 Nama: Salsabila Nailafahdi</p>
                </div>

                <div class="ticket-qr-section">
                  <div
                    class="qr-placeholder"
                    style="background-color: #e0e0e0; color: #888"
                  >
                    <span>[Digunakan]</span>
                  </div>
                </div>
              </div>
            </div>
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
