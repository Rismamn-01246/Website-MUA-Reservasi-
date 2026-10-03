<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../app/config/Database.php';

// ── DEBUG SEMENTARA — hapus setelah masalah ditemukan ────────────────
if (isset($_GET['debug'])) {
    header('Content-Type: application/json');
    echo json_encode($_SESSION);
    exit;
}
// ──────────────────────────────────────────────────────────────────

// ── Cek akses admin (versi aman, tidak error kalau key belum ada) ───
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$db     = Database::getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$id     = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if (!$id) {
    redirect_back('ID tidak valid.');
}

switch ($action) {
    case 'confirm':
        $status = 'confirmed';
        $stmt   = $db->prepare("UPDATE reservasi SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $stmt->close();
        redirect_success('Reservasi berhasil dikonfirmasi.');
        break;

    case 'update':
        $status  = trim($_POST['status'] ?? '');
        $notes   = trim($_POST['notes']  ?? '');
        $allowed = ['pending', 'confirmed', 'done', 'cancelled'];

        if (!in_array($status, $allowed)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Status tidak valid.']);
            exit;
        }

        $stmt = $db->prepare("UPDATE reservasi SET status = ?, notes = ? WHERE id = ?");
        $stmt->bind_param('ssi', $status, $notes, $id);
        $ok = $stmt->execute();
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['success' => $ok]);
        exit;

    case 'delete':
        $stmt = $db->prepare("DELETE FROM reservasi WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        redirect_success('Reservasi berhasil dihapus.');
        break;

    default:
        redirect_back('Aksi tidak dikenal.');
}

function redirect_success(string $msg): void
{
    $ref = $_SERVER['HTTP_REFERER'] ?? 'dashboard-admin1.php';
    $sep = str_contains($ref, '?') ? '&' : '?';
    header('Location: ' . $ref . $sep . 'reservasi=success&msg=' . urlencode($msg));
    exit;
}

function redirect_back(string $msg): void
{
    $ref = $_SERVER['HTTP_REFERER'] ?? 'dashboard-admin1.php';
    $sep = str_contains($ref, '?') ? '&' : '?';
    header('Location: ' . $ref . $sep . 'reservasi=error&msg=' . urlencode($msg));
    exit;
}