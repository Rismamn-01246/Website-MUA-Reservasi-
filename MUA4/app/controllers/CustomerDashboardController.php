<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ReservasiModel.php';

// 1. Cek apakah sudah login
if (empty($_SESSION['user'])) {
    header('Location: Login.php');
    exit;
}

// 2. Ambil nama lengkap (kalau session belum punya, ambil dari DB)
$username = $_SESSION['user'];

if (!empty($_SESSION['fullname'])) {
    $fullname = $_SESSION['fullname'];
} else {
    $userModel = new UserModel();
    $fullname  = $userModel->getFullnameByUsername($username) ?: $username;
}

$fullnameSafe = htmlspecialchars($fullname);

// 3. Ambil riwayat reservasi milik user ini (untuk halaman "Riwayat")
$riwayatReservasi = [];
if (!empty($_SESSION['user_id'])) {
    $reservasiModel   = new ReservasiModel();
    $riwayatReservasi = $reservasiModel->getByUserId((int) $_SESSION['user_id']);
}