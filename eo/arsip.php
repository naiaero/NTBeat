<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'eo') {
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Acara - NTBeat</title>
    <link rel="stylesheet" href="../assets/style/admin-style.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <script src="../assets/script/script.js"></script>
</head>
<body class="admin-body">

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
                <li class="active" onclick="window.location.href = 'arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'verifikasi-pembayaran.php'">Verifikasi Pembayaran<?php echo $badge_pending; ?></li>
                <li onclick="window.location.href = 'profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header">
                <h2>Arsip Penyelenggaraan</h2>
                <p>Pilih acara untuk melihat laporan detail penjualan dan statistik masa lalu.</p>
            </div>

            <div class="bulk-action-bar">
                <div class="selected-count">
                    <span id="count-display">0</span> Data Terpilih
                </div>
                <div class="action-buttons">
                    <button class="btn-universal delete" id="btn-delete-bulk" disabled>Hapus Data</button>
                </div>
            </div>

            <div class="table-box archive-container">
                <form id="bulk-action-form" method="POST" action="../actions/admin-bulk-proses.php">
                    <input type="hidden" name="action_type" id="action-type" value="">
                    <input type="hidden" name="source_page" value="arsip.php">
                    <table class="admin-table archive-table">
                        <thead>
                            <tr>
                            <th width="40"><input type="checkbox" id="check-all"></th>
                            <th>Poster</th>
                            <th>Nama Konser</th>
                            <th>Tanggal Terlaksana</th>
                            <th>Harga Tiket</th>
                            <th>Total Penjualan</th>
                            <th>Pendapatan</th>
                            <th>Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="archive-list">
                        <?php
                        $query_konser = mysqli_query($conn, "SELECT * FROM konser WHERE status = 'Arsip' AND eo_email = '$email_user' ORDER BY tanggal DESC");
                        if (mysqli_num_rows($query_konser) > 0) {
                            while ($row = mysqli_fetch_assoc($query_konser)) {
                                $tanggal_format = date('d M Y', strtotime($row['tanggal']));
                                $poster_path = "../assets/img/" . htmlspecialchars($row['poster']);
                                if (empty($row['poster']) || !file_exists($poster_path)) {
                                    $poster_path = "../assets/img/default-poster.png";
                                }
                                $badge_text = $row['status'];
                                $badge_class = 'badge-safe';
                                if ($row['status'] == 'Arsip') {
                                    $badge_class = 'badge-archived';
                                } elseif ($row['status'] == 'Selesai') {
                                    $badge_class = 'badge-completed';
                                }
                                ?>
                                <tr class="past-event">
                                    <td><input type="checkbox" name="ids[]" class="row-checkbox" value="<?php echo $row['id']; ?>"></td>
                                    <td>
                                        <div class="mini-poster archived" style="background-image: url('<?php echo $poster_path; ?>'); background-size: cover; background-position: center; border: 1px solid #444;"></div>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['nama_konser']); ?></strong><br>
                                        <small>ID: <?php echo $row['id']; ?></small>
                                    </td>
                                    <td><?php echo $tanggal_format; ?></td>
                                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $row['tiket_terjual']; ?> / <?php echo $row['kapasitas']; ?> Tiket</td>
                                    <td>Rp <?php echo number_format($row['tiket_terjual'] * $row['harga'], 0, ',', '.'); ?></td>
                                    <td><span class="<?php echo $badge_class; ?>"><?php echo $badge_text; ?></span></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center; padding: 20px; color:#888;'>Belum ada data konser yang diarsip.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                </form>
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