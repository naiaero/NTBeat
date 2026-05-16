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
            <div class="avatar-placeholder" onclick="openProfileModal()">S</div>
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

    <div id="profileModal" class="modal-overlay">
        <div class="profile-card" style="background-color: #1e1e1e; padding: 40px; border-radius: 20px; text-align: center; width: 400px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);">
            <div class="profile-img" style="width: 100px; height: 100px; background-color: #555; border-radius: 50%; margin: 0 auto 20px; display: flex; justify-content: center; align-items: center; font-size: 40px; color: white;">S</div>
            <h2 style="color: white;">Nama Pengguna</h2>

            <div class="profile-info" style="text-align: left; margin-top: 20px; color: white;">
                <div class="info-row" style="margin-bottom: 15px; display: flex;">
                    <span class="label" style="width: 120px; font-weight: bold; color: #ccc;">Email</span>
                    <span>: nama@gmail.com</span>
                </div>
                <div class="info-row" style="margin-bottom: 15px; display: flex; align-items: center;">
                    <span class="label" style="width: 120px; font-weight: bold; color: #ccc;">Password</span>
                    <span>: **********</span>
                    <button type="button" class="toggle-btn" onclick="togglePassword()" style="background: none; border: 1px solid #555; color: #ccc; cursor: pointer; margin-left: 20px; font-size: 11px; padding: 2px 6px; border-radius: 4px;">Lihat</button>
                </div>
            </div>

            <button class="btn-back" onclick="closeProfileModal()" style="background-color: #a32424; color: white; border: none; padding: 10px 30px; border-radius: 8px; margin-top: 20px; cursor: pointer; float: right;">Back</button>
        </div>
    </div>
</body>
</html>