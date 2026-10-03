<?php

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../models/ReservasiModel.php';

// Pastikan hanya admin yang login bisa mengakses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$reservasiModel = new ReservasiModel();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id     = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

switch ($action) {

    // ── Update status + catatan (dipanggil via fetch/AJAX dari modal detail) ──
    case 'update':
        $status = $_POST['status'] ?? '';
        $notes  = $_POST['notes'] ?? '';

        $allowedStatus = ['pending', 'confirmed', 'done', 'cancelled'];
        if (!in_array($status, $allowedStatus, true)) {
            echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
            exit;
        }

        $ok = $reservasiModel->updateStatusAndNotes($id, $status, $notes);
        echo json_encode(['success' => $ok]);
        exit;

    // ── Konfirmasi reservasi (link <a>, redirect biasa) ──
    case 'confirm':
        $reservasiModel->updateStatus($id, 'confirmed');
        header('Content-Type: text/html');
        header('Location: dashboard-admin1.php?msg=confirmed');
        exit;

    // ── Hapus reservasi (link <a>, redirect biasa) ──
    case 'delete':
        $reservasiModel->delete($id);
        header('Content-Type: text/html');
        header('Location: dashboard-admin1.php?msg=deleted');
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal']);
        exit;
}