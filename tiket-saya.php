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
  </body>
</html>
