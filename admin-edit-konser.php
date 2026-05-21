<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id_konser = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = mysqli_query($conn, "SELECT * FROM konser WHERE id = $id_konser");
if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    $nama_event = $row['nama_konser'];
    $lineup = $row['lineup'];
    $tanggal = $row['tanggal'];
    $waktu = $row['waktu'];
    $venue = $row['lokasi'];
    $harga = $row['harga'];
    $kapasitas = $row['kapasitas'];
    
    $poster_lama = $row['poster'];
    $poster_display_path = "assets/img/" . $row['poster'];
    if (empty($row['poster']) || !file_exists($poster_display_path)) {
        $poster_display_path = "assets/img/default-poster.jpg";
    }
} else {
    echo "<script>
            alert('Konser tidak ditemukan.');
            window.location.href = 'admin-kelola-konser.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Konser #<?php echo $id_konser; ?> - Admin NTBeat</title>
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
                <h2>Ubah Informasi Konser</h2>
                <p>Memperbarui data untuk ID Konser: <strong>#<?php echo $id_konser; ?></strong></p>
            </div>

            <div class="ps-card">
                <!-- Tambahkan ID tersembunyi agar backend tahu data mana yang di-update -->
                <form action="update-konser-proses.php" method="POST" enctype="multipart/form-data" class="ps-form" id="concert-form">
                    <input type="hidden" name="id" value="<?php echo $id_konser; ?>">
                    
                    <div class="ps-avatar-section">
                        <div class="poster-upload-wrapper">
                            <!-- PERBAIKAN 1: Menampilkan gambar lama sebagai background default preview -->
                            <div class="poster-preview" id="imagePreview" style="background-image: url('<?php echo $poster_display_path; ?>'); background-size: cover; background-position: center; border: none;">
                                <!-- Teks dihilangkan jika sudah ada poster lama -->
                            </div>
                            <label for="poster-input" class="ps-edit-icon">
                                📸
                                <input type="file" id="poster-input" name="poster" hidden accept="image/*">
                            </label>
                        </div>
                    </div>
                    <p style="text-align: center; color: #888; font-size: 0.8rem; margin-bottom: 30px;">
                        Klik ikon kamera untuk mengganti poster (Rekomendasi 3:4)
                    </p>

                    <!-- PERBAIKAN 2: Menggunakan atribut value="<?php ?>" untuk memunculkan data lama -->
                    <div class="ps-form-group">
                        <label>Nama Event</label>
                        <input type="text" name="nama_konser" class="ps-input" value="<?php echo htmlspecialchars($nama_event); ?>" required>
                    </div>

                    <!-- PERBAIKAN 3: Textarea TIDAK menggunakan atribut value, nilainya ditaruh di tengah tag -->
                    <div class="ps-form-group" style="align-items: flex-start;">
                        <label>Line-up Artis</label>
                        <textarea name="lineup" class="ps-input" rows="3"><?php echo htmlspecialchars($lineup); ?></textarea>
                    </div>

                    <div class="form-row-double">
                        <div class="ps-form-group">
                            <label>Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal" class="ps-input" value="<?php echo $tanggal; ?>" required>
                        </div>
                        <div class="ps-form-group">
                            <label>Waktu Pelaksanaan</label>
                            <input type="time" name="waktu" class="ps-input" value="<?php echo $waktu; ?>" required>
                        </div>
                    </div>

                    <div class="ps-form-group">
                        <label>Lokasi / Venue</label>
                        <input type="text" name="lokasi" class="ps-input" value="<?php echo htmlspecialchars($venue); ?>" required>
                    </div>

                    <h3 class="ps-subheading">Kapasitas & Penjualan</h3>

                    <div class="form-row-double">
                        <div class="ps-form-group">
                            <label>Harga Tiket (Rp)</label>
                            <input type="number" name="harga" class="ps-input" value="<?php echo intval($harga); ?>" required>
                        </div>
                        <div class="ps-form-group">
                            <label>Total Kapasitas</label>
                            <input type="number" name="kapasitas" class="ps-input" value="<?php echo $kapasitas; ?>" required>
                        </div>
                    </div>

                    <div class="ps-action-bar">
                        <button type="button" class="btn-ps-cancel" onclick="window.location.href='admin-kelola-konser.php'">Batal</button>
                        <button type="submit" class="btn-ps-save" id="btn-update" name="submit_update">Perbarui Data Konser</button>
                    </div>
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