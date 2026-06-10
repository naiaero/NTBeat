<?php
$conn = mysqli_connect('localhost', 'root', '', 'ntbeat');
if (!$conn) { die("Koneksi gagal: " . mysqli_connect_error()); }

$sql = "CREATE TABLE IF NOT EXISTS pengunjung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL UNIQUE,
    jumlah INT DEFAULT 0
)";

if (mysqli_query($conn, $sql)) {
    echo "Tabel pengunjung berhasil dibuat/ditemukan.\n";
} else {
    echo "Gagal: " . mysqli_error($conn) . "\n";
}

// Generate dummy data for the last 7 days so the chart looks nice
$today = date('Y-m-d');
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $jumlah = rand(50, 200); // random dummy data
    mysqli_query($conn, "INSERT IGNORE INTO pengunjung (tanggal, jumlah) VALUES ('$date', $jumlah)");
}
echo "Dummy data generated.\n";
?>
