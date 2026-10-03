<?php
session_start();
require_once '../app/config/Database.php';

// ── Fix 1: gunakan user_id + cek role admin ─────────────────────────
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

// ── Fix 2: ambil koneksi yang benar ─────────────────────────────────
$db = Database::getConnection(); // MySQLi

$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

if (!$id) {
    redirect_back('ID tidak valid.');
}

switch ($action) {

    case 'status':
        $status = in_array($_GET['status'] ?? '', ['tampil','sembunyikan'])
                  ? $_GET['status'] : null;
        if (!$status) redirect_back('Status tidak valid.');

        // ── Fix 3: ganti $pdo->prepare ke MySQLi ────────────────────
        $stmt = $db->prepare("UPDATE testimoni SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $stmt->close();
        redirect_success('Status testimoni diperbarui.');
        break;

        //hapus testimoni
    case 'delete':
        $stmt = $db->prepare("DELETE FROM testimoni WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        redirect_success('Testimoni berhasil dihapus.');
        break;

    default:
        redirect_back('Aksi tidak dikenal.');
}

function redirect_success(string $msg): void {
    $ref = $_SERVER['HTTP_REFERER'] ?? 'dashboard-admin1.php';
    header('Location: ' . $ref . (str_contains($ref,'?') ? '&' : '?') . 'testimoni=success&msg=' . urlencode($msg));
    exit;
}

function redirect_back(string $msg): void {
    $ref = $_SERVER['HTTP_REFERER'] ?? 'dashboard-admin1.php';
    header('Location: ' . $ref . (str_contains($ref,'?') ? '&' : '?') . 'testimoni=error&msg=' . urlencode($msg));
    exit;
}