<?php

if (!isset($userName, $daftarReservasi, $pengaturan)) {
    die('Akses langsung tidak diizinkan. Buka lewat dashboard-admin1.php');
}

require __DIR__ . '/../Layout/admin/_header.php';

require __DIR__ . '/dashboard.php';
require __DIR__ . '/reservasi.php';
require __DIR__ . '/pelanggan.php';
require __DIR__ . '/portofolio.php';
require __DIR__ . '/layanan.php';
require __DIR__ . '/testimoni.php';
require __DIR__ . '/pengaturan.php';

require __DIR__ . '/../Layout/admin/_footer.php';
