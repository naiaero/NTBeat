<?php
session_start();
include '../config/koneksi.php';

// Catat pengunjung
if (!isset($_SESSION['visited_today'])) {
    $_SESSION['visited_today'] = true;
    $today = date('Y-m-d');
    mysqli_query($conn, "INSERT INTO pengunjung (tanggal, jumlah) VALUES ('$today', 1) ON DUPLICATE KEY UPDATE jumlah = jumlah + 1");
}

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
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Pengguna - NTBeat</title>
    <link rel="stylesheet" href="../assets/style/style.css" />
    <script src="../assets/script/script.js"></script>
  </head>
  <body>
    <nav class="header-user">
      <div class="logo-area" onclick="window.location.href='beranda.php'">
        <img src="../assets/img/logo.png" alt="NTBeat Logo" />
        <label>NTBeat</label>
      </div>

      <input
        type="text"
        placeholder="Cari berdasarkan nama konser..."
        class="search-bar"
      />

      <div class="user-profile-nav">
        <span>Halo, <?php echo htmlspecialchars($nama_depan); ?>!</span>
        <div class="avatar-placeholder" onclick="window.location.href = 'profil.php'" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
      </div>
    </nav>

    <div class="dashboard-layout">
      <aside class="sidebar">
        <ul class="sidebar-menu">
          <li class="active" onclick="window.location.href = 'beranda.php'">Daftar Konser</li>
          <li onclick="window.location.href = 'tiket-saya.php'">Tiket Saya & Riwayat</li>
          <li onclick="window.location.href = 'profil.php'">Profil Akun</li>
          <li onclick="openLogoutModal()">Keluar</li>
        </ul>
      </aside>

      <main class="content-area">
        <div class="section-header">
          <h2>Konser Mendatang</h2>
          <p>Segera amankan tiketmu sebelum kehabisan!</p>
        </div>

        <div class="concert-grid">
          <?php
          // Query konser yang statusnya selain 'Arsip' dan join untuk dapatkan nama EO
          $query = mysqli_query($conn, "
              SELECT k.*, u.nama as nama_eo 
              FROM konser k 
              LEFT JOIN users u ON k.eo_email = u.email 
              WHERE k.status != 'Arsip' 
              ORDER BY k.id DESC
          ");
          if (mysqli_num_rows($query) > 0) {
              while ($row = mysqli_fetch_assoc($query)) {
                  $sisa_tiket = intval($row['kapasitas']) - intval($row['tiket_terjual']);
                  
                  date_default_timezone_set('Asia/Makassar');
                  $waktu_konser = strtotime($row['tanggal'] . ' ' . $row['waktu']);
                  $sudah_lewat = ($waktu_konser < time());

                  // Tentukan class/status sisa tiket
                  $badge_class = 'safe';
                  $sisa_text = number_format($sisa_tiket, 0, ',', '.') . " org";
                  
                  if ($sudah_lewat) {
                      $badge_class = 'urgent';
                      $sisa_text = 'Berakhir';
                  } elseif ($sisa_tiket <= 0 || $row['status'] === 'Habis') {
                      $badge_class = 'urgent';
                      $sisa_text = 'Habis';
                  } elseif ($sisa_tiket <= 150 || $row['status'] === 'Hampir Habis') {
                      $badge_class = 'urgent';
                      $sisa_text = "Tersisa " . number_format($sisa_tiket, 0, ',', '.') . " org!";
                  }

                  // Format tanggal, waktu, harga
                  $tanggal_format = date('d M Y', strtotime($row['tanggal']));
                  $waktu_format = date('H:i', strtotime($row['waktu']));
                  $harga_format = "Rp " . number_format($row['harga'], 0, ',', '.');
                  
                  $poster_path = "../assets/img/" . htmlspecialchars($row['poster']);
                  if (empty($row['poster']) || !file_exists($poster_path)) {
                      $poster_path = "../assets/img/default-poster.png";
                  }
                  ?>
                  <div class="concert-card">
                    <div class="card-img-placeholder" style="background-image: url('<?php echo $poster_path; ?>'); background-size: cover; background-position: center; height: 180px; border-bottom: 1px solid #333;">
                      <?php if ($poster_path === '../assets/img/default-poster.png') { ?>
                        <span style="background: rgba(0,0,0,0.6); padding: 5px 10px; border-radius: 5px; color: #fff;">[Poster Konser]</span>
                      <?php } ?>
                    </div>

                    <div class="card-info">
                      <h3 class="concert-title"><?php echo htmlspecialchars($row['nama_konser']); ?></h3>
                      <div class="concert-details">
                        <p style="color: #f1c40f; font-weight: bold; font-size: 0.9rem; margin-bottom: 5px;">🏢 <?php echo htmlspecialchars($row['nama_eo'] ?? 'Penyelenggara Tidak Diketahui'); ?></p>
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
                          <?php if ($sudah_lewat || $sisa_tiket <= 0 || $row['status'] === 'Habis') echo 'disabled style="opacity: 0.5; cursor: not-allowed;"'; ?>
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
                <button class="btn-yakin" onclick="window.location.href = '../auth/logout.php'">Keluar</button>
            </div>
        </div>
    </div>
  </body>
</html>
