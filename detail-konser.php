<?php
session_start();
include 'koneksi.php';

// Membatasi akses hanya untuk customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$id_konser = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = mysqli_query($conn, "SELECT * FROM konser WHERE id = $id_konser AND status != 'Arsip'");
if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
} else {
    echo "<script>
            alert('Konser tidak ditemukan atau sudah tidak aktif.');
            window.location.href = 'halaman-awal.php';
          </script>";
    exit();
}

$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
$inisial = strtoupper(substr($nama_user, 0, 1));

$sisa_tiket = intval($row['kapasitas']) - intval($row['tiket_terjual']);
$badge_class = 'safe';
$sisa_text = 'Tersedia';

if ($sisa_tiket <= 0 || $row['status'] === 'Habis') {
    $badge_class = 'urgent';
    $sisa_text = 'Habis';
} elseif ($sisa_tiket <= 150 || $row['status'] === 'Hampir Habis') {
    $badge_class = 'urgent';
    $sisa_text = "Hanya " . number_format($sisa_tiket, 0, ',', '.') . " Tiket!";
}

$poster_path = "assets/img/" . htmlspecialchars($row['poster']);
if (empty($row['poster']) || !file_exists($poster_path)) {
    $poster_path = "assets/img/default-poster.jpg";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Konser - <?php echo htmlspecialchars($row['nama_konser']); ?></title>
    <link rel="stylesheet" href="assets/style/style.css">
    <script src="assets/script/script.js"></script>
</head>
<body>
    <nav class="header-user">
        <div class="logo-area" onclick="window.location.href='halaman-awal.php'">
            <img src="assets/img/logo.png" alt="NTBeat Logo">
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav">
            <span>Halo, <?php echo htmlspecialchars($nama_user); ?>!</span>
            <div class="avatar-placeholder" onclick="window.location.href = 'profil.php'"><?php echo $inisial; ?></div>
        </div>
    </nav>

    <main class="detail-container">
        <div class="detail-poster">
            <div class="poster-display" style="width: 100%; height: 500px; background-image: url('<?php echo $poster_path; ?>'); background-size: cover; background-position: center; border-radius: 15px; border: 1px solid #333; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
            </div>
        </div>

        <div class="detail-info">
            <a href="halaman-awal.php" class="back-link">← Kembali ke Beranda</a>
            
            <h1 class="detail-title"><?php echo htmlspecialchars($row['nama_konser']); ?></h1>
            
            <div class="detail-meta">
                <p>📅 <strong>Tanggal & Waktu:</strong> <?php echo date('d M Y', strtotime($row['tanggal'])); ?> • <?php echo date('H:i', strtotime($row['waktu'])); ?> WITA</p>
                <p>📍 <strong>Tempat:</strong> <?php echo htmlspecialchars($row['lokasi']); ?></p>
                <p>👥 <strong>Line-up:</strong> <?php echo empty($row['lineup']) ? 'Musisi Pilihan' : htmlspecialchars($row['lineup']); ?></p>
            </div>

            <div class="detail-desc">
                <h3>Tentang Acara</h3>
                <p>Nikmati perhelatan musik yang spektakuler bersama kami. Saksikan penampilan menawan dari para artis favorit Anda dalam atmosfer konser yang luar biasa. NTBeat menjamin kemudahan akses tiket serta kenyamanan transaksi Anda.</p>
            </div>

            <div class="ticket-stats large-stats">
                <div class="stat-box capacity">
                    <span class="stat-label">Total Kapasitas</span>
                    <span class="stat-value"><?php echo number_format($row['kapasitas'], 0, ',', '.'); ?> Orang</span>
                </div>
                <div class="stat-box remaining <?php echo $badge_class; ?>">
                    <span class="stat-label">Sisa Tiket Tersedia</span>
                    <span class="stat-value"><?php echo $sisa_text; ?></span>
                </div>
            </div>

            <div class="checkout-box">
                <div class="price-area">
                    <span class="price-label">Harga Tiket</span>
                    <span class="price-total">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                </div>
                <?php if ($sisa_tiket > 0 && $row['status'] !== 'Habis') { ?>
                    <button class="btn-pesan-sekarang" onclick="bookingTiket()">Pesan Tiket Sekarang</button>
                <?php } else { ?>
                    <button class="btn-pesan-sekarang" disabled style="background-color: #555; color: #888; cursor: not-allowed;">Tiket Habis</button>
                <?php } ?>
            </div>
        </div>
    </main>

    <form id="booking-form" action="pesan-tiket-proses.php" method="POST" style="display:none;">
        <input type="hidden" name="konser_id" value="<?php echo $row['id']; ?>">
        <input type="hidden" name="jumlah_tiket" value="1">
        <input type="hidden" name="total_harga" value="<?php echo $row['harga']; ?>">
    </form>

    <script>
    function bookingTiket() {
        const namaKonser = <?php echo json_encode($row['nama_konser']); ?>;
        const harga = <?php echo json_encode("Rp " . number_format($row['harga'], 0, ',', '.')); ?>;
        const yakin = confirm(`Konfirmasi Pesanan:\n\nAcara: ${namaKonser}\nTotal: ${harga}\n\nApakah Anda ingin melanjutkan ke pembayaran?`);
        if (yakin) {
            document.getElementById('booking-form').submit();
        }
    }
    </script>
</body>
</html>