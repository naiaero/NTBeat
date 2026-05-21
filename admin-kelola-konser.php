<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Konser - NTBeat</title>
    <link rel="stylesheet" href="assets/style/admin-style.css">
    <link rel="stylesheet" href="assets/style/style.css">
    <script src="assets/script/script.js"></script>
</head>
<body class="admin-body">

    <header class="header-user">
        <div class="logo-area">
            <img src="assets/img/logo.png" alt="Logo"> 
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav">
            <span style="color: white; font-size: 0.9rem;">Administrator</span>
            <div class="avatar-placeholder" onclick="window.location.href = 'admin-profil.php'">A</div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li onclick="window.location.href = 'admin-dashboard.php'">Dashboard</li>
                <li onclick="window.location.href = 'admin-form-konser.php'">Tambah Acara Baru</li>
                <li class="active" onclick="window.location.href = 'admin-kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'admin-arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'admin-profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header">
                <h2>Pusat Kendali Concert</h2>
                <p>Pilih satu atau beberapa konser untuk melakukan aksi massal.</p>
            </div>

            <div class="bulk-action-bar">
                <div class="selected-count">
                    <span id="count-display">0</span> Data Terpilih
                </div>
                <div class="action-buttons">
                    <button class="btn-universal edit" id="btn-edit-bulk" disabled>Edit Detail</button>
                    <button class="btn-universal archive" id="btn-archive-bulk" disabled>Arsipkan</button>
                    <button class="btn-universal delete" id="btn-delete-bulk" disabled>Hapus Data</button>
                </div>
            </div>

            <div class="table-box">
                <form id="bulk-action-form" method="POST" action="admin-bulk-proses.php">
                    <input type="hidden" name="action_type" id="action-type" value="">
                    <table class="admin-table manage-table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="check-all"></th>
                                <th>Poster</th>
                                <th>Nama Konser</th>
                                <th>Tanggal & Lokasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="concert-list">
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM konser WHERE status != 'Arsip' ORDER BY id DESC");
                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                                    $badge_class = 'badge-safe';
                                    if ($row['status'] == 'Hampir Habis') {
                                        $badge_class = 'badge-urgent';
                                    } elseif ($row['status'] == 'Habis') {
                                        $badge_class = 'badge-danger';
                                    }

                                    // Format tanggal
                                    $tanggal_format = date('d M Y', strtotime($row['tanggal']));
                                    
                                    // Ganti style mini-poster agar menggunakan poster dari DB jika ada
                                    $poster_path = "assets/img/" . htmlspecialchars($row['poster']);
                                    if (empty($row['poster']) || !file_exists($poster_path)) {
                                        $poster_path = "assets/img/default-poster.jpg";
                                    }
                                    ?>
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" class="row-checkbox" value="<?php echo $row['id']; ?>"></td>
                                        <td>
                                            <div class="mini-poster" style="background-image: url('<?php echo $poster_path; ?>'); background-size: cover; background-position: center; border: 1px solid #444;"></div>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['nama_konser']); ?></strong><br>
                                            <small>ID: <?php echo $row['id']; ?></small>
                                        </td>
                                        <td>
                                            <?php echo $tanggal_format; ?><br>
                                            <small><?php echo htmlspecialchars($row['lokasi']); ?></small>
                                        </td>
                                        <td><span class="<?php echo $badge_class; ?>"><?php echo $row['status']; ?></span></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; padding: 20px; color:#888;'>Belum ada data konser. Silakan tambahkan konser baru.</td></tr>";
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
                <button class="btn-yakin" onclick="window.location.href = 'logout.php'">Keluar</button>
            </div>
        </div>
    </div>
</body>
</html>