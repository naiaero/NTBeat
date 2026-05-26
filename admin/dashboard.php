<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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


// 1. Hitung Total Tiket Terjual (Kecualikan yang diarsip)
$query_tiket = mysqli_query($conn, "SELECT SUM(tiket_terjual) as total_tiket FROM konser WHERE status != 'Arsip'");
$data_tiket = mysqli_fetch_assoc($query_tiket);
$total_tiket_terjual = $data_tiket['total_tiket'] ? $data_tiket['total_tiket'] : 0;

// 2. Hitung Total Kapasitas Semua Konser (Kecualikan yang diarsip)
$query_kapasitas = mysqli_query($conn, "SELECT SUM(kapasitas) as total_kapasitas FROM konser WHERE status != 'Arsip'");
$data_kapasitas = mysqli_fetch_assoc($query_kapasitas);
$total_kapasitas = $data_kapasitas['total_kapasitas'] ? $data_kapasitas['total_kapasitas'] : 0;

// 3. Hitung Sisa Kuota
$sisa_kuota = $total_kapasitas - $total_tiket_terjual;

// 4. Hitung Simulasi Pendapatan (Tiket Terjual x Harga per Tiket) (Kecualikan yang diarsip)
$query_pendapatan = mysqli_query($conn, "SELECT SUM(tiket_terjual * harga) as total_pendapatan FROM konser WHERE status != 'Arsip'");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total_pendapatan'] ? $data_pendapatan['total_pendapatan'] : 0;

// Format rupiah dinamis ringkas (Miliar, Juta, Ribu, atau Rupiah biasa)
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

// Ambil riwayat transaksi 
$chart_labels = [];
$chart_data = [];
$sales_by_date = [];

// Inisialisasi data 7 hari terakhir dengan nilai 0
for ($i = 6; $i >= 0; $i--) {
    $date_str = date('Y-m-d', strtotime("-$i days"));
    $label_str = date('d M', strtotime("-$i days"));
    $sales_by_date[$date_str] = [
        'label' => $label_str,
        'count' => 0
    ];
}

$seven_days_ago = date('Y-m-d', strtotime('-6 days')) . ' 00:00:00';
$query_chart = mysqli_query($conn, "
    SELECT DATE(tanggal_pesan) as tanggal, SUM(jumlah_tiket) as total_tiket 
    FROM pesanan 
    WHERE tanggal_pesan >= '$seven_days_ago'
    GROUP BY DATE(tanggal_pesan)
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
    <title>Admin Dashboard - NTBeat</title>
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
        <div class="logo-area" onclick="window.location.href='dashboard.php'" style="cursor: pointer;">
            <img src="../assets/img/logo.png" alt="Logo"> <label>NTBeat</label>
        </div>
        <div class="user-profile-nav" onclick="window.location.href = 'profil.php'" style="cursor: pointer;">
            <span style="color: white; font-size: 0.9rem; margin-right: 10px;"><?php echo htmlspecialchars($nama_depan); ?></span>
            <div class="avatar-placeholder" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="active" onclick="window.location.href = 'dashboard.php'">Dashboard</li>
                <li onclick="window.location.href = 'form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'profil.php'">Pengaturan Profil</li>
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
                            // Ambil data konser dari database (urutkan dari yang tiketnya paling banyak terjual, kecualikan yang diarsip)
                            $query_konser = mysqli_query($conn, "SELECT nama_konser, tiket_terjual, kapasitas, status FROM konser WHERE status != 'Arsip' ORDER BY tiket_terjual DESC LIMIT 4");
                            
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