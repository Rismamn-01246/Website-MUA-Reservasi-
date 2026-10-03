<?php
session_start();
require_once '../app/config/Database.php';

// Proteksi: hanya admin yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Kalau customer, arahkan ke dashboard customer
    if (!empty($_SESSION['user_id'])) {
        header('Location: customer-dashboard.php');
    } else {
        header('Location: Login.php');
    }
    exit;
}

// Baca action dan id dari GET (delete via URL) atau POST (edit via form)
$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$id     = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));

if (!$id) {
    header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('ID pelanggan tidak valid.'));
    exit;
}

$db = Database::getConnection();

// ── Edit Pelanggan ────────────────────────────────────────────
if ($action === 'update') {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: dashboard-admin1.php');
        exit;
    }

    $fullname = trim($_POST['fullname'] ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $email    = trim($_POST['email']    ?? '');
    $address  = trim($_POST['address']  ?? '');

    if (!$fullname) {
        header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('Nama pelanggan wajib diisi.'));
        exit;
    }

    // Cek apakah email sudah dipakai user lain (jika email diisi)
    if ($email !== '') {
        $cekEmail = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $cekEmail->bind_param('si', $email, $id);
        $cekEmail->execute();
        $cekEmail->store_result();
        if ($cekEmail->num_rows > 0) {
            $cekEmail->close();
            header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('Email sudah digunakan oleh akun lain.'));
            exit;
        }
        $cekEmail->close();
    }

    $stmt = $db->prepare("UPDATE users SET fullname = ?, phone = ?, email = ?, address = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $fullname, $phone, $email, $address, $id);
    $ok = $stmt->execute();
    $stmt->close();

    if ($ok) {
        header('Location: dashboard-admin1.php?pelanggan=success&msg=' . urlencode('Data pelanggan berhasil diperbarui.'));
    } else {
        header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('Gagal memperbarui data pelanggan.'));
    }
    exit;
}

// ── Hapus Pelanggan ───────────────────────────────────────────
if ($action === 'delete') {

    // 1. Pastikan pelanggan bukan admin (jangan sampai hapus akun admin)
    $cek = $db->prepare("SELECT role FROM users WHERE id = ?");
    $cek->bind_param('i', $id);
    $cek->execute();
    $result = $cek->get_result();
    $user   = $result->fetch_assoc();
    $cek->close();

    if (!$user) {
        header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('Pelanggan tidak ditemukan.'));
        exit;
    }

    if ($user['role'] === 'admin') {
        header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('Tidak bisa menghapus akun admin.'));
        exit;
    }

    // 2. Hapus reservasi milik pelanggan ini dulu (foreign key)
    $hapusRsv = $db->prepare("DELETE FROM reservasi WHERE user_id = ?");
    $hapusRsv->bind_param('i', $id);
    $hapusRsv->execute();
    $hapusRsv->close();

    // 3. Hapus testimoni milik pelanggan ini (jika ada relasi)
    $cekKolom = $db->query("SHOW COLUMNS FROM testimoni LIKE 'user_id'");
    if ($cekKolom && $cekKolom->num_rows > 0) {
        $hapusTesti = $db->prepare("DELETE FROM testimoni WHERE user_id = ?");
        $hapusTesti->bind_param('i', $id);
        $hapusTesti->execute();
        $hapusTesti->close();
    }

    // 4. Hapus user
    $hapus = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $hapus->bind_param('i', $id);
    $hapus->execute();
    $affected = $hapus->affected_rows;
    $hapus->close();

    if ($affected > 0) {
        header('Location: dashboard-admin1.php?pelanggan=success&msg=' . urlencode('Pelanggan berhasil dihapus.'));
    } else {
        header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('Gagal menghapus pelanggan.'));
    }
    exit;
}

// Jika action tidak dikenal
header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('Aksi tidak dikenal.'));
exit;
