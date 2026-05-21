<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$email_user = $_SESSION['email'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_user'");
$user_data = mysqli_fetch_assoc($query_user);
$nama_user = $user_data['nama'];
$foto_user = isset($user_data['foto']) ? $user_data['foto'] : '';
$inisial = strtoupper(substr($nama_user, 0, 1));

$foto_path = "assets/img/" . $foto_user;
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
    <link rel="stylesheet" href="assets/style/admin-style.css">
    <link rel="stylesheet" href="assets/style/style.css">
    <script src="assets/script/script.js"></script>
</head>
<body class="admin-body">

    <header class="header-user">
        <div class="logo-area" onclick="window.location.href='admin-dashboard.php'" style="cursor: pointer;">
            <img src="assets/img/logo.png" alt="Logo"> 
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav" onclick="window.location.href = 'admin-profil.php'" style="cursor: pointer;">
            <span style="color: white; font-size: 0.9rem; margin-right: 10px;"><?php echo htmlspecialchars($nama_user); ?></span>
            <div class="avatar-placeholder" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li onclick="window.location.href = 'admin-dashboard.php'">Dashboard</li>
                <li onclick="window.location.href = 'admin-form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'admin-kelola-konser.php'">Kelola Data Konser</li>
                <li class="active" onclick="window.location.href = 'admin-arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'admin-profil.php'">Pengaturan Profil</li>
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
            </div>

            <div class="table-box archive-container">
                <table class="admin-table archive-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="check-all"></th>
                            <th>Poster</th>
                            <th>Nama Konser</th>
                            <th>Tanggal Terlaksana</th>
                            <th>Total Penjualan</th>
                            <th>Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="archive-list">
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM konser WHERE status IN ('Arsip', 'Selesai') ORDER BY id DESC");
                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                $tanggal_format = date('d M Y', strtotime($row['tanggal']));
                                $poster_path = "assets/img/" . htmlspecialchars($row['poster']);
                                if (empty($row['poster']) || !file_exists($poster_path)) {
                                    $poster_path = "assets/img/default-poster.png";
                                }
                                $badge_text = $row['status'];
                                $badge_class = $row['status'] == 'Arsip' ? 'badge-archived' : 'badge-safe';
                                ?>
                                <tr class="past-event">
                                    <td><input type="checkbox" class="row-checkbox" value="<?php echo $row['id']; ?>"></td>
                                    <td>
                                        <div class="mini-poster archived" style="background-image: url('<?php echo $poster_path; ?>'); background-size: cover; background-position: center; border: 1px solid #444;"></div>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['nama_konser']); ?></strong><br>
                                        <small>ID: <?php echo $row['id']; ?></small>
                                    </td>
                                    <td><?php echo $tanggal_format; ?></td>
                                    <td><?php echo $row['tiket_terjual']; ?> / <?php echo $row['kapasitas']; ?> Tiket</td>
                                    <td><span class="<?php echo $badge_class; ?>"><?php echo $badge_text; ?></span></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding: 20px; color:#888;'>Belum ada data konser yang diarsip atau selesai.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="archive-footer">
                <p>Data di halaman ini bersifat permanen dan digunakan untuk keperluan audit keuangan organisasi.</p>
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