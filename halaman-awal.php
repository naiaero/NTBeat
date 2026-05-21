<?php
session_start();
include 'koneksi.php';

// Membatasi akses hanya untuk customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
$inisial = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
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
      <div class="logo-area" onclick="window.location.href='halaman-awal.php'">
        <img src="assets/img/logo.png" alt="NTBeat Logo" />
        <label>NTBeat</label>
      </div>

      <input
        type="text"
        placeholder="Cari berdasarkan nama konser..."
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
          <li class="active" onclick="window.location.href = 'halaman-awal.php'">📅 Daftar Konser</li>
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
          <?php
          // Query konser yang statusnya selain 'Arsip'
          $query = mysqli_query($conn, "SELECT * FROM konser WHERE status != 'Arsip' ORDER BY id DESC");
          if (mysqli_num_rows($query) > 0) {
              while ($row = mysqli_fetch_assoc($query)) {
                  $sisa_tiket = intval($row['kapasitas']) - intval($row['tiket_terjual']);
                  
                  // Tentukan class/status sisa tiket
                  $badge_class = 'safe';
                  $sisa_text = 'Tersedia';
                  
                  if ($sisa_tiket <= 0 || $row['status'] === 'Habis') {
                      $badge_class = 'urgent';
                      $sisa_text = 'Habis';
                  } elseif ($sisa_tiket <= 150 || $row['status'] === 'Hampir Habis') {
                      $badge_class = 'urgent';
                      $sisa_text = "Tersisa " . number_format($sisa_tiket, 0, ',', '.') . "!";
                  }

                  // Format tanggal, waktu, harga
                  $tanggal_format = date('d M Y', strtotime($row['tanggal']));
                  $waktu_format = date('H:i', strtotime($row['waktu']));
                  $harga_format = "Rp " . number_format($row['harga'], 0, ',', '.');
                  
                  $poster_path = "assets/img/" . htmlspecialchars($row['poster']);
                  if (empty($row['poster']) || !file_exists($poster_path)) {
                      $poster_path = "assets/img/default-poster.jpg";
                  }
                  ?>
                  <div class="concert-card">
                    <div class="card-img-placeholder" style="background-image: url('<?php echo $poster_path; ?>'); background-size: cover; background-position: center; height: 180px; border-bottom: 1px solid #333;">
                      <?php if ($poster_path === 'assets/img/default-poster.jpg') { ?>
                        <span style="background: rgba(0,0,0,0.6); padding: 5px 10px; border-radius: 5px; color: #fff;">[Poster Konser]</span>
                      <?php } ?>
                    </div>

                    <div class="card-info">
                      <h3 class="concert-title"><?php echo htmlspecialchars($row['nama_konser']); ?></h3>
                      <div class="concert-details">
                        <p>📅 <?php echo $tanggal_format; ?> • <?php echo $waktu_format; ?> WITA</p>
                        <p>📍 <?php echo htmlspecialchars($row['lokasi']); ?></p>
                        <p class="concert-price"><?php echo $harga_format; ?></p>
                      </div>

                      <div class="ticket-stats">
                        <div class="stat-box capacity">
                          <span class="stat-label">Kapasitas</span>
                          <span class="stat-value"><?php echo number_format($row['kapasitas'], 0, ',', '.'); ?> org</span>
                        </div>
                        <div class="stat-box remaining <?php echo $badge_class; ?>">
                          <span class="stat-label">Sisa Tiket</span>
                          <span class="stat-value"><?php echo $sisa_text; ?></span>
                        </div>
                      </div>
                    </div>

                    <div class="card-footer">
                      <div class="card-actions">
                        <button
                          class="btn-detail"
                          onclick="window.location.href = 'detail-konser.php?id=<?php echo $row['id']; ?>'"
                        >
                          Detail
                        </button>
                        <button 
                          class="btn-beli" 
                          data-id="<?php echo $row['id']; ?>"
                          <?php if ($sisa_tiket <= 0 || $row['status'] === 'Habis') echo 'disabled style="opacity: 0.5; cursor: not-allowed;"'; ?>
                        >
                          Pesan
                        </button>
                      </div>
                    </div>
                  </div>
                  <?php
              }
          } else {
              echo "<div style='grid-column: 1/-1; text-align: center; color: #888; padding: 40px;'>Belum ada konser mendatang yang terdaftar.</div>";
          }
          ?>
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
