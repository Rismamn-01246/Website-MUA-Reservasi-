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

$fullname = trim($_POST['fullname'] ?? '');
$phone    = trim($_POST['phone']    ?? '');
$email    = trim($_POST['email']    ?? '');
$address  = trim($_POST['address']  ?? '');

if (!$fullname) {
    header('Location: dashboard-admin1.php?pelanggan=error&msg=' . urlencode('Nama pelanggan wajib diisi.'));
    exit;
}

$db = Database::getConnection();

$stmt = $db->prepare("
    INSERT INTO users (fullname, phone, email, address, role, created_at)
    VALUES (?, ?, ?, ?, 'customer', NOW())
");
$stmt->bind_param('ssss', $fullname, $phone, $email, $address);
$stmt->execute();
$stmt->close();

header('Location: dashboard-admin1.php?pelanggan=success&msg=' . urlencode('Pelanggan berhasil ditambahkan.'));
exit;
