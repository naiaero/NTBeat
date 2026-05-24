<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

$id_konser = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = mysqli_query($conn, "SELECT * FROM konser WHERE id = $id_konser AND status != 'Arsip'");
if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
} else {
    echo "<script>
            alert('Konser tidak ditemukan atau sudah tidak aktif.');
            window.location.href = 'beranda.php';
          </script>";
    exit();
}

$email_user = $_SESSION['email'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_user'");
$user_data = mysqli_fetch_assoc($query_user);
$nama_user = $user_data['nama'];
$foto_user = isset($user_data['foto']) ? $user_data['foto'] : '';
$inisial = strtoupper(substr($nama_user, 0, 1));

$foto_path = "../assets/img/" . $foto_user;
$avatar_style = "";
if (!empty($foto_user) && file_exists($foto_path) && $foto_user !== 'default-avatar.png' && $foto_user !== 'tds4.jpg' && $foto_user !== 'logo.png') {
    $avatar_style = "background-image: url('$foto_path'); background-size: cover; background-position: center; color: transparent; border: 1px solid #d4af37;";
}

date_default_timezone_set('Asia/Makassar'); // Menggunakan zona waktu WITA
$waktu_sekarang = time();
$waktu_konser = strtotime($row['tanggal'] . ' ' . $row['waktu']);
$sudah_lewat = ($waktu_konser < $waktu_sekarang);

$sisa_tiket = intval($row['kapasitas']) - intval($row['tiket_terjual']);
$badge_class = 'safe';
$sisa_text = 'Tersedia';

if ($sudah_lewat) {
    $badge_class = 'urgent';
    $sisa_text = 'Berakhir';
} elseif ($sisa_tiket <= 0 || $row['status'] === 'Habis') {
    $badge_class = 'urgent';
    $sisa_text = 'Habis';
} elseif ($sisa_tiket <= 150 || $row['status'] === 'Hampir Habis') {
    $badge_class = 'urgent';
    $sisa_text = "Hanya " . number_format($sisa_tiket, 0, ',', '.') . " Tiket!";
}

$poster_path = "../assets/img/" . htmlspecialchars($row['poster']);
if (empty($row['poster']) || !file_exists($poster_path)) {
    $poster_path = "../assets/img/default-poster.png";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Konser - <?php echo htmlspecialchars($row['nama_konser']); ?></title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <style>
        .qty-selector {
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 25px;
            padding: 6px 16px;
            width: fit-content;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.5);
        }
        .btn-qty {
            background: none;
            border: none;
            color: #f1c40f;
            font-size: 1.3rem;
            font-weight: bold;
            cursor: pointer;
            padding: 0 8px;
            transition: transform 0.1s, color 0.2s;
            outline: none;
            display: inline-block;
            line-height: 1;
        }
        .btn-qty:hover {
            color: #fff;
            transform: scale(1.25);
        }
        .btn-qty:active {
            transform: scale(0.95);
        }
        .qty-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #444;
            border-radius: 8px;
            color: #fff;
            font-size: 1.15rem;
            font-weight: bold;
            width: 55px;
            padding: 4px 6px;
            text-align: center;
            outline: none;
            -moz-appearance: textfield;
            transition: all 0.2s ease-in-out;
            cursor: text;
        }
        .qty-input:hover {
            border-color: #666;
            background: rgba(255, 255, 255, 0.08);
        }
        .qty-input:focus {
            border-color: #d4af37;
            background: rgba(212, 175, 55, 0.1);
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.3);
        }
        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        @media (max-width: 768px) {
            .checkout-box {
                flex-direction: column;
                align-items: stretch;
                gap: 20px;
            }
            .btn-pesan-sekarang {
                width: 100%;
                text-align: center;
            }
        }
    </style>
    <script>
        window.detailStats = {
            sisaTiket: <?php echo intval($sisa_tiket); ?>
        };
    </script>
    <script src="../assets/script/script.js"></script>
</head>
<body>
    <nav class="header-user">
        <div class="logo-area" onclick="window.location.href='beranda.php'">
            <img src="../assets/img/logo.png" alt="NTBeat Logo">
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav">
            <span>Halo, <?php echo htmlspecialchars($nama_user); ?>!</span>
            <div class="avatar-placeholder" onclick="window.location.href = 'profil.php'" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
        </div>
    </nav>

    <main class="detail-container">
        <div class="detail-poster">
            <div class="poster-display" style="width: 100%; height: 500px; background-image: url('<?php echo $poster_path; ?>'); background-size: cover; background-position: center; border-radius: 15px; border: 1px solid #333; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
            </div>
        </div>

        <div class="detail-info">
            <a href="beranda.php" class="back-link">← Kembali ke Beranda</a>
            
            <h1 class="detail-title"><?php echo htmlspecialchars($row['nama_konser']); ?></h1>
            
            <div class="detail-meta">
                <p>📅 <strong>Tanggal & Waktu:</strong> <?php echo date('d M Y', strtotime($row['tanggal'])); ?> • <?php echo date('H:i', strtotime($row['waktu'])); ?> WITA</p>
                <p>📍 <strong>Tempat:</strong> <?php echo htmlspecialchars($row['lokasi']); ?></p>
                <p>👥 <strong>Line-up:</strong> <?php echo empty($row['lineup']) ? 'Musisi Pilihan' : htmlspecialchars($row['lineup']); ?></p>
            </div>

            <div class="detail-desc">
                <h3>Tentang Acara</h3>
                <p>
                    <?php 
                        $default_desc = "Nikmati perhelatan musik yang spektakuler bersama kami. Saksikan penampilan menawan dari para artis favorit Anda dalam atmosfer konser yang luar biasa. NTBeat menjamin kemudahan akses tiket serta kenyamanan transaksi Anda.";
                        echo !empty($row['deskripsi']) ? nl2br(htmlspecialchars($row['deskripsi'])) : $default_desc;
                    ?>
                </p>
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
                    <span class="price-label">Total Harga</span>
                    <span class="price-total" id="price-total-display">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                </div>
                <?php if (!$sudah_lewat && $sisa_tiket > 0 && $row['status'] !== 'Habis') { ?>
                    <div class="qty-picker-wrapper" style="display: flex; flex-direction: column; gap: 5px; align-items: center;">
                        <span class="price-label">Jumlah Tiket</span>
                        <div class="qty-selector">
                            <button type="button" class="btn-qty" onclick="changeQty(-1)">-</button>
                            <input type="number" id="qty-input" class="qty-input" value="1" min="1" max="<?php echo $sisa_tiket; ?>" oninput="updateQtyFromInput(this.value)" onblur="finalizeQtyInput(this)" onkeydown="if(event.key==='.' || event.key==='e' || event.key==='E' || event.key==='-' || event.key==='+') event.preventDefault();">
                            <button type="button" class="btn-qty" onclick="changeQty(1)">+</button>
                        </div>
                    </div>
                <?php } ?>
                
                <?php if ($sudah_lewat) { ?>
                    <button class="btn-pesan-sekarang" disabled style="background-color: #555; color: #888; cursor: not-allowed;">Sudah Berakhir</button>
                <?php } elseif ($sisa_tiket > 0 && $row['status'] !== 'Habis') { ?>
                    <button class="btn-pesan-sekarang" onclick="bookingTiket()">Pesan</button>
                <?php } else { ?>
                    <button class="btn-pesan-sekarang" disabled style="background-color: #555; color: #888; cursor: not-allowed;">Tiket Habis</button>
                <?php } ?>
            </div>
        </div>
    </main>

    <form id="booking-form" action="../actions/pesan-tiket-proses.php" method="POST" style="display:none;">
        <input type="hidden" name="konser_id" value="<?php echo $row['id']; ?>">
        <input type="hidden" id="jumlah_tiket_input" name="jumlah_tiket" value="1">
        <input type="hidden" id="total_harga_input" name="total_harga" value="<?php echo $row['harga']; ?>">
    </form>

    <!-- Modals untuk Feedback Pembelian -->
    <div id="warningModal" class="modal-overlay" style="display: none;">
        <div class="logout-card">
            <h2>⚠️ Peringatan</h2>
            <p id="warningMessage"></p>
            <div class="logout-actions">
                <button class="btn-yakin" onclick="document.getElementById('warningModal').style.display='none'">Tutup</button>
            </div>
        </div>
    </div>

    <div id="bookingModal" class="modal-overlay" style="display: none;">
        <div class="logout-card">
            <h2>🎟️ Konfirmasi Pesanan</h2>
            <div style="margin: 20px 0; text-align: left; background: #111; padding: 15px; border-radius: 8px; font-size: 14px;">
                <p style="margin: 5px 0; color: #ccc;"><strong>Acara:</strong> <span id="confirmAcara"></span></p>
                <p style="margin: 5px 0; color: #ccc;"><strong>Jumlah:</strong> <span id="confirmJumlah"></span> Tiket</p>
                <p style="margin: 5px 0; color: #ccc;"><strong>Total:</strong> <span id="confirmTotal" style="color: #f1c40f; font-weight: bold;"></span></p>
            </div>
            <p>Apakah Anda ingin melanjutkan ke pembayaran?</p>
            <div class="logout-actions">
                <button class="btn-batal" onclick="document.getElementById('bookingModal').style.display='none'">Batal</button>
                <button class="btn-yakin" onclick="document.getElementById('booking-form').submit()">Lanjut</button>
            </div>
        </div>
    </div>

    <script>
    const basePrice = <?php echo intval($row['harga']); ?>;
    const maxQty = <?php echo intval($sisa_tiket); ?>;
    let currentQty = 1;

    function formatRupiah(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function showWarning(msg) {
        document.getElementById('warningMessage').innerText = msg;
        document.getElementById('warningModal').style.display = 'flex';
    }

    function changeQty(amount) {
        const nextQty = currentQty + amount;
        if (nextQty >= 1 && nextQty <= maxQty) {
            currentQty = nextQty;
            
            // Update input value
            document.getElementById('qty-input').value = currentQty;
            
            // Calculate total price
            const totalPrice = currentQty * basePrice;
            
            // Update display total price
            document.getElementById('price-total-display').innerText = formatRupiah(totalPrice);
            
            // Update form inputs
            document.getElementById('jumlah_tiket_input').value = currentQty;
            document.getElementById('total_harga_input').value = totalPrice;
        } else if (nextQty > maxQty && amount > 0) {
            showWarning('Jumlah pembelian melebihi tiket yang tersedia.');
        }
    }

    function updateQtyFromInput(val) {
        if (val === '') {
            currentQty = 0;
            document.getElementById('price-total-display').innerText = formatRupiah(0);
            document.getElementById('jumlah_tiket_input').value = '';
            document.getElementById('total_harga_input').value = 0;
            return;
        }
        
        let numericVal = parseInt(val, 10);
        if (isNaN(numericVal)) {
            return;
        }
        
        if (numericVal < 0) {
            numericVal = 0;
        } else if (numericVal > maxQty) {
            numericVal = maxQty;
            showWarning('Jumlah pembelian melebihi tiket yang tersedia.');
        }
        
        currentQty = numericVal;
        
        // Sync input value in case of clamping
        const qtyInput = document.getElementById('qty-input');
        if (qtyInput && parseInt(qtyInput.value, 10) !== currentQty) {
            qtyInput.value = currentQty;
        }
        
        // Calculate total price
        const totalPrice = currentQty * basePrice;
        
        // Update display total price
        document.getElementById('price-total-display').innerText = formatRupiah(totalPrice);
        
        // Update form inputs
        document.getElementById('jumlah_tiket_input').value = currentQty;
        document.getElementById('total_harga_input').value = totalPrice;
    }

    function finalizeQtyInput(inputEl) {
        let val = parseInt(inputEl.value, 10);
        if (isNaN(val) || val < 1) {
            val = 1;
        } else if (val > maxQty) {
            val = maxQty;
        }
        inputEl.value = val;
        updateQtyFromInput(val.toString());
    }

    function bookingTiket() {
        const qtyInput = document.getElementById('qty-input');
        if (qtyInput) {
            finalizeQtyInput(qtyInput);
        }
        
        if (currentQty < 1) {
            showWarning('Silakan masukkan jumlah tiket minimal 1.');
            if (qtyInput) {
                qtyInput.focus();
            }
            return;
        }
        
        const namaKonser = <?php echo json_encode($row['nama_konser']); ?>;
        const formattedTotal = formatRupiah(currentQty * basePrice);
        
        document.getElementById('confirmAcara').innerText = namaKonser;
        document.getElementById('confirmJumlah').innerText = currentQty;
        document.getElementById('confirmTotal').innerText = formattedTotal;
        document.getElementById('bookingModal').style.display = 'flex';
    }
    </script>
</body>
</html>