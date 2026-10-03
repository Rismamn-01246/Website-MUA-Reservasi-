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

$id       = (int)($_POST['id'] ?? 0);
$judul    = trim($_POST['judul']    ?? '');
$kategori = trim($_POST['kategori'] ?? 'Wedding');
$tanggal  = trim($_POST['tanggal']  ?? '');

if (!$id || !$judul) {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Data tidak lengkap.'));
    exit;
}

$db_obj = new Database();
$conn   = $db_obj->getConnection();

// Ambil data lama untuk tahu nama foto saat ini
$stmtCek = mysqli_prepare($conn, "SELECT foto FROM portofolio WHERE id = ?");
mysqli_stmt_bind_param($stmtCek, 'i', $id);
mysqli_stmt_execute($stmtCek);
$result  = mysqli_stmt_get_result($stmtCek);
$existing = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmtCek);

if (!$existing) {
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Data tidak ditemukan.'));
    exit;
}

$namaFile   = $existing['foto'];
$uploadDir  = __DIR__ . '/uploads/portofolio/';
$tanggalVal = $tanggal ?: null;

// Kalau ada foto baru yang diupload
if (!empty($_FILES['foto']['name'])) {
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

    $namaFileBaru = uniqid('porto_', true) . '.' . $ext;
    $dest         = $uploadDir . $namaFileBaru;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
        header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Gagal mengupload file.'));
        exit;
    }

    // Hapus foto lama kalau berhasil upload baru
    $fotoLama = $uploadDir . $namaFile;
    if ($namaFile && file_exists($fotoLama)) {
        unlink($fotoLama);
    }

    $namaFile = $namaFileBaru;
}

// Update ke database
$stmt = mysqli_prepare($conn, "UPDATE portofolio SET judul = ?, kategori = ?, foto = ?, tanggal = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'ssssi', $judul, $kategori, $namaFile, $tanggalVal, $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: dashboard-admin1.php?portofolio=success&msg=' . urlencode('Portofolio berhasil diperbarui.'));
} else {
    mysqli_stmt_close($stmt);
    header('Location: dashboard-admin1.php?portofolio=error&msg=' . urlencode('Gagal menyimpan: ' . mysqli_error($conn)));
}
exit;