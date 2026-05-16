<?php
session_start();
include 'koneksi.php';

// Keamanan ekstra: Cek apakah yang akses beneran Admin, kalau bukan tendang ke login!
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Hitung Total Tiket Terjual
$query_tiket = mysqli_query($conn, "SELECT SUM(tiket_terjual) as total_tiket FROM konser");
$data_tiket = mysqli_fetch_assoc($query_tiket);
$total_tiket_terjual = $data_tiket['total_tiket'] ? $data_tiket['total_tiket'] : 0;

// 2. Hitung Total Kapasitas Semua Konser
$query_kapasitas = mysqli_query($conn, "SELECT SUM(kapasitas) as total_kapasitas FROM konser");
$data_kapasitas = mysqli_fetch_assoc($query_kapasitas);
$total_kapasitas = $data_kapasitas['total_kapasitas'] ? $data_kapasitas['total_kapasitas'] : 0;

// 3. Hitung Sisa Kuota
$sisa_kuota = $total_kapasitas - $total_tiket_terjual;

// 4. Hitung Simulasi Pendapatan (Tiket Terjual x Harga per Tiket)
$query_pendapatan = mysqli_query($conn, "SELECT SUM(tiket_terjual * harga) as total_pendapatan FROM konser");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total_pendapatan'] ? $data_pendapatan['total_pendapatan'] : 0;

// Ubah format angka jadi jutaan (Contoh: 213000000 jadi 213,0)
$pendapatan_jt = number_format($total_pendapatan / 1000000, 1, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NTBeat</title>
    <link rel="stylesheet" href="assets/style/admin-style.css">
    <link rel="stylesheet" href="assets/style/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/script/script.js"></script>
    
</head>
<body class="admin-body">

    <header class="header-user">
        <div class="logo-area">
            <img src="assets/img/logo.png" alt="Logo"> <label>NTBeat</label>
        </div>
        <div class="user-profile-nav">
            <span style="color: white; font-size: 0.9rem;">Administrator</span>
            <div class="avatar-placeholder">A</div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="active" onclick="window.location.href = 'admin-dashboard.php'">Dashboard</li>
                <li onclick="window.location.href = 'admin-form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'admin-kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'admin-arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'admin-profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header">
                <h2>Ringkasan Penjualan</h2>
                <p>Pantau antusiasme penonton dan kuota tiket secara langsung.</p>
            </div>
            <section class="stats-container">
                <div class="card-admin">
                    <span class="stat-label">Total Tiket Terjual</span>
                    <div class="stat-value gold-text" id="sold-count">
                        <?php echo number_format($total_tiket_terjual, 0, ',', '.'); ?>
                    </div>
                    <div class="trend positive">↑ Data Realtime</div>
                </div>
                <div class="card-admin">
                    <span class="stat-label">Sisa Kuota (All Events)</span>
                    <div class="stat-value" id="remaining-count">
                        <?php echo number_format($sisa_kuota, 0, ',', '.'); ?>
                    </div>
                    <div class="stat-sub">Kapasitas: <?php echo number_format($total_kapasitas, 0, ',', '.'); ?></div>
                </div>
                <div class="card-admin">
                    <span class="stat-label">Simulasi Pendapatan</span>
                    <div class="stat-value gold-text" id="revenue-count">
                        Rp <?php echo $pendapatan_jt; ?>jt
                    </div>
                    <div class="stat-sub">Target: Rp 300jt</div>
                </div>
            </section>
            <!-- <section class="stats-container">
                <div class="card-admin">
                    <span class="stat-label">Total Tiket Terjual</span>
                    <div class="stat-value gold-text" id="sold-count">1.420</div>
                    <div class="trend positive">↑ 15% dari kemarin</div>
                </div>
                <div class="card-admin">
                    <span class="stat-label">Sisa Kuota (All Events)</span>
                    <div class="stat-value" id="remaining-count">580</div>
                    <div class="stat-sub">Kapasitas: 2.000</div>
                </div>
                <div class="card-admin">
                    <span class="stat-label">Simulasi Pendapatan</span>
                    <div class="stat-value gold-text" id="revenue-count">Rp 213,0jt</div>
                    <div class="stat-sub">Target: Rp 300jt</div>
                </div>
            </section> -->

            <div class="analytics-grid">
                <div class="chart-box">
                    <h3>Tren Penjualan Per Jam</h3>
                    <div class="line-chart-wrapper">
                        <canvas id="ntbeatLineChart"></canvas>
                    </div>
                </div>
                    
                <div class="table-box">
                    <h3>Status Kuota Terkini</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Konser</th>
                                <th>Terjual</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil data konser dari database (urutkan dari yang tiketnya paling banyak terjual)
                            $query_konser = mysqli_query($conn, "SELECT nama_konser, tiket_terjual, kapasitas, status FROM konser ORDER BY tiket_terjual DESC LIMIT 4");
                            
                            // Looping (Ulangi) pembuatan baris tabel sebanyak jumlah konser yang ada
                            while ($row = mysqli_fetch_assoc($query_konser)) {
                                
                                // Tentukan warna badge (label) otomatis berdasarkan status
                                $badge_class = 'badge-safe'; // Default warna hijau untuk 'Tersedia'
                                if ($row['status'] == 'Hampir Habis') {
                                    $badge_class = 'badge-urgent'; // Warna merah/oranye
                                } elseif ($row['status'] == 'Habis' || $row['status'] == 'Selesai') {
                                    $badge_class = 'badge-danger'; // Tambahkan class ini di CSS kamu untuk warna abu/gelap kalau mau
                                }

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nama_konser']) . "</td>";
                                echo "<td>" . $row['tiket_terjual'] . "/" . $row['kapasitas'] . "</td>";
                                echo "<td><span class='" . $badge_class . "'>" . $row['status'] . "</span></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                        <!-- <tbody>
                            <tr>
                                <td>Mataram Sound Wave</td>
                                <td>880/1000</td>
                                <td><span class="badge-urgent">Hampir Habis</span></td>
                            </tr>
                            <tr>
                                <td>Senggigi Jazz</td>
                                <td>540/1000</td>
                                <td><span class="badge-safe">Tersedia</span></td>
                            </tr>
                        </tbody> -->
                    </table>
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
                <button class="btn-yakin" onclick="window.location.href = 'index.php'">Keluar</button>
            </div>
        </div>
    </div>

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