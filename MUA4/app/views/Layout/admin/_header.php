<?php

$badgeMap = [
    'pending'   => 'warning',
    'confirmed' => 'success',
    'done'      => 'info',
    'cancelled' => 'danger',
];
$labelMap = [
    'pending'   => 'Menunggu',
    'confirmed' => 'Dikonfirmasi',
    'done'      => 'Selesai',
    'cancelled' => 'Dibatalkan',
];

$reservasiForJs = array_map(function ($r) use ($labelMap) {
    return [
        'id'              => (int) $r['id'],
        'nama'            => $r['fullname'],
        'layanan'         => $r['service'],
        'tanggal'         => date('Y-m-d', strtotime($r['reservasi_date'])),
        'tanggal_display' => date('d M Y', strtotime($r['reservasi_date'])),
        'waktu'           => $r['reservasi_time'],
        'lokasi'          => $r['address'],
        'harga'           => 'Rp ' . number_format($r['price'] ?? 0, 0, ',', '.'),
        'status'          => $r['status'],
        'catatan'         => $r['notes'] ?? '',
    ];
}, $daftarReservasi);
$reservasiJson = json_encode($reservasiForJs, JSON_UNESCAPED_UNICODE);

$layananForJs = array_map(fn($l) => [
    'id'            => (int)$l['id'],
    'nama'          => $l['nama'],
    'harga'         => (int)$l['harga'],
    'deskripsi'     => $l['deskripsi'] ?? '',
    'yang_termasuk' => $l['yang_termasuk'] ?? '',
    'status'        => $l['status'],
], $daftarLayanan);
$layananJson = json_encode($layananForJs, JSON_UNESCAPED_UNICODE);

$pelangganForJs = array_map(function ($p) {
    return [
        'id'      => (int)$p['id'],
        'nama'    => $p['fullname'] ?? $p['name'] ?? '',
        'phone'   => $p['phone']   ?? '',
        'email'   => $p['email']   ?? '',
        'address' => $p['address'] ?? '',
    ];
}, $daftarPelanggan);
$pelangganJson = json_encode($pelangganForJs, JSON_UNESCAPED_UNICODE);

$portofolioForJs = array_map(function ($item) {
    return [
        'id'       => (int)$item['id'],
        'judul'    => $item['judul'],
        'kategori' => $item['kategori'],
        'tanggal'  => !empty($item['tanggal']) ? date('Y-m-d', strtotime($item['tanggal'])) : '',
        'foto'     => $item['foto'],
    ];
}, $daftarPortofolio);
$portofolioJson = json_encode($portofolioForJs, JSON_UNESCAPED_UNICODE);

// ── Base path untuk form action (otomatis deteksi subfolder) ──
$scriptDir  = dirname($_SERVER['SCRIPT_NAME']); // misal: /MUA/public
$basePath   = rtrim($scriptDir, '/');           // hapus trailing slash

function buildNotif(string $key): string {
    $s   = $_GET[$key] ?? '';
    $msg = htmlspecialchars(urldecode($_GET['msg'] ?? ''));
    if ($s === 'success') return '<div class="notif-msg success">✓ ' . ($msg ?: 'Berhasil.') . '</div>';
    if ($s === 'error')   return '<div class="notif-msg error">✗ '   . ($msg ?: 'Terjadi kesalahan.') . '</div>';
    return '';
}
$pengaturanMsg  = buildNotif('pengaturan');
$layananMsg     = buildNotif('layanan');
$portofolioMsg  = buildNotif('portofolio');
$testimoniMsg   = buildNotif('testimoni');
$reservasiMsg   = buildNotif('reservasi');
$pelangganMsg   = buildNotif('pelanggan');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard — Aza MUA</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/dashboard-admin.css" />
</head>
<body>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<nav class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="brand">✦ Aza MUA</div>
    <div class="sub">Admin Panel</div>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section">Menu Utama</div>
    <a class="nav-item active" href="#" onclick="navigate('dashboard', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
      </svg>
      <span class="nav-label">Dashboard</span>
    </a>
    <a class="nav-item" href="#" id="nav-reservasi" onclick="navigate('reservasi', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      <span class="nav-label">Reservasi</span>
      <?php if ($jumlahPending > 0): ?>
        <span class="nav-badge" id="pending-badge"><?= $jumlahPending ?></span>
      <?php else: ?>
        <span class="nav-badge" id="pending-badge" style="display:none">0</span>
      <?php endif; ?>
    </a>
    <a class="nav-item" href="#" id="nav-pelanggan" onclick="navigate('pelanggan', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      <span class="nav-label">Pelanggan</span>
    </a>
    <div class="nav-section">Konten</div>
    <a class="nav-item" href="#" id="nav-portofolio" onclick="navigate('portofolio', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <circle cx="8.5" cy="8.5" r="1.5"/>
        <polyline points="21 15 16 10 5 21"/>
      </svg>
      <span class="nav-label">Portofolio</span>
    </a>
    <a class="nav-item" href="#" id="nav-layanan" onclick="navigate('layanan', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10 9 9 9 8 9"/>
      </svg>
      <span class="nav-label">Layanan & Harga</span>
    </a>
    <a class="nav-item" href="#" id="nav-testimoni" onclick="navigate('testimoni', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <span class="nav-label">Testimoni</span>
    </a>
    <div class="nav-section">Sistem</div>
    <a class="nav-item" href="#" id="nav-pengaturan" onclick="navigate('pengaturan', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <circle cx="12" cy="12" r="3"/>
        <path d="M19.07 4.93l-1.41 1.41M22 12h-2M19.07 19.07l-1.41-1.41M12 22v-2M4.93 19.07l1.41-1.41M2 12h2M4.93 4.93l1.41 1.41"/>
      </svg>
      <span class="nav-label">Pengaturan</span>
    </a>
  </div>
  <div class="sidebar-footer">
    <div class="admin-card">
      <div class="admin-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
      <div class="admin-info">
        <div class="name"><?= htmlspecialchars($userName) ?></div>
        <div class="role">Administrator</div>
      </div>
    </div>
  </div>
</nav>

<div class="main">
  <div class="topbar">
    <div class="topbar-left">
      <button class="btn-hamburger" onclick="toggleSidebar()" aria-label="Menu">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <div>
        <div class="page-title" id="pageTitle">Dashboard</div>
        <div class="page-date" id="currentDate">Selamat datang, <?= htmlspecialchars($userName) ?></div>
      </div>
    </div>
    <div class="topbar-right">
      <div style="display:flex;align-items:center;gap:6px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="global-search" placeholder="Cari reservasi..."
          oninput="globalSearch(this.value)"
          style="border:none;outline:none;font-size:13px;background:transparent;width:140px">
      </div>
      <a class="btn-secondary" href="<?= $basePath ?>/logout.php">Logout</a>
      <button class="btn-notif" aria-label="Notifikasi"
        onclick="navigate('reservasi', document.getElementById('nav-reservasi'))">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <?php if ($jumlahPending > 0): ?>
          <span class="notif-dot" id="notif-dot"></span>
          <span class="notif-count" id="notif-count"><?= $jumlahPending ?></span>
        <?php else: ?>
          <span class="notif-dot" id="notif-dot" style="display:none"></span>
          <span class="notif-count" id="notif-count" style="display:none">0</span>
        <?php endif; ?>
      </button>
    </div>
  </div>

  <div class="content">

