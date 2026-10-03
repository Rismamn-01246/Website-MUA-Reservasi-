<?php
session_start();
require_once __DIR__ . '/../app/config/Database.php';

// Cek session admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('ID tidak valid.'));
    exit;
}

$db = Database::getConnection();

// Ambil nama file dulu sebelum dihapus dari DB
$stmt = $db->prepare("SELECT foto FROM portofolio WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row) {
    // Hapus file fisik dari public/uploads/portofolio/
    $filePath = __DIR__ . '/uploads/portofolio/' . $row['foto'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Hapus dari database
    $del = $db->prepare("DELETE FROM portofolio WHERE id = ?");
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();

    header('Location: dashboard-admin1.php?portofolio=success&msg=' . urlencode('Portofolio berhasil dihapus.'));
} else {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Data tidak ditemukan.'));
}
exit;