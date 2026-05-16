<?php
  session_start();
  $nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
  $inisial = strtoupper(substr($nama_user, 0, 1));
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Pengguna - NTBeat</title>
    <link rel="stylesheet" href="assets/style/style.css" />
    <script src="assets/script/script.js"></script>
  </head>
  <body>
    <nav class="header-user">
      <div class="logo-area">
        <img src="assets/img/logo.png" alt="NTBeat Logo" />
        <label>NTBeat</label>
      </div>

      <input
        type="text"
        placeholder="Cari berdasarkan nama artis..."
        class="search-bar"
      />

      <div class="user-profile-nav">
        <span>Halo, <?php echo htmlspecialchars($nama_user); ?>!</span>
        <div class="avatar-placeholder" onclick="window.location.href = 'profil.php'"><?php echo $inisial; ?></div>
      </div>
    </nav>

    <div class="dashboard-layout">
      <aside class="sidebar">
        <ul class="sidebar-menu">
          <li class="active">📅 Daftar Konser</li>
          <li onclick="window.location.href = 'tiket-saya.php'">
            🎟️ Tiket Saya & Riwayat
          </li>
          <li onclick="window.location.href = 'profil.php'">⚙️ Profil Akun</li>
          <li onclick="openLogoutModal()">🚪 Keluar</li>
        </ul>
      </aside>

      <main class="content-area">
        <div class="section-header">
          <h2>Konser Mendatang</h2>
          <p>Segera amankan tiketmu sebelum kehabisan!</p>
        </div>

        <div class="concert-grid">
          <div class="concert-card">
            <div class="card-img-placeholder">
              <span>[Poster Konser]</span>
            </div>

            <div class="card-info">
              <h3 class="concert-title">Symphony of Lombok</h3>
              <div class="concert-details">
                <p>📅 15 Mei 2026 • 19:00 WITA</p>
                <p>📍 Taman Budaya NTB, Mataram</p>
                <p class="concert-price">Rp 250.000</p>
              </div>

              <div class="ticket-stats">
                <div class="stat-box capacity">
                  <span class="stat-label">Kapasitas</span>
                  <span class="stat-value">5.000 org</span>
                </div>
                <div class="stat-box remaining urgent">
                  <span class="stat-label">Sisa Tiket</span>
                  <span class="stat-value">Tersisa 45!</span>
                </div>
              </div>
            </div>

            <div class="card-footer">
              <div class="card-actions">
                <button
                  class="btn-detail"
                  onclick="window.location.href = 'detail-konser.php'"
                >
                  Detail
                </button>
                <button class="btn-beli">Pesan</button>
              </div>
            </div>
          </div>

          <div class="concert-card">
            <div class="card-img-placeholder">
              <span>[Poster Konser]</span>
            </div>

            <div class="card-info">
              <h3 class="concert-title">NCT Dream Live on Screen</h3>
              <div class="concert-details">
                <p>📅 20 Juni 2026 • 18:30 WITA</p>
                <p>📍 Epicentrum Mall Atrium</p>
                <p class="concert-price">Rp 150.000</p>
              </div>

              <div class="ticket-stats">
                <div class="stat-box capacity">
                  <span class="stat-label">Kapasitas</span>
                  <span class="stat-value">2.000 org</span>
                </div>
                <div class="stat-box remaining safe">
                  <span class="stat-label">Sisa Tiket</span>
                  <span class="stat-value">Tersedia</span>
                </div>
              </div>
            </div>

            <div class="card-footer">
              <div class="card-actions">
                <button
                  class="btn-detail"
                  onclick="window.location.href = 'detail-konser.php'"
                >
                  Detail
                </button>
                <button class="btn-beli">Pesan</button>
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
