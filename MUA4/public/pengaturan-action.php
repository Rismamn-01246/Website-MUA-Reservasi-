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

$db_obj = new Database();
$conn   = $db_obj->getConnection();

// ── Ambil & sanitasi input ──────────────────────────────────
$nama_bisnis = trim($_POST['nama_bisnis'] ?? '');
$nama_admin  = trim($_POST['nama_admin']  ?? '');
$whatsapp    = trim($_POST['whatsapp']    ?? '');
$email       = trim($_POST['email']       ?? '');
$lokasi      = trim($_POST['lokasi']      ?? '');
$bio         = trim($_POST['bio']         ?? '');
$jam_buka    = trim($_POST['jam_buka']    ?? '');
$jam_tutup   = trim($_POST['jam_tutup']   ?? '');

// ── Cek apakah baris pengaturan sudah ada ──────────────────
$cek = mysqli_query($conn, "SELECT id FROM pengaturan LIMIT 1");
$exists = mysqli_fetch_assoc($cek);

if ($exists) {
    // UPDATE
    $stmt = mysqli_prepare($conn,
        "UPDATE pengaturan SET
            nama_bisnis = ?, nama_admin = ?, whatsapp = ?,
            email = ?, lokasi = ?, bio = ?,
            jam_buka = ?, jam_tutup = ?
         WHERE id = ?"
    );
    $pengaturanId = (int)$exists['id'];
    mysqli_stmt_bind_param($stmt, 'ssssssssi',
        $nama_bisnis, $nama_admin, $whatsapp,
        $email, $lokasi, $bio,
        $jam_buka, $jam_tutup, $pengaturanId
    );
} else {
    // INSERT pertama kali
    $stmt = mysqli_prepare($conn,
        "INSERT INTO pengaturan
            (nama_bisnis, nama_admin, whatsapp, email, lokasi, bio, jam_buka, jam_tutup)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'ssssssss',
        $nama_bisnis, $nama_admin, $whatsapp,
        $email, $lokasi, $bio,
        $jam_buka, $jam_tutup
    );
}

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// ── Proses ganti password (opsional) ────────────────────────
$oldPassword     = $_POST['old_password']     ?? '';
$newPassword     = $_POST['new_password']     ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$passwordError   = '';

if ($oldPassword !== '' || $newPassword !== '' || $confirmPassword !== '') {

    if ($newPassword !== $confirmPassword) {
        $passwordError = 'Konfirmasi kata sandi tidak cocok.';
    } elseif (strlen($newPassword) < 6) {
        $passwordError = 'Kata sandi baru minimal 6 karakter.';
    } else {
        // Ambil hash password lama dari DB
        $stmtUser = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
        $userId   = (int)$_SESSION['user_id'];
        mysqli_stmt_bind_param($stmtUser, 'i', $userId);
        mysqli_stmt_execute($stmtUser);
        $resUser  = mysqli_stmt_get_result($stmtUser);
        $user     = mysqli_fetch_assoc($resUser);
        mysqli_stmt_close($stmtUser);

        if (!$user || !password_verify($oldPassword, $user['password'])) {
            $passwordError = 'Kata sandi lama tidak sesuai.';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmtPw = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtPw, 'si', $hashedPassword, $userId);
            mysqli_stmt_execute($stmtPw);
            mysqli_stmt_close($stmtPw);
        }
    }
}

// ── Redirect ────────────────────────────────────────────────
if ($passwordError !== '') {
    header('Location: dashboard-admin1.php?pengaturan=error&msg=' . urlencode($passwordError));
} elseif ($ok) {
    header('Location: dashboard-admin1.php?pengaturan=success&msg=' . urlencode('Pengaturan berhasil disimpan.'));
} else {
    header('Location: dashboard-admin1.php?pengaturan=error&msg=' . urlencode('Gagal menyimpan ke database: ' . mysqli_error($conn)));
}
exit;