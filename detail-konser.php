<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Konser - NTBeat</title>
    <link rel="stylesheet" href="assets/style/style.css">
    <script src="assets/script/script.js"></script>
</head>
<body>
    <nav class="header-user">
        <div class="logo-area" onclick="window.location.href='halaman-user.php'">
            <img src="assets/img/logo.png" alt="NTBeat Logo">
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav">
            <span>Halo, Salsabila!</span>
            <div class="avatar-placeholder" onclick="window.location.href = 'profil.php'">S</div>
        </div>
    </nav>

    <main class="detail-container">
        <div class="detail-poster">
            <div class="poster-placeholder">
                <span>[Poster Symphony of Lombok]</span>
            </div>
        </div>

        <div class="detail-info">
            <a href="halaman-awal.html" class="back-link">← Kembali ke Beranda</a>
            
            <h1 class="detail-title">Symphony of Lombok</h1>
            
            <div class="detail-meta">
                <p>📅 <strong>Tanggal & Waktu:</strong> 15 Mei 2026 • 19:00 WITA</p>
                <p>📍 <strong>Tempat:</strong> Taman Budaya NTB, Mataram</p>
                <p>👥 <strong>Kategori:</strong> Hiburan / Musik Orkestra</p>
            </div>

            <div class="detail-desc">
                <h3>Tentang Acara</h3>
                <p>Rasakan harmoni alam dan budaya dalam balutan musik orkestra yang memukau. Symphony of Lombok menghadirkan kolaborasi musisi lokal dan nasional untuk menyajikan aransemen lagu-lagu daerah Nusa Tenggara Barat dengan gaya klasik kontemporer. Jangan lewatkan malam yang penuh keajaiban ini!</p>
            </div>

            <div class="ticket-stats large-stats">
                <div class="stat-box capacity">
                    <span class="stat-label">Total Kapasitas</span>
                    <span class="stat-value">5.000 Orang</span>
                </div>
                <div class="stat-box remaining urgent">
                    <span class="stat-label">Sisa Tiket Tersedia</span>
                    <span class="stat-value">Hanya 45 Tiket!</span>
                </div>
            </div>

            <div class="checkout-box">
                <div class="price-area">
                    <span class="price-label">Harga Tiket</span>
                    <span class="price-total">Rp 250.000</span>
                </div>
                <button class="btn-pesan-sekarang" onclick="konfirmasiPembelian()">Pesan Tiket Sekarang</button>
            </div>
        </div>
    </main>
</body>
</html>