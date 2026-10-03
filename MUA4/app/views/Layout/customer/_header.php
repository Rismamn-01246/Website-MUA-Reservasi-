<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user'])) {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/../../../models/UserModel.php';
require_once __DIR__ . '/../../../models/ReservasiModel.php';
require_once __DIR__ . '/../../../models/JadwalModel.php';
require_once __DIR__ . '/../../../models/PortofolioModel.php';
require_once __DIR__ . '/../../../models/TestimoniModel.php';
require_once __DIR__ . '/../../../models/LayananModel.php';

// ── HANDLER: Update Profil ─────────────────────────────────────
$profilSuccess = false;
$profilError   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    $newFullname = trim($_POST['fullname'] ?? '');
    $newEmail    = trim($_POST['email']    ?? '');
    $newPhone    = trim($_POST['phone']    ?? '');
    $newAddress  = trim($_POST['address']  ?? '');

    $userModel = new UserModel();
    try {
        $ok = $userModel->updateProfile(
            (int) $_SESSION['user_id'],
            $newFullname, $newEmail, $newPhone, $newAddress
        );
        if ($ok) {
            $_SESSION['fullname'] = $newFullname;
            $_SESSION['email']    = $newEmail;
            $_SESSION['phone']    = $newPhone;
            $_SESSION['address']  = $newAddress;
            $profilSuccess = true;
        }
    } catch (\Exception $e) {
        $profilError = $e->getMessage();
    }
}

// ── HANDLER: Buat Reservasi ────────────────────────────────────
$reservasiSuccess = false;
$reservasiError   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_reservasi') {
    $service  = trim($_POST['service']        ?? '');
    $date     = trim($_POST['reservasi_date'] ?? '');
    $time     = trim($_POST['reservasi_time'] ?? '');
    $address  = trim($_POST['address']        ?? '');
    $notes    = trim($_POST['notes']          ?? '');
    $phone    = $_SESSION['phone']    ?? '';
    $fullname = $_SESSION['fullname'] ?? '';

    if (strlen($time) === 5) $time = $time . ':00';

    $jadwalModel    = new JadwalModel();
    $reservasiModel = new ReservasiModel();

    if ($service === '' || $date === '' || $time === '' || $address === '') {
        $reservasiError = 'Semua bidang wajib diisi kecuali catatan tambahan.';
    } elseif ($date < date('Y-m-d')) {
        $reservasiError = 'Tanggal reservasi tidak boleh di masa lalu.';
    } elseif ($jadwalModel->isTanggalDiblokir($date)) {
        $reservasiError = 'Maaf, tanggal tersebut tidak tersedia (hari tutup). Silakan pilih tanggal lain.';
    } elseif (!$jadwalModel->isSlotTersedia($date, $time)) {
        $reservasiError = 'Maaf, jam tersebut sudah penuh. Silakan pilih jam lain.';
    } else {
        // Ambil harga dari DB — pakai ID jika tersedia (lebih andal), fallback ke nama
        $layananModelRsv = new LayananModel();
        $layananIdPost   = (int)($_POST['layanan_id'] ?? 0);
        if ($layananIdPost > 0) {
            $layananData = $layananModelRsv->getById($layananIdPost);
        } else {
            $layananData = $layananModelRsv->getByNama($service);
        }
        $hargaRsv = (float)($layananData['harga'] ?? 0);

        $created = $reservasiModel->create([
            'user_id'        => (int) $_SESSION['user_id'],
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
            $reservasiSuccess = true;
        } else {
            $reservasiError = 'Gagal menyimpan reservasi. Silakan coba lagi.';
        }
    }
}

// ── HANDLER: Kirim Testimoni ───────────────────────────────────
$testimoniSuccess = false;
$testimoniError   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'kirim_testimoni') {
    $reservasiId = (int) ($_POST['reservasi_id'] ?? 0);
    $rating      = (int) ($_POST['rating']       ?? 0);
    $komentar    = trim($_POST['komentar']        ?? '');

    if ($reservasiId <= 0 || $rating < 1 || $rating > 5) {
        $testimoniError = 'Mohon pilih rating bintang terlebih dahulu.';
    } elseif (strlen($komentar) < 5) {
        $testimoniError = 'Komentar minimal 5 karakter.';
    } else {
        try {
            $testimoniModel = new TestimoniModel();
            $reservasiInfo  = null;
            $layananNama    = '';
            // Ambil nama layanan dari reservasi untuk disimpan ke testimoni
            if ($reservasiId > 0 && !empty($_SESSION['user_id'])) {
                $reservasiModel2 = new ReservasiModel();
                $semuaRsv = $reservasiModel2->getByUserId((int)$_SESSION['user_id']);
                foreach ($semuaRsv as $rsv) {
                    if ((int)$rsv['id'] === $reservasiId) {
                        $layananNama   = $rsv['service'] ?? '';
                        $reservasiInfo = $rsv;
                        break;
                    }
                }
            }
            $ok = $testimoniModel->create(
                (int) $_SESSION['user_id'],
                $_SESSION['fullname'] ?? 'Pelanggan',
                $layananNama,
                $rating,
                $komentar
            );
            if ($ok) {
                $testimoniSuccess = true;
            } else {
                $testimoniError = 'Gagal mengirim testimoni. Silakan coba lagi.';
            }
        } catch (\Exception $e) {
            $testimoniError = 'Gagal mengirim testimoni: ' . $e->getMessage();
        }
    }
}

$fullnameSafe = htmlspecialchars($_SESSION['fullname'] ?? 'Pelanggan');

// ── DATA ───────────────────────────────────────────────────────
$jadwalModel = $jadwalModel ?? new JadwalModel();
$slotAktif   = $jadwalModel->getAllSlotAktif();

$portofolioModel = new PortofolioModel();
$portoAll        = $portofolioModel->getAllTampil();
$portoPreview    = array_slice($portoAll, 0, 3);

$riwayatReservasi = [];
if (!empty($_SESSION['user_id'])) {
    $reservasiModel   = $reservasiModel ?? new ReservasiModel();
    $riwayatReservasi = $reservasiModel->getByUserId((int) $_SESSION['user_id']);
}

$PORTO_FOLDER = 'uploads/portofolio/';

// ── Ambil layanan aktif dari database ─────────────────────────
$layananModel  = new LayananModel();
$layananDariDb = $layananModel->getAllAktif();

// Peta emoji & warna per kategori (fallback jika tidak ada di DB)
$katMeta = [
    'wedding'    => ['emoji'=>'💍','warna'=>'warna-a','cat'=>'wedding'],
    'engagement' => ['emoji'=>'💕','warna'=>'warna-b','cat'=>'engagement'],
    'wisuda'     => ['emoji'=>'🎓','warna'=>'warna-c','cat'=>'wisuda'],
    'party'      => ['emoji'=>'🎉','warna'=>'warna-d','cat'=>'party'],
    'photoshoot' => ['emoji'=>'📸','warna'=>'warna-e','cat'=>'photoshoot'],
    'formal'     => ['emoji'=>'👑','warna'=>'warna-f','cat'=>'party'],
];
$warnaList = ['warna-a','warna-b','warna-c','warna-d','warna-e','warna-f'];

$layananKatalog = [];
$wi = 0;
foreach ($layananDariDb as $ldb) {
    $namaLower = strtolower($ldb['nama'] ?? '');
    // Tentukan kategori berdasarkan nama layanan
    $cat = 'party';
    if (str_contains($namaLower, 'wedding'))    $cat = 'wedding';
    elseif (str_contains($namaLower, 'engagement') || str_contains($namaLower, 'lamaran')) $cat = 'engagement';
    elseif (str_contains($namaLower, 'wisuda'))  $cat = 'wisuda';
    elseif (str_contains($namaLower, 'photoshoot') || str_contains($namaLower, 'foto'))    $cat = 'photoshoot';

    $meta  = $katMeta[$cat] ?? ['emoji'=>'✨','warna'=>$warnaList[$wi % count($warnaList)],'cat'=>$cat];

    // Harga format Rupiah
    $hargaAngka = (float)($ldb['harga'] ?? 0);
    $hargaFmt   = 'Rp ' . number_format($hargaAngka, 0, ',', '.');

    // includes dari kolom yang_termasuk (pisah per baris)
    $includes = [];
    if (!empty($ldb['yang_termasuk'])) {
        $includes = array_filter(array_map('trim', explode("
", $ldb['yang_termasuk'])));
    }
    if (empty($includes)) $includes = ['Riasan wajah penuh', 'Setting spray tahan lama'];

    $layananKatalog[] = [
        'id'       => (int)$ldb['id'],
        'cat'      => $cat,
        'nama'     => $ldb['nama'],
        'emoji'    => $meta['emoji'],
        'badge'    => null,
        'desc'     => $ldb['deskripsi'] ?? '',
        'includes' => array_values($includes),
        'harga'    => $hargaFmt,
        'harga_num'=> $hargaAngka,
        'warna'    => $meta['warna'],
    ];
    $wi++;
}

// Fallback jika DB kosong - pakai data hardcoded
if (empty($layananKatalog)) {
    $layananKatalog = [
        ['id'=>0,'cat'=>'wedding',    'nama'=>'Wedding Makeup',    'emoji'=>'💍','badge'=>'Terlaris',
         'desc'=>'Riasan pengantin elegan yang tahan lama sepanjang hari pernikahanmu.',
         'includes'=>['Konsultasi & trial makeup','Riasan wajah & leher penuh','Pemasangan bulu mata','Setting spray tahan 12 jam'],
         'harga'=>'Rp 500.000','harga_num'=>500000,'warna'=>'warna-a'],
        ['id'=>0,'cat'=>'wisuda',     'nama'=>'Wisuda Makeup',     'emoji'=>'🎓','badge'=>'Populer',
         'desc'=>'Abadikan momen kelulusan dengan riasan anggun dan fotogenik.',
         'includes'=>['Riasan wajah penuh','Pemasangan bulu mata','Setting spray tahan 8 jam'],
         'harga'=>'Rp 350.000','harga_num'=>350000,'warna'=>'warna-c'],
    ];
}

// Cocokkan foto portofolio per kategori layanan
foreach ($layananKatalog as &$lay) {
    $lay['foto'] = null;
    foreach ($portoAll as $p) {
        $katPorto = strtolower(trim($p['kategori'] ?? ''));
        if ($katPorto === $lay['cat']) {
            $lay['foto'] = $PORTO_FOLDER . $p['foto'];
            break;
        }
    }
}
unset($lay);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Pelanggan — AZA MUA</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/dashboard-customer.css" />

</head>
<body>
  <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <div class="brand">✦ Aza MUA</div>
      <div class="sub">PORTAL PELANGGAN</div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Menu Utama</div>

      <a href="#" class="nav-item active" data-view="beranda" onclick="sv('beranda'); return false;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
          <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        <span class="nav-label">Beranda</span>
      </a>

      <a href="#" class="nav-item" data-view="reservasi" onclick="sv('reservasi'); return false;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span class="nav-label">Reservasi</span>
      </a>

      <a href="#" class="nav-item" data-view="riwayat" onclick="sv('riwayat'); return false;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/>
        </svg>
        <span class="nav-label">Riwayat</span>
      </a>

      <div class="nav-section">Informasi</div>

      <a href="#" class="nav-item" data-view="katalog" onclick="sv('katalog'); return false;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"/>
        </svg>
        <span class="nav-label">Katalog Layanan</span>
      </a>

      <div class="nav-section">Akun</div>

      <a href="#" class="nav-item" data-view="profil" onclick="sv('profil'); return false;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
        </svg>
        <span class="nav-label">Profil &amp; Pengaturan</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="admin-card">
        <div class="profil-avatar" id="pfAvatar"><?= strtoupper(substr($fullnameSafe, 0, 1)) ?></div>
        <div class="admin-info">
          <div class="name" id="sbName"><?= $fullnameSafe ?></div>
          <div class="role">Pelanggan</div>
        </div>
      </div>
      <!-- Logout lama disembunyikan via CSS, tetap ada sebagai fallback -->
      <div class="sidebar-footer-bottom">
        <a href="logout.php" class="logout-link" title="Logout">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Keluar
        </a>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button class="btn-hamburger" onclick="toggleSidebar()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
        <div>
          <div class="page-title" id="pageTitle">Beranda</div>
          <div class="page-date"  id="pageDate">Selamat datang kembali, <?= $fullnameSafe ?></div>
        </div>
      </div>
      <div class="topbar-right">
        <div class="search-bar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          Cari...
        </div>
        <button class="btn-notif">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
          <span class="notif-dot"></span>
        </button>
        <!-- ── LOGOUT BUTTON — kanan atas topbar ── -->
        <a href="logout.php" class="topbar-logout" title="Keluar dari akun">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Keluar
        </a>
      </div>
    </div>

    <div class="content">
      <div class="container-inner">

        <!-- ════════════════════ VIEW: BERANDA ════════════════════ -->
