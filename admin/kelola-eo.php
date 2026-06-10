<?php
session_start();
include '../config/koneksi.php';

// Verifikasi role admin
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
if (!empty($foto_user) && file_exists($foto_path) && $foto_user !== 'default-avatar.png' && $foto_user !== 'logo.png') {
    $avatar_style = "background-image: url('$foto_path'); background-size: cover; background-position: center; color: transparent; border: 1px solid #d4af37;";
}

$query_eo = mysqli_query($conn, "SELECT nama, email, alamat, foto, status FROM users WHERE role = 'eo' ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event Organizer - Admin NTBeat</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <script src="../assets/script/script.js"></script>
    <style>
        .table-container {
            overflow-x: auto;
            background: #1a1a1a;
            border-radius: 8px;
            border: 1px solid #333;
            margin-top: 20px;
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
        .auth-form input, .auth-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #444;
            background: #222;
            color: #fff;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 0.95rem;
        }
        .auth-form textarea {
            resize: vertical;
            min-height: 100px;
        }
        .auth-form label {
            display: block;
            margin-bottom: 8px;
            color: #ddd;
            font-weight: 500;
        }
        .form-container {
            background: #1a1a1a;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #333;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <header class="header-user">
        <div class="logo-area" onclick="window.location.href = 'dashboard.php'">
            <img src="../assets/img/logo.png" alt="NTBeat Logo">
            <label>NTBeat</label>
        </div>
        <div class="user-profile-nav">
            <span>Halo, <?php echo htmlspecialchars($nama_depan); ?>!</span>
            <div class="avatar-placeholder" onclick="window.location.href = 'profil.php'" style="<?php echo $avatar_style; ?>"><?php echo $inisial; ?></div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li onclick="window.location.href = 'dashboard.php'">Dashboard Platform</li>
                <li class="active" onclick="window.location.href = 'kelola-eo.php'">Kelola Pengguna EO</li>
                <li onclick="window.location.href = 'form-konser.php'">Tambah Acara Baru</li>
                <li onclick="window.location.href = 'kelola-konser.php'">Kelola Data Konser</li>
                <li onclick="window.location.href = 'arsip.php'">Arsip Penyelenggaraan</li>
                <li onclick="window.location.href = 'profil.php'">Pengaturan Profil</li>
                <li onclick="openLogoutModal()">Keluar</li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="section-header" style="margin-bottom: 20px;">
                <h2>Manajemen Akun Event Organizer (EO)</h2>
                <p>Tambah dan kelola daftar penyelenggara acara yang diizinkan menjual tiket di platform ini.</p>
            </div>

            <!-- Form Tambah EO -->
            <div class="form-container">
                <h3 style="color: #f1c40f; margin-bottom: 15px;">Buat Akun Penyelenggara Baru</h3>
                <form action="../actions/tambah-eo-proses.php" method="POST" class="auth-form">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label>Nama Perusahaan / Penyelenggara</label>
                            <input type="text" name="nama" required placeholder="Contoh: Nada Terang Festival">
                        </div>
                        <div style="flex: 1;">
                            <label>Email Penyelenggara</label>
                            <input type="email" name="email" required placeholder="Contoh: contact@nadaterang.com">
                        </div>
                    </div>
                    
                    <div style="margin-top: 5px;">
                        <label>Password Akun</label>
                        <input type="password" name="password" required placeholder="Buat kata sandi aman">
                    </div>

                    <div style="margin-top: 5px;">
                        <label>Alamat Lengkap Perusahaan</label>
                        <textarea name="alamat" required placeholder="Masukkan alamat operasional penyelenggara..."></textarea>
                    </div>
                    
                    <button type="submit" class="auth-btn-submit" style="width: auto; padding: 10px 25px; margin-top: 10px;">Tambahkan Penyelenggara</button>
                </form>
            </div>

            <h3 style="color: #f1c40f;">Daftar Penyelenggara (EO) yang Terdaftar</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nama Penyelenggara</th>
                            <th>Email Akun</th>
                            <th>Alamat Operasional</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_eo) > 0) {
                            while ($row = mysqli_fetch_assoc($query_eo)) { 
                                $foto_eo = empty($row['foto']) ? 'default-avatar.png' : $row['foto'];
                                $inisial_eo = strtoupper(substr($row['nama'], 0, 1));
                                $status_eo = isset($row['status']) ? $row['status'] : 'aktif';
                            ?>
                                <tr>
                                    <td>
                                        <?php if ($foto_eo !== 'default-avatar.png' && file_exists("../assets/img/" . $foto_eo)) { ?>
                                            <img src="../assets/img/<?php echo htmlspecialchars($foto_eo); ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #d4af37;">
                                        <?php } else { ?>
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #333; color: #f1c40f; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; border: 1px solid #d4af37;"><?php echo $inisial_eo; ?></div>
                                        <?php } ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($row['alamat'])); ?></td>
                                    <td>
                                        <?php if ($status_eo == 'aktif') { ?>
                                            <span style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">Aktif</span>
                                        <?php } else { ?>
                                            <span style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">Nonaktif</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <button class="auth-btn-submit" style="padding: 6px 12px; font-size: 0.8rem; width: auto; margin: 0; background: #3498db; color: white; border: none;" onclick="openEditModal('<?php echo htmlspecialchars(addslashes($row['email'])); ?>', '<?php echo htmlspecialchars(addslashes($row['nama'])); ?>', '<?php echo htmlspecialchars(addslashes(str_replace(array("\r", "\n"), array('', '\\n'), $row['alamat']))); ?>')">Edit</button>
                                            
                                            <?php if($status_eo == 'aktif') { ?>
                                            <button class="auth-btn-submit" style="padding: 6px 12px; font-size: 0.8rem; width: auto; margin: 0; background: #e74c3c; color: white; border: none;" onclick="if(confirm('Yakin menonaktifkan akun EO ini? Mereka tidak akan bisa login ke sistem.')) window.location.href='../actions/nonaktif-eo-proses.php?email=<?php echo urlencode($row['email']); ?>&aksi=nonaktifkan'">Nonaktifkan</button>
                                            <?php } else { ?>
                                            <button class="auth-btn-submit" style="padding: 6px 12px; font-size: 0.8rem; width: auto; margin: 0; background: #2ecc71; color: white; border: none;" onclick="window.location.href='../actions/nonaktif-eo-proses.php?email=<?php echo urlencode($row['email']); ?>&aksi=aktifkan'">Aktifkan</button>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr><td colspan="6" style="text-align: center; color: #888;">Belum ada penyelenggara acara (EO) yang terdaftar di sistem.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
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

    <!-- Modal Edit EO -->
    <div id="editEoModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div class="form-container" style="width: 100%; max-width: 500px; margin: 0; position: relative;">
            <button onclick="closeEditModal()" style="position: absolute; right: 20px; top: 20px; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            <h3 style="color: #f1c40f; margin-bottom: 15px;">Edit Data Penyelenggara</h3>
            <form action="../actions/edit-eo-proses.php" method="POST" class="auth-form">
                <input type="hidden" name="email_lama" id="edit_email_lama">
                <label>Nama Perusahaan / Penyelenggara</label>
                <input type="text" name="nama" id="edit_nama" required>
                
                <label>Email Penyelenggara</label>
                <input type="email" name="email" id="edit_email" required>
                
                <label>Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" placeholder="Ketik sandi baru...">
                
                <label>Alamat Lengkap Perusahaan</label>
                <textarea name="alamat" id="edit_alamat" required></textarea>
                
                <div style="display: flex; gap: 15px; margin-top: 15px; justify-content: flex-end;">
                    <button type="button" class="auth-btn-submit" style="width: auto; padding: 10px 20px; background: #444; margin: 0;" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="auth-btn-submit" style="width: auto; padding: 10px 20px; margin: 0;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(email, nama, alamat) {
            document.getElementById('edit_email_lama').value = email;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_alamat').value = alamat;
            document.getElementById('editEoModal').style.display = 'flex';
        }
        function closeEditModal() {
            document.getElementById('editEoModal').style.display = 'none';
        }
    </script>
</body>
</html>
