<?php
session_start();
require_once '../app/config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard-admin1.php');
    exit;
}

$fullname       = trim($_POST['fullname']       ?? '');
$phone          = trim($_POST['phone']          ?? '');
$service        = trim($_POST['service']        ?? '');
$reservasi_date = trim($_POST['reservasi_date'] ?? '');
$reservasi_time = trim($_POST['reservasi_time'] ?? '');
$address        = trim($_POST['address']        ?? '');
$notes          = trim($_POST['notes']          ?? '');

if (!$fullname || !$service || !$reservasi_date || !$address) {
    header('Location: dashboard-admin1.php?reservasi=error&msg=' . urlencode('Data wajib tidak lengkap.'));
    exit;
}

$db = Database::getConnection();

// Ambil harga dari tabel layanan
$stmtL = $db->prepare("SELECT harga FROM layanan WHERE nama = ? LIMIT 1");
$stmtL->bind_param('s', $service);
$stmtL->execute();
$stmtL->bind_result($harga);
$stmtL->fetch();
$stmtL->close();
$harga = $harga ?: 0;

$stmt = $db->prepare("
    INSERT INTO reservasi (fullname, phone, service, reservasi_date, reservasi_time, address, notes, price, status, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
");
$stmt->bind_param('sssssssd', $fullname, $phone, $service, $reservasi_date, $reservasi_time, $address, $notes, $harga);
$stmt->execute();
$stmt->close();

header('Location: dashboard-admin1.php?reservasi=success&msg=' . urlencode('Reservasi berhasil ditambahkan.'));
exit;