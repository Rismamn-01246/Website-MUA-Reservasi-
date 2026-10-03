<?php

require_once __DIR__ . '/../models/ReservasiModel.php';
require_once __DIR__ . '/../models/LayananModel.php';
require_once __DIR__ . '/../models/JadwalModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/TestimoniModel.php';
require_once __DIR__ . '/../models/PortofolioModel.php';
require_once __DIR__ . '/../models/PengaturanModel.php';   // ← ditambahkan

// ── 1. Cek session (session sudah di-start di public/dashboard-admin1.php) ──
if (empty($_SESSION['user'])) {
    header('Location: Login.php');
    exit;
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: customer-dashboard.php');
    exit;
}

// ── 2. Inisialisasi model ────────────────────────────────────────────
$jadwalModel     = new JadwalModel();
$reservasiModel  = new ReservasiModel();
$layananModel    = new LayananModel();
$testimoniModel  = new TestimoniModel();
$portofolioModel = new PortofolioModel();
$userModel       = new UserModel();
$pengaturanModel = new PengaturanModel();   // ← ditambahkan

// ── PERBAIKAN: Ambil nama admin yang login dari session/database ────
// Sebelumnya: $userName = htmlspecialchars($_SESSION['user']);
// Masalah: $_SESSION['user'] berisi username/identifier (bukan nama tampil),
// sehingga selalu tampil sama (mis. "Administrator"/"Aza MUA").
//
// Solusi: gunakan fullname dari session jika ada (disimpan saat login),
// jika tidak ada, ambil dari database berdasarkan id/username di session.

if (!empty($_SESSION['fullname'])) {
    // Kasus A: saat login sudah menyimpan nama lengkap ke session
    $userName = htmlspecialchars($_SESSION['fullname']);
} elseif (!empty($_SESSION['user_id'])) {
    // Kasus B: ambil nama dari database berdasarkan user_id
    $adminData = $userModel->findById((int) $_SESSION['user_id']);
    $userName  = htmlspecialchars($adminData['fullname'] ?? $_SESSION['user']);
} else {
    // Kasus C: cari berdasarkan username yang disimpan di $_SESSION['user']
    $adminData = $userModel->findByUsername($_SESSION['user']);
    $userName  = htmlspecialchars($adminData['fullname'] ?? $_SESSION['user']);
}

// ── 3. AJAX: GET ?ajax=slot&tanggal=YYYY-MM-DD ──────────────────────
if (isset($_GET['ajax']) && $_GET['ajax'] === 'slot') {
    $tgl = trim($_GET['tanggal'] ?? '');
    $d   = DateTime::createFromFormat('Y-m-d', $tgl);
    if (!$d || $d->format('Y-m-d') !== $tgl) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Format tanggal tidak valid.']);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode([
        'tanggal'  => $tgl,
        'diblokir' => $jadwalModel->isTanggalDiblokir($tgl),
        'slots'    => $jadwalModel->getSlotDenganStatus($tgl),
    ]);
    exit;
}

// ── 4. AJAX: POST ?ajax=blokir_tanggal ──────────────────────────────
if (isset($_GET['ajax']) && $_GET['ajax'] === 'blokir_tanggal' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl  = trim($_POST['tanggal']     ?? '');
    $aksi = trim($_POST['aksi']        ?? ''); // 'blokir' atau 'buka'
    $ket  = trim($_POST['keterangan']  ?? '');

    header('Content-Type: application/json');

    $d = DateTime::createFromFormat('Y-m-d', $tgl);
    if (!$d || $d->format('Y-m-d') !== $tgl || !in_array($aksi, ['blokir', 'buka'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
        exit;
    }

    $ok = ($aksi === 'blokir')
        ? $jadwalModel->blokTanggal($tgl, $ket)
        : $jadwalModel->bukaTanggal($tgl);

    echo json_encode(['success' => $ok, 'tanggal' => $tgl, 'aksi' => $aksi]);
    exit;
}

// ── 5. Data utama dashboard ──────────────────────────────────────────
$daftarPelanggan   = $userModel->getAllCustomers();
$daftarReservasi   = $reservasiModel->getAll();
$daftarLayanan     = $layananModel->getAll();
$daftarSlot        = $jadwalModel->getAllSlotAktif();
$tanggalDiblokir   = $jadwalModel->getTanggalDiblokir();
$daftarTestimoni   = $testimoniModel->getAll();
$daftarPortofolio  = $portofolioModel->getAll();
$pengaturan        = $pengaturanModel->getData();   // ← ditambahkan

// ── 6. Hitung statistik reservasi ───────────────────────────────────
$totalReservasi  = count($daftarReservasi);
$jumlahPending   = 0;
$jumlahConfirmed = 0;
$jumlahDone      = 0;
$jumlahCancelled = 0;

foreach ($daftarReservasi as $r) {
    switch ($r['status'] ?? '') {
        case 'pending':   $jumlahPending++;   break;
        case 'confirmed': $jumlahConfirmed++; break;
        case 'done':      $jumlahDone++;      break;
        case 'cancelled': $jumlahCancelled++; break;
    }
}

// Alias untuk filter-bar di view
$countSemua     = $totalReservasi;
$countConfirmed = $jumlahConfirmed;
$countPending   = $jumlahPending;
$countDone      = $jumlahDone;
$countCancelled = $jumlahCancelled;

// ── 7. Hitung statistik testimoni ───────────────────────────────────
$totalTestimoni   = count($daftarTestimoni);
$totalTampil      = 0;
$totalSembunyikan = 0;
$totalBintang5    = 0;
$totalBintang4    = 0;

foreach ($daftarTestimoni as $t) {
    if ($t['status'] === 'tampil')      $totalTampil++;
    if ($t['status'] === 'sembunyikan') $totalSembunyikan++;
    if ($t['rating'] == 5)              $totalBintang5++;
    if ($t['rating'] == 4)              $totalBintang4++;
}

// ── 8. Statistik pelanggan ───────────────────────────────────────────
$totalPelanggan = count($daftarPelanggan);


$reservasiJson = json_encode(array_map(fn($r) => [
    'id'      => (int) $r['id'],
    'nama'    => $r['fullname'],
    'layanan' => $r['service'],
    'tanggal' => date('Y-m-d', strtotime($r['reservasi_date'])),
    'tanggal_display' => date('d M Y', strtotime($r['reservasi_date'])),
    'waktu'   => $r['reservasi_time'],
    'lokasi'  => $r['address'],
    'harga'   => 'Rp ' . number_format($r['price'] ?? 0, 0, ',', '.'),
    'status'  => $r['status'],
    'catatan' => $r['notes'] ?? '',
], $daftarReservasi), JSON_UNESCAPED_UNICODE);