<?php
session_start();
require_once __DIR__ . '/../../models/PengaturanModel.php';

// Pastikan hanya admin yang login bisa mengakses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/admin/dashboard-admin1.php');
    exit;
}

$pengaturanModel = new PengaturanModel();

// ── Ambil & sanitasi input ──────────────────────────────────
$data = [
    'nama_bisnis' => trim($_POST['nama_bisnis'] ?? ''),
    'nama_admin'  => trim($_POST['nama_admin'] ?? ''),
    'whatsapp'    => trim($_POST['whatsapp'] ?? ''),
    'email'       => trim($_POST['email'] ?? ''),
    'lokasi'      => trim($_POST['lokasi'] ?? ''),
    'bio'         => trim($_POST['bio'] ?? ''),
    'jam_buka'    => trim($_POST['jam_buka'] ?? ''),
    'jam_tutup'   => trim($_POST['jam_tutup'] ?? ''),
];

// ── Update data pengaturan utama ────────────────────────────
$ok = $pengaturanModel->update($data);

// ── Proses ganti password (opsional) ────────────────────────
$oldPassword     = $_POST['old_password'] ?? '';
$newPassword     = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$passwordError = '';

if ($oldPassword !== '' || $newPassword !== '' || $confirmPassword !== '') {
    require_once __DIR__ . '/../../models/UserModel.php';
    $userModel = new UserModel();

    if ($newPassword !== $confirmPassword) {
        $passwordError = 'Konfirmasi kata sandi tidak cocok.';
    } elseif (strlen($newPassword) < 6) {
        $passwordError = 'Kata sandi baru minimal 6 karakter.';
    } else {
        $valid = $userModel->verifyPassword((int) $_SESSION['user_id'], $oldPassword);

        if (!$valid) {
            $passwordError = 'Kata sandi lama tidak sesuai.';
        } else {
            // PERBAIKAN: hash password baru sebelum disimpan
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $userModel->updatePassword((int) $_SESSION['user_id'], $hashedPassword);
        }
    }
}

// ── Redirect kembali dengan status ──────────────────────────
$status = ($ok && $passwordError === '') ? 'success' : 'error';
$msg    = $passwordError !== '' ? urlencode($passwordError) : '';

header('Location: ../../views/admin/dashboard-admin1.php?pengaturan=' . $status . ($msg ? '&msg=' . $msg : ''));
exit;