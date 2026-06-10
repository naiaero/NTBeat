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
if (!empty($foto_user) && file_exists($foto_path) && $foto_user !== 'default-avatar.png' && $foto_user !== 'logo.png') {
    $avatar_style = "background-image: url('$foto_path'); background-size: cover; background-position: center; color: transparent; border: 1px solid #d4af37;";
}

// 1. Hitung Total Tiket Terjual Lunas (Kecualikan yang diarsip)
$query_tiket = mysqli_query($conn, "
    SELECT SUM(p.jumlah_tiket) as total_tiket 
    FROM pesanan p 
    JOIN konser k ON p.konser_id = k.id 
    WHERE p.status_bayar = 'Lunas' AND k.status != 'Arsip' AND k.eo_email = '$email_user'
");
$data_tiket = mysqli_fetch_assoc($query_tiket);
$total_tiket_terjual = $data_tiket['total_tiket'] ? $data_tiket['total_tiket'] : 0;

// 2. Hitung Total Kapasitas Semua Konser (Kecualikan yang diarsip)
$query_kapasitas = mysqli_query($conn, "SELECT SUM(kapasitas) as total_kapasitas FROM konser WHERE status != 'Arsip' AND eo_email = '$email_user'");
$data_kapasitas = mysqli_fetch_assoc($query_kapasitas);
$total_kapasitas = $data_kapasitas['total_kapasitas'] ? $data_kapasitas['total_kapasitas'] : 0;

// 3. Hitung Sisa Kuota
$sisa_kuota = $total_kapasitas - $total_tiket_terjual;

// 4. Hitung Pendapatan Lunas (Kecualikan yang diarsip)
$query_pendapatan = mysqli_query($conn, "
    SELECT SUM(p.total_harga) as total_pendapatan 
    FROM pesanan p 
    JOIN konser k ON p.konser_id = k.id 
    WHERE p.status_bayar = 'Lunas' AND k.status != 'Arsip' AND k.eo_email = '$email_user'
");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total_pendapatan'] ? $data_pendapatan['total_pendapatan'] : 0;

function format_rupiah_ringkas($num) {
    if ($num >= 1000000000) {
        $val_str = str_replace('.', ',', sprintf("%.1f", $num / 1000000000));
        if (substr($val_str, -2) === ',0') {
            $val_str = substr($val_str, 0, -2);
        }
        return "Rp " . $val_str . " M";
    } elseif ($num >= 1000000) {
        $val_str = str_replace('.', ',', sprintf("%.1f", $num / 1000000));
        if (substr($val_str, -2) === ',0') {
            $val_str = substr($val_str, 0, -2);
        }
        return "Rp " . $val_str . " jt";
    } elseif ($num >= 1000) {
        $val_str = str_replace('.', ',', sprintf("%.1f", $num / 1000));
        if (substr($val_str, -2) === ',0') {
            $val_str = substr($val_str, 0, -2);
        }
        return "Rp " . $val_str . " rb";
    } else {
        return "Rp " . number_format($num, 0, ',', '.');
    }
}
$pendapatan_formatted = format_rupiah_ringkas($total_pendapatan);

$chart_labels = [];
$chart_data = [];
$sales_by_date = [];

for ($i = 6; $i >= 0; $i--) {
    $date_str = date('Y-m-d', strtotime("-$i days"));
    $label_str = date('d M', strtotime("-$i days"));
    $sales_by_date[$date_str] = ['label' => $label_str, 'count' => 0];
}

$seven_days_ago = date('Y-m-d', strtotime('-6 days')) . ' 00:00:00';
$query_chart = mysqli_query($conn, "
    SELECT DATE(p.tanggal_pesan) as tanggal, SUM(p.jumlah_tiket) as total_tiket 
    FROM pesanan p
    JOIN konser k ON p.konser_id = k.id 
    WHERE p.tanggal_pesan >= '$seven_days_ago' AND p.status_bayar = 'Lunas' AND k.eo_email = '$email_user'
    GROUP BY DATE(p.tanggal_pesan)
");

if ($query_chart) {
    while ($row_chart = mysqli_fetch_assoc($query_chart)) {
        $date_key = $row_chart['tanggal'];
        if (isset($sales_by_date[$date_key])) {
            $sales_by_date[$date_key]['count'] = intval($row_chart['total_tiket']);
        }
    }
}

foreach ($sales_by_date as $date_info) {
    $chart_labels[] = $date_info['label'];
    $chart_data[] = $date_info['count'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EO Dashboard - NTBeat</title>
    <link rel="stylesheet" href="../assets/style/admin-style.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.dbStats = {
            totalSold: <?php echo intval($total_tiket_terjual); ?>,
            totalCapacity: <?php echo intval($total_kapasitas); ?>,
            totalRevenue: <?php echo floatval($total_pendapatan); ?>
        };
        window.chartData = {
            labels: <?php echo json_encode($chart_labels); ?>,
            data: <?php echo json_encode($chart_data); ?>
        };
    </script>
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
                <li class="active" onclick="window.location.href = 'dashboard.php'">Dashboard Acara</li>
                <li onclick="window.location.href = 'form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'verifikasi-pembayaran.php'">Verifikasi Pembayaran<?php echo $badge_pending; ?></li>
                <li onclick="window.location.href = 'profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header">
                <h2>Ringkasan Penjualan</h2>
                <p>Pantau antusiasme penonton dan kuota tiket Anda secara langsung.</p>
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
                    <span class="stat-label">Sisa Kuota (Semua Acara)</span>
                    <div class="stat-value" id="remaining-count">
                        <?php echo number_format($sisa_kuota, 0, ',', '.'); ?>
                    </div>
                    <div class="stat-sub">Kapasitas: <?php echo number_format($total_kapasitas, 0, ',', '.'); ?></div>
                </div>
                <div class="card-admin">
                    <span class="stat-label">Pendapatan</span>
                    <div class="stat-value gold-text" id="revenue-count">
                        <?php echo $pendapatan_formatted; ?>
                    </div>
                </div>
            </section>

            <div class="analytics-grid">
                <div class="chart-box">
                    <h3>Tren Penjualan Per Hari</h3>
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
                            $query_konser = mysqli_query($conn, "
                                SELECT k.nama_konser, k.kapasitas, k.status, 
                                       IFNULL(SUM(p.jumlah_tiket), 0) as tiket_lunas 
                                FROM konser k 
                                LEFT JOIN pesanan p ON k.id = p.konser_id AND p.status_bayar = 'Lunas'
                                WHERE k.status != 'Arsip' AND k.eo_email = '$email_user'
                                GROUP BY k.id 
                                ORDER BY tiket_lunas DESC 
                                LIMIT 4
                            ");
                            
                            while ($row = mysqli_fetch_assoc($query_konser)) {
                                $badge_class = 'badge-safe';
                                if ($row['status'] == 'Hampir Habis') {
                                    $badge_class = 'badge-urgent';
                                } elseif ($row['status'] == 'Habis' || $row['status'] == 'Selesai') {
                                    $badge_class = 'badge-danger';
                                }

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nama_konser']) . "</td>";
                                echo "<td>" . intval($row['tiket_lunas']) . "/" . $row['kapasitas'] . "</td>";
                                echo "<td><span class='" . $badge_class . "'>" . $row['status'] . "</span></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
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
                <button class="btn-yakin" onclick="window.location.href = '../auth/logout.php'">Keluar</button>
            </div>
        </div>
    </div>
</body>
</html>