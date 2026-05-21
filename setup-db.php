<?php
include 'koneksi.php';

echo "<h2>NTBeat Database Initializer</h2>";

// 1. Buat Tabel users
$query_users = "CREATE TABLE IF NOT EXISTS `users` (
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL,
  `foto` varchar(255) DEFAULT 'default-avatar.png',
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if (mysqli_query($conn, $query_users)) {
    echo "✔ Tabel 'users' berhasil dibuat atau sudah ada.<br>";
    // Pastikan kolom 'foto' ada jika tabel sudah dibuat sebelumnya tanpa kolom tersebut
    $check_column = mysqli_query($conn, "SHOW COLUMNS FROM `users` LIKE 'foto'");
    if (mysqli_num_rows($check_column) == 0) {
        mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `foto` varchar(255) DEFAULT 'default-avatar.png'");
        echo "✔ Kolom 'foto' berhasil ditambahkan ke tabel 'users'.<br>";
    }
} else {
    echo "❌ Gagal membuat tabel 'users': " . mysqli_error($conn) . "<br>";
}

// 2. Buat Tabel konser
$query_konser = "CREATE TABLE IF NOT EXISTS `konser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_konser` varchar(150) NOT NULL,
  `lineup` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `tiket_terjual` int(11) DEFAULT 0,
  `poster` varchar(255) DEFAULT 'default-poster.png',
  `status` enum('Tersedia','Hampir Habis','Habis','Selesai','Arsip') DEFAULT 'Tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $query_konser)) {
    echo "✔ Tabel 'konser' berhasil dibuat atau sudah ada.<br>";
} else {
    echo "❌ Gagal membuat tabel 'konser': " . mysqli_error($conn) . "<br>";
}

// 3. Buat Tabel pesanan
$query_pesanan = "CREATE TABLE IF NOT EXISTS `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `konser_id` int(11) NOT NULL,
  `jumlah_tiket` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status_bayar` enum('Pending','Lunas','Dibatalkan') DEFAULT 'Lunas',
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `fk_pesanan_konser` (`konser_id`),
  CONSTRAINT `fk_pesanan_konser` FOREIGN KEY (`konser_id`) REFERENCES `konser` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $query_pesanan)) {
    echo "✔ Tabel 'pesanan' berhasil dibuat atau sudah ada.<br>";
} else {
    echo "❌ Gagal membuat tabel 'pesanan': " . mysqli_error($conn) . "<br>";
}

// 4. Masukkan Data Sampel Konser jika tabel masih kosong
$check_empty = mysqli_query($conn, "SELECT COUNT(*) as total FROM konser");
$row = mysqli_fetch_assoc($check_empty);

if ($row['total'] == 0) {
    $insert_queries = [
        "INSERT INTO `konser` (`nama_konser`, `lineup`, `tanggal`, `waktu`, `lokasi`, `harga`, `kapasitas`, `tiket_terjual`, `status`, `poster`) VALUES
        ('Mataram Sound Wave', 'Pamungkas, Hindia, Isyana Sarasvati', '2026-08-25', '19:00:00', 'Eks Bandara Selaparang', 150000.00, 1000, 880, 'Hampir Habis', 'default-poster.png')",
        
        "INSERT INTO `konser` (`nama_konser`, `lineup`, `tanggal`, `waktu`, `lokasi`, `harga`, `kapasitas`, `tiket_terjual`, `status`, `poster`) VALUES
        ('Senggigi Jazz Night', 'Tompi, Raisa, Maliq & D\'Essentials', '2026-06-20', '20:00:00', 'Pantai Senggigi', 150000.00, 1000, 540, 'Tersedia', 'default-poster.png')",
        
        "INSERT INTO `konser` (`nama_konser`, `lineup`, `tanggal`, `waktu`, `lokasi`, `harga`, `kapasitas`, `tiket_terjual`, `status`, `poster`) VALUES
        ('Festival Budaya Sasak', 'Gendang Beleq community, Local Musicians', '2026-07-12', '16:00:00', 'Lapangan Mataram', 100000.00, 1500, 120, 'Tersedia', 'default-poster.png')",

        "INSERT INTO `konser` (`nama_konser`, `lineup`, `tanggal`, `waktu`, `lokasi`, `harga`, `kapasitas`, `tiket_terjual`, `status`, `poster`) VALUES
        ('Symphony of Lombok', 'Lombok Philharmonic Orchestra', '2026-05-15', '19:00:00', 'Taman Budaya NTB, Mataram', 250000.00, 5000, 4955, 'Hampir Habis', 'default-poster.png')",
        
        "INSERT INTO `konser` (`nama_konser`, `lineup`, `tanggal`, `waktu`, `lokasi`, `harga`, `kapasitas`, `tiket_terjual`, `status`, `poster`) VALUES
        ('NCT Dream Live on Screen', 'NCT Dream', '2026-06-20', '18:30:00', 'Epicentrum Mall Atrium', 150000.00, 2000, 1420, 'Tersedia', 'default-poster.png')"
    ];

    $success = true;
    foreach ($insert_queries as $q) {
        if (!mysqli_query($conn, $q)) {
            $success = false;
            echo "❌ Gagal memasukkan data sampel: " . mysqli_error($conn) . "<br>";
        }
    }
    if ($success) {
        echo "✔ Data sampel konser berhasil dimasukkan!<br>";
    }
} else {
    echo "ℹ Tabel 'konser' sudah berisi data, melewati pengisian data sampel.<br>";
}

// 5. Pastikan ada user admin@ntbeat.com dengan password admin123 terdaftar
$check_admin = mysqli_query($conn, "SELECT email FROM users WHERE email='admin@ntbeat.com'");
if (mysqli_num_rows($check_admin) == 0) {
    $hashed_admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO users (nama, email, password, role) VALUES ('Administrator', 'admin@ntbeat.com', '$hashed_admin_pass', 'admin')";
    if (mysqli_query($conn, $insert_admin)) {
        echo "✔ User Admin (admin@ntbeat.com / admin123) berhasil didaftarkan di database.<br>";
    } else {
        echo "❌ Gagal mendaftarkan User Admin: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "ℹ User Admin sudah terdaftar di database.<br>";
}

echo "<br><b>Inisialisasi selesai! Silakan hapus file ini demi keamanan atau langsung mulai pengujian.</b>";
?>
