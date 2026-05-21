<?php
session_start();
include 'koneksi.php';

// Membatasi akses hanya untuk customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['konser_id'])) {
    $konser_id = intval($_POST['konser_id']);
    $jumlah_tiket = intval($_POST['jumlah_tiket']);
    $user_email = $_SESSION['email'];

    if ($jumlah_tiket <= 0) {
        $jumlah_tiket = 1;
    }

    // 1. Ambil info konser saat ini untuk validasi kapasitas
    $query_konser = mysqli_query($conn, "SELECT * FROM konser WHERE id = $konser_id AND status != 'Arsip'");
    if (mysqli_num_rows($query_konser) == 0) {
        echo "<script>
                alert('Konser tidak ditemukan atau sudah tidak tersedia.');
                window.location.href = 'halaman-awal.php';
              </script>";
        exit();
    }

    $konser = mysqli_fetch_assoc($query_konser);
    $kapasitas = intval($konser['kapasitas']);
    $tiket_terjual = intval($konser['tiket_terjual']);
    $sisa_tiket = $kapasitas - $tiket_terjual;
    $harga = floatval($konser['harga']);

    // Validasi sisa kuota
    if ($sisa_tiket < $jumlah_tiket) {
        echo "<script>
                alert('Gagal memesan! Sisa tiket tidak mencukupi kuota pemesanan Anda.');
                window.location.href = 'detail-konser.php?id=$konser_id';
              </script>";
        exit();
    }

    // 2. Hitung total harga transaksi
    $total_harga = $jumlah_tiket * $harga;

    // Generate order_id unik (#NTB-YYMM-XXXX)
    $unique_suffix = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    $order_id = "NTB-" . date("ym") . "-" . $unique_suffix;

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // 3. Masukkan record pesanan baru ke tabel pesanan
        $insert_pesanan = "INSERT INTO pesanan (order_id, user_email, konser_id, jumlah_tiket, total_harga, status_bayar) 
                           VALUES ('$order_id', '$user_email', $konser_id, $jumlah_tiket, $total_harga, 'Lunas')";
        
        if (!mysqli_query($conn, $insert_pesanan)) {
            throw new Exception("Gagal membuat data pesanan.");
        }

        // 4. Update data tiket terjual di tabel konser
        $tiket_terjual_baru = $tiket_terjual + $jumlah_tiket;
        $sisa_tiket_baru = $kapasitas - $tiket_terjual_baru;

        // Recalculate status
        $status_baru = $konser['status'];
        if ($status_baru !== 'Selesai') {
            if ($sisa_tiket_baru <= 0) {
                $status_baru = 'Habis';
            } elseif ($sisa_tiket_baru <= 150 || $tiket_terjual_baru >= $kapasitas * 0.85) {
                $status_baru = 'Hampir Habis';
            } else {
                $status_baru = 'Tersedia';
            }
        }

        $update_konser = "UPDATE konser SET tiket_terjual = $tiket_terjual_baru, status = '$status_baru' WHERE id = $konser_id";
        if (!mysqli_query($conn, $update_konser)) {
            throw new Exception("Gagal memperbarui kuota tiket konser.");
        }

        // Commit transaksi
        mysqli_commit($conn);

        echo "<script>
                alert('Pemesanan Sukses! Tiket Anda berhasil dipesan.');
                window.location.href = 'tiket-saya.php';
              </script>";
        exit();

    } catch (Exception $e) {
        // Rollback transaksi jika gagal
        mysqli_rollback($conn);
        echo "<script>
                alert('Terjadi kesalahan sistem: " . $e->getMessage() . "');
                window.location.href = 'detail-konser.php?id=$konser_id';
              </script>";
        exit();
    }

} else {
    header("Location: halaman-awal.php");
    exit();
}
?>
