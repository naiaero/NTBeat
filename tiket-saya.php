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
$user_email = $_SESSION['email'];

function format_indonesian_date($date_str) {
    $days = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    $months = [
        'Jan' => 'Januari',
        'Feb' => 'Februari',
        'Mar' => 'Maret',
        'Apr' => 'April',
        'May' => 'Mei',
        'Jun' => 'Juni',
        'Jul' => 'Juli',
        'Aug' => 'Agustus',
        'Sep' => 'September',
        'Oct' => 'Oktober',
        'Nov' => 'November',
        'Dec' => 'Desember'
    ];
    $time = strtotime($date_str);
    $day = $days[date('l', $time)];
    $m = $months[date('M', $time)];
    $d = date('d', $time);
    $y = date('Y', $time);
    return "$day, $d $m $y";
}

// Tiket aktif: status konser bukan 'Arsip'/'Selesai' dan tanggal belum lewat
$query_active = mysqli_query($conn, "
    SELECT p.*, k.nama_konser, k.tanggal, k.waktu, k.lokasi 
    FROM pesanan p
    JOIN konser k ON p.konser_id = k.id
    WHERE p.user_email = '$user_email' AND k.status NOT IN ('Arsip', 'Selesai') AND k.tanggal >= CURDATE()
    ORDER BY p.id DESC
");

// Riwayat tiket: status konser 'Arsip'/'Selesai' atau tanggal sudah lewat
$query_past = mysqli_query($conn, "
    SELECT p.*, k.nama_konser, k.tanggal, k.waktu, k.lokasi 
    FROM pesanan p
    JOIN konser k ON p.konser_id = k.id
    WHERE p.user_email = '$user_email' AND (k.status IN ('Arsip', 'Selesai') OR k.tanggal < CURDATE())
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
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
      <div class="logo-area" onclick="window.location.href = 'halaman-awal.php'">
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
              <?php
              if (mysqli_num_rows($query_active) > 0) {
                  while ($row = mysqli_fetch_assoc($query_active)) {
                      ?>
                      <div class="e-ticket-card">
                        <div class="ticket-main-info">
                          <div class="ticket-header">
                            <span class="badge-active">Tiket Aktif</span>
                            <span class="order-id">#<?php echo htmlspecialchars($row['order_id']); ?></span>
                          </div>
                          <h3><?php echo htmlspecialchars($row['nama_konser']); ?></h3>
                          <p class="ticket-detail">📅 <?php echo format_indonesian_date($row['tanggal']); ?> • <?php echo date('H:i', strtotime($row['waktu'])); ?> WITA</p>
                          <p class="ticket-detail">📍 <?php echo htmlspecialchars($row['lokasi']); ?></p>
                          <p class="ticket-detail">👤 Nama: <?php echo htmlspecialchars($nama_user); ?> (<?php echo $row['jumlah_tiket']; ?> Tiket)</p>
                        </div>

                        <div class="ticket-qr-section">
                          <div class="qr-placeholder">
                            <span>[QR Code]</span>
                          </div>
                          <button class="btn-unduh" onclick="unduhTiket(this, '<?php echo $row['order_id']; ?>', '<?php echo addslashes($row['nama_konser']); ?>')">Unduh PDF</button>
                        </div>
                      </div>
                      <?php
                  }
              } else {
                  echo "<p style='color: #888; text-align: center; padding: 20px;'>Belum ada tiket aktif.</p>";
              }
              ?>
            </div>
          </div>

          <div class="ticket-column">
            <div class="section-header">
              <h2>Riwayat Pembelian</h2>
              <p>Daftar acara yang sudah pernah dihadiri.</p>
            </div>

            <div class="e-ticket-list">
              <?php
              if (mysqli_num_rows($query_past) > 0) {
                  while ($row = mysqli_fetch_assoc($query_past)) {
                      ?>
                      <div class="e-ticket-card expired-ticket">
                        <div class="ticket-main-info">
                          <div class="ticket-header">
                            <span class="badge-expired">Selesai</span>
                            <span class="order-id">#<?php echo htmlspecialchars($row['order_id']); ?></span>
                          </div>
                          <h3><?php echo htmlspecialchars($row['nama_konser']); ?></h3>
                          <p class="ticket-detail">📅 <?php echo format_indonesian_date($row['tanggal']); ?> • <?php echo date('H:i', strtotime($row['waktu'])); ?> WITA</p>
                          <p class="ticket-detail">📍 <?php echo htmlspecialchars($row['lokasi']); ?></p>
                          <p class="ticket-detail">👤 Nama: <?php echo htmlspecialchars($nama_user); ?> (<?php echo $row['jumlah_tiket']; ?> Tiket)</p>
                        </div>

                        <div class="ticket-qr-section">
                          <div class="qr-placeholder" style="background-color: #e0e0e0; color: #888">
                            <span>[Digunakan]</span>
                          </div>
                        </div>
                      </div>
                      <?php
                  }
              } else {
                  echo "<p style='color: #888; text-align: center; padding: 20px;'>Belum ada riwayat konser.</p>";
              }
              ?>
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
                <button class="btn-yakin" onclick="window.location.href = 'logout.php'">Keluar</button>
            </div>
        </div>
    </div>

    <script>
    function unduhTiket(btn, orderId, title) {
        btn.innerText = "Mengunduh...";
        btn.disabled = true;

        setTimeout(() => {
            alert(`E-Ticket ${title} (#${orderId}) berhasil diunduh dalam format PDF.`);
            btn.innerText = "Unduh PDF";
            btn.disabled = false;
        }, 1500);
    }
    </script>
  </body>
</html>
