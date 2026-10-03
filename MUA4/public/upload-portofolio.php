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

$judul    = trim($_POST['judul']    ?? '');
$kategori = trim($_POST['kategori'] ?? 'Wedding');
$tanggal  = trim($_POST['tanggal']  ?? '');

if (!$judul) {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Judul wajib diisi.'));
    exit;
}

if (empty($_FILES['foto']['name'])) {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Pilih foto terlebih dahulu.'));
    exit;
}

$uploadDir = __DIR__ . '/uploads/portofolio/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'webp'];
if (!in_array($ext, $allowed)) {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Format file tidak didukung (JPG/PNG/WEBP).'));
    exit;
}

if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Ukuran file melebihi 5MB.'));
    exit;
}

$namaFile = uniqid('porto_', true) . '.' . $ext;
$dest     = $uploadDir . $namaFile;

if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Gagal mengupload file.'));
    exit;
}

// Koneksi pakai mysqli
$db_obj = new Database();
$conn   = $db_obj->getConnection();

$tanggalVal = $tanggal ?: null;

$stmt = mysqli_prepare($conn, "INSERT INTO portofolio (judul, kategori, foto, tanggal, status) VALUES (?, ?, ?, ?, 'tampil')");
mysqli_stmt_bind_param($stmt, 'ssss', $judul, $kategori, $namaFile, $tanggalVal);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: dashboard-admin1.php?portofolio=success&msg=' . urlencode('Foto berhasil diupload.'));
} else {
    mysqli_stmt_close($stmt);
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Gagal menyimpan ke database: ' . mysqli_error($conn)));
}
exit;