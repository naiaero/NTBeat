<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NTBeat</title>
    <link rel="stylesheet" href="assets/style/admin-style.css">
    <link rel="stylesheet" href="assets/style/style.css">
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
            </section>

            <div class="analytics-grid">
                <div class="chart-box">
                    <h3>Tren Penjualan Per Jam</h3>
                    <div class="bar-chart">
                        <div class="bar-item"><div class="bar" style="height: 40%"></div><span>10:00</span></div>
                        <div class="bar-item"><div class="bar" style="height: 70%"></div><span>11:00</span></div>
                        <div class="bar-item"><div class="bar" style="height: 55%"></div><span>12:00</span></div>
                        <div class="bar-item"><div class="bar" style="height: 85%"></div><span>13:00</span></div>
                        <div class="bar-item"><div class="bar live" id="live-bar" style="height: 30%"></div><span>LIVE</span></div>
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
                <button class="btn-yakin" onclick="window.location.href = 'index.php'">Keluar</button>
            </div>
        </div>
    </div>
</body>
</html>