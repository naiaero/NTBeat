<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

$email_user = $_SESSION['email'];
$stmt_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt_user->bind_param("s", $email_user);
$stmt_user->execute();
$query_user = $stmt_user->get_result();
$user_data = mysqli_fetch_assoc($query_user);
$nama_user = $user_data['nama'];
$nama_depan = explode(' ', trim($nama_user))[0];
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Akun - NTBeat</title>
    <link rel="stylesheet" href="../assets/style/style.css" />
    <script src="../assets/script/script.js"></script>
  </head>
  <body>
    <nav class="header-user">
      <div
        class="logo-area"
        onclick="window.location.href = 'beranda.php'"
      >
        <img src="../assets/img/logo.png" alt="NTBeat Logo" />
        <label>NTBeat</label>
      </div>
      <div class="user-profile-nav">
        <span>Halo, <?php echo htmlspecialchars($nama_depan); ?>!</span>
        <div class="avatar-placeholder" onclick="window.location.href = 'profil.php'" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
      </div>
    </nav>

    <div class="dashboard-layout">
      <aside class="sidebar">
        <ul class="sidebar-menu">
          <li onclick="window.location.href = 'beranda.php'">Daftar Konser</li>
          <li onclick="window.location.href = 'tiket-saya.php'">Tiket Saya & Riwayat</li>
          <li class="active">Profil Akun</li>
          <li onclick="openLogoutModal()">Keluar</li>
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
            <form class="ps-form" action="../actions/update-profile-proses.php" method="POST" enctype="multipart/form-data" id="profile-form">
              <div class="ps-avatar-section">
                <div class="ps-avatar-wrapper">
                  <div id="avatar-preview" style="width: 100%; height: 100%; border-radius: 50%; border: 3px solid #5d1016; display: flex; align-items: center; justify-content: center; background-color: #333; color: #d4af37; font-size: 40px; font-weight: bold; <?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
                  <label for="avatar-input" class="ps-edit-icon">✏️</label>
                  <input type="file" id="avatar-input" name="foto" style="display: none;" accept="image/*" />
                </div>
              </div>

              <div class="ps-form-group">
                <label for="username">Nama</label>
                <input
                  type="text"
                  id="username"
                  name="nama"
                  class="ps-input"
                  value="<?php echo htmlspecialchars($nama_user); ?>"
                  required
                />
              </div>

              <div class="ps-form-group">
                <label for="email">Alamat Email</label>
                <input
                  type="email"
                  id="email"
                  class="ps-input"
                  value="<?php echo htmlspecialchars($_SESSION['email']); ?>"
                  readonly
                  style="background-color: #222; color: #888; cursor: not-allowed;"
                />
              </div>

              <h3 class="ps-subheading">Ubah Kata Sandi</h3>

              <div class="ps-form-group">
                <label for="current_password">Password saat ini</label>
                <input
                  type="password"
                  id="current_password"
                  name="current_password"
                  class="ps-input"
                  placeholder="Masukkan password lama"
                />
              </div>

              <div class="ps-form-group">
                <label for="new_password">Password baru</label>
                <input
                  type="password"
                  id="new_password"
                  name="new_password"
                  class="ps-input"
                  placeholder="Masukkan password baru"
                />
              </div>

              <div class="ps-action-bar">
                <button
                  type="button"
                  class="btn-ps-cancel"
                  onclick="window.location.href = 'beranda.php'"
                >
                  Batal
                </button>
                <button type="submit" class="btn-ps-save">Simpan</button>
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
