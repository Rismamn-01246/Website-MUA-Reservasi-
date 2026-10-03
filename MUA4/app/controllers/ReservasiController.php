<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/ReservasiModel.php';
require_once __DIR__ . '/../models/JadwalModel.php';
require_once __DIR__ . '/../models/LayananModel.php';

// ── Wajib login untuk mengakses halaman reservasi ────────────────────
if (empty($_SESSION['user_id'])) {
    header('Location: Login.php'); // sesuaikan path halaman login kamu
    exit;
}

// Variabel untuk View
$error    = '';
// PERBAIKAN: $success sekarang dibaca dari query string setelah redirect
// (pola Post/Redirect/Get), bukan diset langsung setelah POST. Ini supaya
// refresh halaman tidak mengirim ulang form yang sama (mencegah reservasi
// duplikat seperti yang terjadi sebelumnya).
$success  = (isset($_GET['status']) && $_GET['status'] === 'success');
$fullname = $_SESSION['fullname'] ?? '';
$phone    = '';
$service  = '';
$date     = '';
$time     = '';
$address  = '';
$notes    = '';

// Data untuk form
$jadwalModel       = new JadwalModel();
$slotAktif         = $jadwalModel->getAllSlotAktif();
$tanggalDiblokir   = $jadwalModel->getTanggalDiblokir();
$slotStatusHariIni = [];

$layananModel = new LayananModel();
$layananList  = $layananModel->getAllAktif();

// ── AJAX: GET ?cek_slot=YYYY-MM-DD ───────────────────────────────────
if (isset($_GET['cek_slot'])) {
    $tgl = trim($_GET['cek_slot']);
    $d   = DateTime::createFromFormat('Y-m-d', $tgl);
    if (!$d || $d->format('Y-m-d') !== $tgl) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Format tanggal tidak valid.']);
        exit;
    }
    $diblokir = $jadwalModel->isTanggalDiblokir($tgl);
    $slots    = $diblokir ? [] : $jadwalModel->getSlotDenganStatus($tgl);
    header('Content-Type: application/json');
    echo json_encode([
        'tanggal'  => $tgl,
        'diblokir' => $diblokir,
        'slots'    => $slots,
    ]);
    exit;
}

// ── PROSES FORM POST ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Nama dikunci dari session, tidak menerima input dari form
    $fullname = $_SESSION['fullname'] ?? '';
    $phone    = trim($_POST['phone']    ?? '');
    $service  = trim($_POST['service']  ?? '');
    $date     = trim($_POST['date']     ?? '');
    $time     = trim($_POST['time']     ?? '');
    $address  = trim($_POST['address']  ?? '');
    $notes    = trim($_POST['notes']    ?? '');

    if ($fullname === '' || $phone === '' || $service === '' || $date === '' || $time === '' || $address === '') {
        $error = 'Semua bidang wajib diisi kecuali catatan tambahan.';
    } elseif ($date < date('Y-m-d')) {
        $error = 'Tanggal reservasi tidak boleh di masa lalu.';
    } elseif ($jadwalModel->isTanggalDiblokir($date)) {
        $error = 'Maaf, tanggal ' . date('d/m/Y', strtotime($date))
               . ' tidak tersedia (hari tutup). Silakan pilih tanggal lain.';
    } elseif (!$jadwalModel->isSlotTersedia($date, $time)) {
        $slotStatusHariIni = $jadwalModel->getSlotDenganStatus($date);
        $error = 'Maaf, jam ' . date('H:i', strtotime($time))
               . ' pada tanggal ' . date('d/m/Y', strtotime($date))
               . ' sudah penuh. Silakan pilih jam lain yang tersedia.';
    } else {
        $reservasiModel = new ReservasiModel();
        $userId         = $_SESSION['user_id'];

        // PERBAIKAN: ambil harga layanan dari tabel layanan berdasarkan nama
        // yang dipilih, supaya kolom price di reservasi tidak selalu 0.
        $layananData = $layananModel->getByNama($service);
        $hargaRsv    = (float) ($layananData['harga'] ?? 0);

        $created = $reservasiModel->create([
            'user_id'        => $userId,
            'fullname'       => $fullname,
            'phone'          => $phone,
            'service'        => $service,
            'reservasi_date' => $date,
            'reservasi_time' => $time,
            'address'        => $address,
            'notes'          => $notes,
            'price'          => $hargaRsv,
        ]);

        if ($created) {
            // PERBAIKAN: redirect setelah sukses, bukan lanjut render di
            // request POST yang sama. Mencegah duplikat saat user refresh
            // halaman setelah submit.
            header('Location: reservasi.php?status=success');
            exit;
        } else {
            $error = 'Gagal menyimpan reservasi. Silakan coba lagi.';
        }
    }
}