<?php
session_start();
require_once '../app/config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$db = Database::getConnection(); // MySQLi

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard-admin1.php');
    exit;
}

$nama    = trim($_POST['nama']    ?? '');
$layanan = trim($_POST['layanan'] ?? '');
$rating  = (int)($_POST['rating'] ?? 5);
$ulasan  = trim($_POST['ulasan']  ?? '');

if (!$nama || !$ulasan) {
    header('Location: dashboard-admin1.php?testimoni=error&msg=' . urlencode('Nama dan ulasan wajib diisi.'));
    exit;
}

$rating = max(1, min(5, $rating));

// ── Ganti $pdo ke $db, dan PDO syntax ke MySQLi ─────────────────────
$stmt = $db->prepare("
    INSERT INTO testimoni (nama, layanan, rating, ulasan, status, created_at)
    VALUES (?, ?, ?, ?, 'tampil', NOW())
");
$stmt->bind_param('ssis', $nama, $layanan, $rating, $ulasan);
$stmt->execute();
$stmt->close();

header('Location: dashboard-admin1.php?testimoni=success&msg=' . urlencode('Testimoni berhasil ditambahkan.'));
exit;