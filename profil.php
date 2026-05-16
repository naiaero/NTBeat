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
    <title>Profil Akun - NTBeat</title>
    <link rel="stylesheet" href="assets/style/style.css" />
    <script src="assets/script/script.js"></script>
  </head>
  <body>
    <nav class="header-user">
      <div
        class="logo-area"
        onclick="window.location.href = 'halaman-awal.php'"
      >
        <img src="assets/img/logo.png" alt="NTBeat Logo" />
        <label>NTBeat</label>
      </div>
      <div class="user-profile-nav">
        <span>Halo, <?php echo htmlspecialchars($nama_user); ?>!</span>
        <div class="avatar-placeholder" onclick="window.location.href = 'profil.php'"><?php echo $inisial; ?></div>
      </div>
    </nav>

    <div class="dashboard-layout">
      <aside class="sidebar">
        <ul class="sidebar-menu">
          <li onclick="window.location.href = 'halaman-awal.php'">
            📅 Daftar Konser
          </li>
          <li onclick="window.location.href = 'tiket-saya.php'">
            🎟️ Tiket Saya & Riwayat
          </li>
          <li class="active">⚙️ Profil Akun</li>
          <li onclick="openLogoutModal()">🚪 Keluar</li>
        </ul>
      </aside>

      <main class="content-area">
        <div class="ps-container">
          <div class="section-header">
            <h2>Pengaturan Profil</h2>
            <p>
              Ubah profil dan kata sandi anda untuk kenyamanan pengelolaan akun.
            </p>
          </div>

          <div class="ps-card">
            <div class="ps-avatar-section">
              <div class="ps-avatar-wrapper">
                <img src="assets/img/tds4.jpg" alt="Foto Profil" />
                <div class="ps-edit-icon">✏️</div>
              </div>
            </div>

            <form class="ps-form" action="#" id="profile-form">
              <div class="ps-form-group">
                <label for="username">Nama</label>
                <input
                  type="text"
                  id="username"
                  class="ps-input"
                  value=""
                />
              </div>

              <div class="ps-form-group">
                <label for="email">Alamat Email</label>
                <input
                  type="email"
                  id="email"
                  class="ps-input"
                  value=""
                />
              </div>

              <h3 class="ps-subheading">Ubah Kata Sandi</h3>

              <div class="ps-form-group">
                <label for="current_password">Password saat ini</label>
                <input
                  type="password"
                  id="current_password"
                  class="ps-input"
                  placeholder="Masukkan password lama"
                />
              </div>

              <div class="ps-form-group">
                <label for="new_password">Password baru</label>
                <input
                  type="password"
                  id="new_password"
                  class="ps-input"
                  placeholder="Masukkan password baru"
                />
              </div>

              <div class="ps-action-bar">
                <button
                  type="button"
                  class="btn-ps-cancel"
                  onclick="window.location.href = 'halaman-awal.php'"
                >
                  Cancel
                </button>
                <button type="submit" class="btn-ps-save">Save</button>
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
