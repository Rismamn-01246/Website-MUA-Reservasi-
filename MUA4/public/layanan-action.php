<?php
session_start();
require_once __DIR__ . '/../app/config/Database.php';

$db = Database::getConnection();

// ── Cek session: gunakan 'user_id' + 'role' (sesuai login) ──────────
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$id     = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if (!$id) {
    redirect_back('ID tidak valid.');
}

switch ($action) {

    //edit
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect_back('Metode tidak valid.');
        }
        $nama          = trim($_POST['nama']          ?? '');
        $harga         = (int)($_POST['harga']        ?? 0);
        $deskripsi     = trim($_POST['deskripsi']     ?? '');
        $yang_termasuk = trim($_POST['yang_termasuk'] ?? '');
        $status        = in_array($_POST['status'] ?? '', ['aktif','nonaktif'])
                         ? $_POST['status'] : 'aktif';

        if (!$nama || $harga < 0) {
            redirect_back('Nama dan harga wajib diisi.');
        }

        $stmt = $db->prepare("
            UPDATE layanan
               SET nama = ?, harga = ?, deskripsi = ?, yang_termasuk = ?, status = ?
             WHERE id = ?
        ");
        $stmt->bind_param('sisssi', $nama, $harga, $deskripsi, $yang_termasuk, $status, $id);
        $stmt->execute();
        $stmt->close();
        redirect_success('Layanan berhasil diperbarui.');
        break;

    case 'status':
        $status = in_array($_GET['status'] ?? '', ['aktif','nonaktif'])
                ? $_GET['status'] : null;
        if (!$status) {
            redirect_back('Status tidak valid.');
        }
        $stmt = $db->prepare("UPDATE layanan SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $stmt->close();
        redirect_success('Status layanan diperbarui.');
        break;

        //hapus layanan
    case 'delete':
        $stmt = $db->prepare("DELETE FROM layanan WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        redirect_success('Layanan berhasil dihapus.');
        break;

    default:
        redirect_back('Aksi tidak dikenal.');
}

function redirect_success(string $msg): void {
    $ref = $_SERVER['HTTP_REFERER'] ?? 'dashboard-admin1.php';
    $sep = str_contains($ref, '?') ? '&' : '?';
    header('Location: ' . $ref . $sep . 'layanan=success&msg=' . urlencode($msg));
    exit;
}

function redirect_back(string $msg): void {
    $ref = $_SERVER['HTTP_REFERER'] ?? 'dashboard-admin1.php';
    $sep = str_contains($ref, '?') ? '&' : '?';
    header('Location: ' . $ref . $sep . 'layanan=error&msg=' . urlencode($msg));
    exit;
}