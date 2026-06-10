<?php
session_start();
include '../config/koneksi.php';

// Membatasi akses hanya untuk customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['konser_id'])) {
    $konser_id = intval($_POST['konser_id']);
    $jumlah_tiket = intval($_POST['jumlah_tiket']);
    $bank = isset($_POST['bank']) ? $_POST['bank'] : 'Lainnya';
    $user_email = $_SESSION['email'];

    if ($jumlah_tiket <= 0) {
        $jumlah_tiket = 1;
    }

    // 1. Ambil info konser saat ini untuk validasi kapasitas
    $stmt_konser = $conn->prepare("SELECT * FROM konser WHERE id = ? AND status != 'Arsip'");
    $stmt_konser->bind_param("i", $konser_id);
    $stmt_konser->execute();
    $query_konser = $stmt_konser->get_result();
    if ($query_konser->num_rows == 0) {
        setcookie("flash_msg", "Konser tidak ditemukan atau sudah tidak tersedia.", time() + 5, "/");
        header("Location: ../user/beranda.php");
        exit();
    }

    $konser = mysqli_fetch_assoc($query_konser);
    
    // Validasi waktu konser (tidak boleh pesan jika sudah lewat)
    date_default_timezone_set('Asia/Makassar');
    $waktu_konser = strtotime($konser['tanggal'] . ' ' . $konser['waktu']);
    if ($waktu_konser < time()) {
        setcookie("flash_msg", "Gagal memesan! Acara ini sudah berakhir dan tiket tidak lagi tersedia.", time() + 5, "/");
        header("Location: ../user/detail-konser.php?id=$konser_id");
        exit();
    }

    $kapasitas = intval($konser['kapasitas']);
    $tiket_terjual = intval($konser['tiket_terjual']);
    $sisa_tiket = $kapasitas - $tiket_terjual;
    $harga = floatval($konser['harga']);

    // Validasi sisa kuota
    if ($sisa_tiket < $jumlah_tiket) {
        setcookie("flash_msg", "Gagal memesan! Sisa tiket tidak mencukupi kuota pemesanan Anda.", time() + 5, "/");
        header("Location: ../user/detail-konser.php?id=$konser_id");
        exit();
    }

    // 2. Hitung total harga transaksi
    $total_harga = $jumlah_tiket * $harga;

    // Generate order_id unik (#NTB-YYMM-XXXX)
    $unique_suffix = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    $order_id = "NTB-" . date("ym") . "-" . $unique_suffix;

    // Generate VA Number berdasarkan Bank
    $va_number = "";
    $rand_va = rand(1000000000, 9999999999);
    if ($bank === "BCA") {
        $va_number = "3901" . $rand_va;
    } elseif ($bank === "Mandiri") {
        $va_number = "89508" . $rand_va;
    } elseif ($bank === "BNI") {
        $va_number = "8098" . $rand_va;
    } elseif ($bank === "BRI") {
        $va_number = "2200" . $rand_va;
    } else {
        $va_number = "8000" . $rand_va;
    }

    $waktu_kadaluarsa = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // 3. Masukkan record pesanan baru ke tabel pesanan
        $status_bayar = 'Pending';
        $stmt_insert = $conn->prepare("INSERT INTO pesanan (order_id, user_email, konser_id, jumlah_tiket, total_harga, status_bayar, bank, va_number, waktu_kadaluarsa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("ssiidssss", $order_id, $user_email, $konser_id, $jumlah_tiket, $total_harga, $status_bayar, $bank, $va_number, $waktu_kadaluarsa);
        
        if (!$stmt_insert->execute()) {
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

        $stmt_update = $conn->prepare("UPDATE konser SET tiket_terjual = ?, status = ? WHERE id = ?");
        $stmt_update->bind_param("isi", $tiket_terjual_baru, $status_baru, $konser_id);
        if (!$stmt_update->execute()) {
            throw new Exception("Gagal memperbarui kuota tiket konser.");
        }

        // Commit transaksi
        mysqli_commit($conn);

        setcookie("flash_msg", "Pemesanan Sukses! Tiket Anda berhasil dipesan.", time() + 5, "/");
        header("Location: ../user/tiket-saya.php");
        exit();

    } catch (Exception $e) {
        // Rollback transaksi jika gagal
        mysqli_rollback($conn);
        setcookie("flash_msg", "Terjadi kesalahan sistem: " . $e->getMessage(), time() + 5, "/");
        header("Location: ../user/detail-konser.php?id=$konser_id");
        exit();
    }

} else {
    header("Location: ../user/beranda.php");
    exit();
}
?>
