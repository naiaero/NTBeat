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


// Hitung total EO terdaftar
$query_eo = mysqli_query($conn, "SELECT COUNT(*) as total_eo FROM users WHERE role = 'eo'");
$total_eo = mysqli_fetch_assoc($query_eo)['total_eo'];

// Hitung Total Pengunjung Keseluruhan
$query_total_visitor = mysqli_query($conn, "SELECT SUM(jumlah) as total_semua FROM pengunjung");
$total_semua_pengunjung = mysqli_fetch_assoc($query_total_visitor)['total_semua'] ?? 0;

// Hitung Kunjungan Hari Ini
$today = date('Y-m-d');
$query_today_visitor = mysqli_query($conn, "SELECT jumlah FROM pengunjung WHERE tanggal = '$today'");
$today_row = mysqli_fetch_assoc($query_today_visitor);
$pengunjung_hari_ini = $today_row ? $today_row['jumlah'] : 0;

// Data untuk grafik pengunjung (7 hari terakhir)
$query_visitor = mysqli_query($conn, "SELECT * FROM pengunjung ORDER BY tanggal DESC LIMIT 7");
$dates = [];
$counts = [];
while($row = mysqli_fetch_assoc($query_visitor)) {
    $dates[] = date('d M', strtotime($row['tanggal']));
    $counts[] = $row['jumlah'];
}
// Balik urutan agar dari hari terlama ke terbaru (kiri ke kanan di grafik)
$dates = array_reverse($dates);
$counts = array_reverse($counts);
$dates_json = json_encode($dates);
$counts_json = json_encode($counts);

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
    <style>
        /* Modern Boxed Dashboard Style */
        .content-area {
            padding: 30px 40px;
            background-color: #121212;
            min-height: 100vh;
        }
        .section-header h2 {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 5px;
        }
        .section-header p {
            color: #888;
            font-size: 1rem;
            margin-bottom: 30px;
        }
        
        /* Boxed Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }
        
        .data-box {
            background: linear-gradient(145deg, #1f1f1f, #181818);
            border: 1px solid #2a2a2a;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .data-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(241, 196, 15, 0.15);
            border-color: #3a3a3a;
        }
        
        .box-icon {
            font-size: 2.5rem;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin-right: 20px;
        }
        
        .box-content h3 {
            font-size: 0.95rem;
            color: #aaa;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .box-content .value {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
        }
        
        /* Specific Box Colors */
        .box-visitor {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }
        .box-today {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }
        .box-eo {
            background: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
        }
        
        /* Chart Box */
        .chart-box-container {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }
        .chart-box-container h3 {
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        .chart-box-container h3::before {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #f1c40f;
            border-radius: 50%;
            margin-right: 12px;
        }
    </style>
    <script src="../assets/script/script.js"></script>
    
</head>
<body class="admin-body">

    <header class="header-user">
        <div class="logo-area" onclick="window.location.href='dashboard.php'" style="cursor: pointer;">
            <img src="../assets/img/logo.png" alt="Logo"> <label>NTBeat</label>
        </div>
        <div class="user-profile-nav" onclick="window.location.href = 'profil.php'" style="cursor: pointer;">
            <span style="color: white; font-size: 0.9rem; margin-right: 10px;">Halo, <?php echo htmlspecialchars($nama_depan); ?>!</span>
            <div class="avatar-placeholder" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="active" onclick="window.location.href = 'dashboard.php'">Dashboard Platform</li>
                <li onclick="window.location.href = 'kelola-eo.php'">Kelola Pengguna EO</li>
                <li onclick="window.location.href = 'form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header">
                <h2>Ringkasan Platform</h2>
                <p>Pantau statistik lalu lintas pengunjung dan jumlah penyelenggara (EO) di platform.</p>
            </div>

            <div class="dashboard-grid">
                <!-- Box 1: Total Visitors -->
                <div class="data-box">
                    <div class="box-icon box-visitor">📈</div>
                    <div class="box-content">
                        <h3>Total Pengunjung</h3>
                        <div class="value"><?php echo number_format($total_semua_pengunjung); ?></div>
                    </div>
                </div>

                <!-- Box 2: Visitors Today -->
                <div class="data-box">
                    <div class="box-icon box-today">🎯</div>
                    <div class="box-content">
                        <h3>Pengunjung Hari Ini</h3>
                        <div class="value"><?php echo number_format($pengunjung_hari_ini); ?></div>
                    </div>
                </div>

                <!-- Box 3: Total EO -->
                <div class="data-box" style="cursor: pointer;" onclick="window.location.href='kelola-eo.php'">
                    <div class="box-icon box-eo">🏢</div>
                    <div class="box-content">
                        <h3>Mitra EO Terdaftar</h3>
                        <div class="value"><?php echo number_format($total_eo); ?></div>
                    </div>
                </div>
            </div>

            <!-- Box 4: Chart -->
            <div class="chart-box-container">
                <h3>Analisis Kunjungan 7 Hari Terakhir</h3>
                <canvas id="visitorChart" style="max-height: 380px;"></canvas>
            </div>
        </main>

        <!-- Skrip Inisialisasi Chart.js -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('visitorChart').getContext('2d');
                var visitorChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo $dates_json; ?>,
                        datasets: [{
                            label: 'Jumlah Kunjungan Unik',
                            data: <?php echo $counts_json; ?>,
                            borderColor: '#f1c40f',
                            backgroundColor: 'rgba(241, 196, 15, 0.2)',
                            borderWidth: 3,
                            pointBackgroundColor: '#111',
                            pointBorderColor: '#f1c40f',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#fff', font: { size: 14 } }
                            }
                        },
                        scales: {
                            x: { ticks: { color: '#ccc' }, grid: { color: '#333' } },
                            y: { ticks: { color: '#ccc', beginAtZero: true, precision: 0 }, grid: { color: '#333' } }
                        }
                    }
                });
            });
        </script>
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