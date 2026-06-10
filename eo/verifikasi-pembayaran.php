<?php
session_start();
include '../config/koneksi.php';

// Verifikasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'eo') {
    header("Location: ../auth/login.php");
    exit();
}

$email_user = $_SESSION['email'];

// Tandai semua notifikasi pesanan baru sebagai 'sudah dibaca' saat halaman ini dibuka
$stmt_read = $conn->prepare("
    UPDATE pesanan p 
    JOIN konser k ON p.konser_id = k.id 
    SET p.is_read_eo = 1 
    WHERE p.status_bayar = 'Menunggu Verifikasi' AND p.is_read_eo = 0 AND k.eo_email = ?
");
$stmt_read->bind_param('s', $email_user);
$stmt_read->execute();

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
if (!empty($foto_user) && file_exists($foto_path) && $foto_user !== 'default-avatar.png' && $foto_user !== 'logo.png') {
    $avatar_style = "background-image: url('$foto_path'); background-size: cover; background-position: center; color: transparent; border: 1px solid #d4af37;";
}

// Ambil data pesanan yang perlu diverifikasi
$query_pending = mysqli_query($conn, "
    SELECT p.*, k.nama_konser, u.nama 
    FROM pesanan p
    JOIN konser k ON p.konser_id = k.id
    JOIN users u ON p.user_email = u.email
    WHERE p.status_bayar = 'Menunggu Verifikasi' AND k.eo_email = '$email_user'
    ORDER BY p.waktu_kadaluarsa ASC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran - Admin NTBeat</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <script src="../assets/script/script.js"></script>
    <style>
        .table-container {
            overflow-x: auto;
            background: #1a1a1a;
            border-radius: 8px;
            border: 1px solid #333;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            color: #fff;
        }
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        .data-table th {
            background: #111;
            font-weight: bold;
            color: #f1c40f;
        }
        .data-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .btn-lihat {
            background: #3498db;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .btn-lihat:hover { background: #2980b9; }
        .action-flex {
            display: flex;
            gap: 8px;
        }
        .btn-setuju {
            background: #2ecc71;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .btn-setuju:hover { background: #27ae60; }
        .btn-tolak {
            background: #e74c3c;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .btn-tolak:hover { background: #c0392b; }
    </style>
</head>
<body>
        <header class="header-user">
        <div class="logo-area" onclick="window.location.href = 'dashboard.php'" style="cursor: pointer;">
            <img src="../assets/img/logo.png" alt="Logo"> 
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav" onclick="window.location.href = 'profil.php'" style="cursor: pointer;">
            <span style="color: white; font-size: 0.9rem; margin-right: 10px;">Halo, <?php echo htmlspecialchars($nama_depan); ?>!</span>
            <div class="avatar-placeholder" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            
            <?php
            $query_notif = mysqli_query($conn, "
                SELECT COUNT(p.id) as total_pending 
                FROM pesanan p 
                JOIN konser k ON p.konser_id = k.id 
                WHERE p.status_bayar = 'Menunggu Verifikasi' AND p.is_read_eo = 0 AND k.eo_email = '$email_user'
            ");
            $data_pending = mysqli_fetch_assoc($query_notif);
            $jumlah_pending = $data_pending['total_pending'];
            $badge_pending = ($jumlah_pending > 0) ? " <span style='background: #e74c3c; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75rem; font-weight: bold; margin-left: 5px; box-shadow: 0 0 5px rgba(231,76,60,0.5);'>" . $jumlah_pending . "</span>" : "";
            ?>
            <ul class="sidebar-menu">
                <li onclick="window.location.href = 'dashboard.php'">Dashboard Acara</li>
                <li onclick="window.location.href = 'form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'arsip.php'">Arsip Penyelenggaraan</li>
                <li class="active" onclick="window.location.href = 'verifikasi-pembayaran.php'">Verifikasi Pembayaran<?php echo $badge_pending; ?></li>
                <li onclick="window.location.href = 'profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header" style="margin-bottom: 20px;">
                <h2>Menunggu Verifikasi</h2>
                <p>Daftar transaksi pelanggan yang telah mengunggah bukti pembayaran.</p>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Nama Customer</th>
                            <th>Konser</th>
                            <th>Jml Tiket</th>
                            <th>Total Rp</th>
                            <th>Bukti Transfer</th>
                            <th>Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_pending) > 0) {
                            while ($row = mysqli_fetch_assoc($query_pending)) { ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($row['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_konser']); ?></td>
                                    <td><?php echo $row['jumlah_tiket']; ?></td>
                                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <button class="btn-lihat" onclick="lihatBukti('../assets/img/bukti_bayar/<?php echo htmlspecialchars($row['bukti_bayar']); ?>')">Lihat Gambar</button>
                                    </td>
                                    <td>
                                        <div class="action-flex">
                                            <form action="../actions/verifikasi-bayar-proses.php" method="POST" onsubmit="event.preventDefault(); openConfirmActionModal(this, '✅ Terima Pesanan', 'Apakah Anda yakin ingin menandai pesanan ini Lunas?');">
                                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                                <input type="hidden" name="aksi" value="setuju">
                                                <button type="submit" class="btn-setuju">Terima</button>
                                            </form>
                                            <form action="../actions/verifikasi-bayar-proses.php" method="POST" onsubmit="event.preventDefault(); openConfirmActionModal(this, '❌ Tolak Pesanan', 'Apakah Anda yakin ingin menolak pesanan ini dan mengembalikan stok tiketnya?');">
                                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                                <input type="hidden" name="konser_id" value="<?php echo htmlspecialchars($row['konser_id']); ?>">
                                                <input type="hidden" name="jumlah_tiket" value="<?php echo htmlspecialchars($row['jumlah_tiket']); ?>">
                                                <input type="hidden" name="aksi" value="tolak">
                                                <button type="submit" class="btn-tolak">Tolak</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr><td colspan="7" style="text-align: center; color: #888;">Tidak ada pembayaran yang menunggu verifikasi.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Gambar -->
    <div id="imageModal" class="modal-overlay" style="display: none; align-items: center; justify-content: center;">
        <div style="position: relative; max-width: 90%; max-height: 90vh;">
            <button onclick="document.getElementById('imageModal').style.display='none'" style="position: absolute; top: -15px; right: -15px; background: #e74c3c; color: #fff; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; font-weight: bold; font-size: 1.2rem; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">X</button>
            <img id="buktiImg" src="" style="max-width: 100%; max-height: 85vh; border-radius: 8px; border: 2px solid #333;">
        </div>
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

    <!-- Modal Konfirmasi Aksi (Terima/Tolak) -->
    <div id="confirmActionModal" class="modal-overlay" style="display: none; z-index: 9999;">
        <div class="logout-card">
            <h2 id="confirmActionTitle">Konfirmasi</h2>
            <p id="confirmActionText" style="margin-bottom: 20px; color: #ccc;"></p>
            <div class="logout-actions">
                <button class="btn-batal" onclick="closeConfirmActionModal()">Batal</button>
                <button class="btn-yakin" id="confirmActionBtn">Yakin</button>
            </div>
        </div>
    </div>

    <script>
        function lihatBukti(url) {
            document.getElementById('buktiImg').src = url;
            document.getElementById('imageModal').style.display = 'flex';
        }

        let currentFormToSubmit = null;

        function openConfirmActionModal(formElement, title, text) {
            currentFormToSubmit = formElement;
            document.getElementById('confirmActionTitle').innerText = title;
            document.getElementById('confirmActionText').innerText = text;
            document.getElementById('confirmActionModal').style.display = 'flex';
        }

        function closeConfirmActionModal() {
            currentFormToSubmit = null;
            document.getElementById('confirmActionModal').style.display = 'none';
        }

        document.getElementById('confirmActionBtn').addEventListener('click', function() {
            if (currentFormToSubmit) {
                currentFormToSubmit.submit();
            }
        });
    </script>
</body>
</html>
