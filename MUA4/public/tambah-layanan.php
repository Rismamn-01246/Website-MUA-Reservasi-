<?php
session_start();
require_once '../app/config/Database.php';

// ── Fix 1: user_id + role ────────────────────────────────────────────
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard-admin1.php');
    exit;
}

$nama          = trim($_POST['nama']          ?? '');
$harga         = (int)($_POST['harga']        ?? 0);
$deskripsi     = trim($_POST['deskripsi']     ?? '');
$yang_termasuk = trim($_POST['yang_termasuk'] ?? '');
$status        = in_array($_POST['status'] ?? '', ['aktif','nonaktif'])
                 ? $_POST['status'] : 'aktif';

if (!$nama) {
    header('Location: dashboard-admin1.php?layanan=error&msg=' . urlencode('Nama layanan wajib diisi.'));
    exit;
}

// ── Fix 2: $pdo → $db, PDO syntax → MySQLi ──────────────────────────
$db = Database::getConnection();

$stmt = $db->prepare("
    INSERT INTO layanan (nama, harga, deskripsi, yang_termasuk, status)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param('sisss', $nama, $harga, $deskripsi, $yang_termasuk, $status);
$stmt->execute();
$stmt->close();

header('Location: dashboard-admin1.php?layanan=success&msg=' . urlencode('Layanan berhasil ditambahkan.'));
exit;