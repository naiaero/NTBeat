<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'eo')) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['aksi'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['aksi'];
    $role = $_SESSION['role'];
    $email_user = $_SESSION['email'];
    $redirect_dir = ($role === 'admin') ? '../admin/' : '../eo/';

    if ($role === 'eo') {
        $stmt_cek = $conn->prepare("SELECT k.eo_email FROM pesanan p JOIN konser k ON p.konser_id = k.id WHERE p.order_id = ?");
        $stmt_cek->bind_param("s", $order_id);
        $stmt_cek->execute();
        $res_cek = $stmt_cek->get_result();
        if ($res_cek->num_rows == 0) {
            setcookie("flash_msg", "Pesanan tidak ditemukan.", time() + 5, "/");
            header("Location: " . $redirect_dir . "verifikasi-pembayaran.php");
            exit();
        }
        $row_cek = $res_cek->fetch_assoc();
        if ($row_cek['eo_email'] !== $email_user) {
            setcookie("flash_msg", "Akses ditolak. Ini bukan pesanan untuk konser Anda.", time() + 5, "/");
            header("Location: " . $redirect_dir . "verifikasi-pembayaran.php");
            exit();
        }
    }

    if ($action === 'setuju') {
        $stmt = $conn->prepare("UPDATE pesanan SET status_bayar = 'Lunas' WHERE order_id = ?");
        $stmt->bind_param("s", $order_id);
        if ($stmt->execute()) {
            setcookie("flash_msg", "Pesanan berhasil disetujui dan Lunas.", time() + 5, "/");
        } else {
            setcookie("flash_msg", "Gagal menyetujui pesanan.", time() + 5, "/");
        }
    } elseif ($action === 'tolak') {
        $konser_id = intval($_POST['konser_id']);
        $jml_tiket = intval($_POST['jumlah_tiket']);

        $conn->begin_transaction();
        try {
            $stmt_update = $conn->prepare("UPDATE pesanan SET status_bayar = 'Dibatalkan' WHERE order_id = ?");
            $stmt_update->bind_param("s", $order_id);
            $stmt_update->execute();

            $stmt_konser = $conn->prepare("UPDATE konser SET tiket_terjual = tiket_terjual - ? WHERE id = ?");
            $stmt_konser->bind_param("ii", $jml_tiket, $konser_id);
            $stmt_konser->execute();

            $stmt_cek_status = $conn->prepare("SELECT kapasitas, tiket_terjual, status FROM konser WHERE id = ?");
            $stmt_cek_status->bind_param("i", $konser_id);
            $stmt_cek_status->execute();
            $res = $stmt_cek_status->get_result()->fetch_assoc();

            if ($res['status'] !== 'Arsip' && $res['status'] !== 'Selesai') {
                $sisa = $res['kapasitas'] - $res['tiket_terjual'];
                $new_status = 'Tersedia';
                if ($sisa <= 0) {
                    $new_status = 'Habis';
                } elseif ($sisa <= 150 || $res['tiket_terjual'] >= $res['kapasitas'] * 0.85) {
                    $new_status = 'Hampir Habis';
                }

                if ($new_status !== $res['status']) {
                    $stmt_update_status = $conn->prepare("UPDATE konser SET status = ? WHERE id = ?");
                    $stmt_update_status->bind_param("si", $new_status, $konser_id);
                    $stmt_update_status->execute();
                }
            }

            $conn->commit();
            setcookie("flash_msg", "Pesanan berhasil ditolak (dibatalkan) dan kuota tiket dikembalikan.", time() + 5, "/");
        } catch (Exception $e) {
            $conn->rollback();
            setcookie("flash_msg", "Gagal menolak pesanan.", time() + 5, "/");
        }
    }
    header("Location: " . $redirect_dir . "verifikasi-pembayaran.php");
    exit();
} else {
    header("Location: ../auth/login.php");
    exit();
}
?>
