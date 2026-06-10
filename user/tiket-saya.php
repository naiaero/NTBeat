<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

// ==========================================
// AUTO CANCEL TICKETS (Kedaluwarsa VA 30 Menit)
// ==========================================
$stmt_cancel = $conn->prepare("SELECT id, konser_id, jumlah_tiket FROM pesanan WHERE status_bayar = 'Pending' AND waktu_kadaluarsa < NOW()");
$stmt_cancel->execute();
$res_cancel = $stmt_cancel->get_result();

while ($row = $res_cancel->fetch_assoc()) {
    $p_id = $row['id'];
    $k_id = $row['konser_id'];
    $jml = $row['jumlah_tiket'];
    
    // 1. Batalkan pesanan
    $update_pesanan = $conn->prepare("UPDATE pesanan SET status_bayar = 'Dibatalkan' WHERE id = ?");
    $update_pesanan->bind_param("i", $p_id);
    $update_pesanan->execute();
    
    // 2. Kembalikan stok
    $update_konser = $conn->prepare("UPDATE konser SET tiket_terjual = GREATEST(tiket_terjual - ?, 0) WHERE id = ?");
    $update_konser->bind_param("ii", $jml, $k_id);
    $update_konser->execute();

    // 3. Update status konser (jika tadinya Habis, menjadi Tersedia)
    $cek_konser = $conn->prepare("SELECT kapasitas, tiket_terjual, status FROM konser WHERE id = ?");
    $cek_konser->bind_param("i", $k_id);
    $cek_konser->execute();
    $res_konser = $cek_konser->get_result()->fetch_assoc();
    
    if ($res_konser && $res_konser['status'] !== 'Selesai' && $res_konser['status'] !== 'Arsip') {
        $sisa = $res_konser['kapasitas'] - $res_konser['tiket_terjual'];
        $new_status = $res_konser['status'];
        if ($sisa > 150) {
            $new_status = 'Tersedia';
        } elseif ($sisa > 0) {
            $new_status = 'Hampir Habis';
        }
        
        if ($new_status !== $res_konser['status']) {
            $update_status = $conn->prepare("UPDATE konser SET status = ? WHERE id = ?");
            $update_status->bind_param("si", $new_status, $k_id);
            $update_status->execute();
        }
    }
}
// ==========================================

$user_email = $_SESSION['email'];
$stmt_user = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt_user->bind_param("s", $user_email);
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
$stmt_active = $conn->prepare("
    SELECT p.*, k.nama_konser, k.tanggal, k.waktu, k.lokasi 
    FROM pesanan p
    JOIN konser k ON p.konser_id = k.id
    WHERE p.user_email = ? AND k.status NOT IN ('Arsip', 'Selesai') AND k.tanggal >= CURDATE()
    ORDER BY p.id DESC
");
$stmt_active->bind_param("s", $user_email);
$stmt_active->execute();
$query_active = $stmt_active->get_result();

// Riwayat tiket: status konser 'Arsip'/'Selesai' atau tanggal sudah lewat
$stmt_past = $conn->prepare("
    SELECT p.*, k.nama_konser, k.tanggal, k.waktu, k.lokasi 
    FROM pesanan p
    JOIN konser k ON p.konser_id = k.id
    WHERE p.user_email = ? AND (k.status IN ('Arsip', 'Selesai') OR k.tanggal < CURDATE())
    ORDER BY p.id DESC
");
$stmt_past->bind_param("s", $user_email);
$stmt_past->execute();
$query_past = $stmt_past->get_result();
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tiket Saya & Riwayat - NTBeat</title>
    <link rel="stylesheet" href="../assets/style/style.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="../assets/script/script.js"></script>
  </head>
  <body>
    <nav class="header-user">
      <div class="logo-area" onclick="window.location.href = 'beranda.php'">
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
          <li class="active">Tiket Saya & Riwayat</li>
          <li onclick="window.location.href = 'profil.php'">Profil Akun</li>
          <li onclick="openLogoutModal()">Keluar</li>
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
                      $status_bayar = $row['status_bayar'];
                      ?>
                      <div class="e-ticket-card">
                        <div class="ticket-main-info">
                          <div class="ticket-header">
                            <?php if ($status_bayar === 'Lunas') { ?>
                                <span class="badge-active">Tiket Aktif</span>
                            <?php } elseif ($status_bayar === 'Pending') { ?>
                                <span class="badge-active" style="background: #f39c12; color: #fff;">Menunggu Pembayaran</span>
                            <?php } elseif ($status_bayar === 'Menunggu Verifikasi') { ?>
                                <span class="badge-active" style="background: #3498db; color: #fff;">Menunggu Verifikasi</span>
                            <?php } else { ?>
                                <span class="badge-expired" style="background: #e74c3c; color: #fff;">Dibatalkan</span>
                            <?php } ?>
                            <span class="order-id">#<?php echo htmlspecialchars($row['order_id']); ?></span>
                          </div>
                          <h3><?php echo htmlspecialchars($row['nama_konser']); ?></h3>
                          <p class="ticket-detail">📅 <?php echo format_indonesian_date($row['tanggal']); ?> • <?php echo date('H:i', strtotime($row['waktu'])); ?> WITA</p>
                          <p class="ticket-detail">📍 <?php echo htmlspecialchars($row['lokasi']); ?></p>
                          <p class="ticket-detail">👤 Nama: <?php echo htmlspecialchars($nama_user); ?> (<?php echo $row['jumlah_tiket']; ?> Tiket)</p>

                          <?php if ($status_bayar === 'Pending' || $status_bayar === 'Menunggu Verifikasi') { ?>
                            <div style="margin-top: 15px; padding: 15px; background: #1a1a1a; border: 1px solid #333; border-radius: 8px;">
                                <?php if ($status_bayar === 'Menunggu Verifikasi') { ?>
                                    <p style="color: #3498db; font-weight: bold; margin: 0;">✅ Bukti bayar terunggah. Menunggu Verifikasi.</p>
                                <?php } else { ?>
                                    <p style="margin: 0 0 10px 0;">Transfer sebesar <strong>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></strong> ke <?php echo htmlspecialchars($row['bank']); ?> Virtual Account:</p>
                                    <p style="font-size: 1.5rem; letter-spacing: 2px; color: #f1c40f; margin: 0 0 10px 0; font-weight: bold;"><?php echo htmlspecialchars($row['va_number']); ?></p>
                                    <p style="margin: 0 0 15px 0; font-size: 0.9rem; color: #e74c3c;">Batas Waktu: <strong id="countdown-<?php echo $row['id']; ?>">...</strong></p>
                                    <script>
                                        (function() {
                                            var distance = <?php echo strtotime($row['waktu_kadaluarsa']) - time(); ?> * 1000;
                                            var x = setInterval(function() {
                                                distance -= 1000;
                                                if (distance < 0) {
                                                    clearInterval(x);
                                                    document.getElementById("countdown-<?php echo $row['id']; ?>").innerHTML = "KADALUARSA";
                                                    setTimeout(function() { location.reload(); }, 2000);
                                                } else {
                                                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                    document.getElementById("countdown-<?php echo $row['id']; ?>").innerHTML = minutes + "m " + seconds + "s ";
                                                }
                                            }, 1000);
                                        })();
                                    </script>
                                    <form action="../actions/upload-bukti-proses.php" method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                        <input type="file" name="bukti_bayar" accept="image/*" required style="font-size: 0.8rem;">
                                        <button type="submit" class="auth-btn-submit" style="padding: 8px 15px; font-size: 0.9rem; width: auto; margin: 0;">Unggah</button>
                                    </form>
                                <?php } ?>
                            </div>
                          <?php } ?>
                        </div>

                        <div class="ticket-qr-section">
                          <?php if ($status_bayar === 'Lunas') { ?>
                              <div class="qr-placeholder" data-code="<?php echo htmlspecialchars($row['order_id']); ?>">
                                <span>[QR Code]</span>
                              </div>
                          <?php } elseif ($status_bayar === 'Pending' || $status_bayar === 'Menunggu Verifikasi') { ?>
                              <div class="qr-placeholder expired" style="border-color: #f39c12; color: #f39c12;">
                                <span>[Menunggu]</span>
                              </div>
                          <?php } else { ?>
                              <div class="qr-placeholder expired" style="border-color: #e74c3c; color: #e74c3c;">
                                <span>[Dibatalkan]</span>
                              </div>
                          <?php } ?>
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
                          <div class="qr-placeholder expired" data-code="<?php echo htmlspecialchars($row['order_id']); ?>">
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
                <button class="btn-yakin" onclick="window.location.href = '../auth/logout.php'">Keluar</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.qr-placeholder').forEach(el => {
            const code = el.getAttribute('data-code');
            if (code) {
                el.innerHTML = "";
                new QRCode(el, {
                    text: code,
                    width: 90,
                    height: 90,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            }
        });
    });
    </script>
  </body>
</html>
