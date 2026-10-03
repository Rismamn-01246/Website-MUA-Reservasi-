<?php
session_start();

require_once __DIR__ . '/../app/config/Database.php';

if (!isset($conn)) {
    $db_obj = new Database();
    $conn   = $db_obj->getConnection();
}

function dbFetch($conn, $sql) {
    if (!$conn) return [];
    $result = mysqli_query($conn, $sql);
    if (!$result) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_free_result($result);
    return $rows;
}

// Layanan aktif
$daftarLayanan = dbFetch($conn,
    "SELECT id, nama, harga, deskripsi, yang_termasuk
     FROM layanan WHERE status = 'aktif' ORDER BY harga ASC"
);

// Portofolio - cek kolom status dulu
$cekStatus = mysqli_query($conn, "SHOW COLUMNS FROM portofolio LIKE 'status'");
$adaStatus = $cekStatus && mysqli_num_rows($cekStatus) > 0;
$daftarPortofolio = $adaStatus
    ? dbFetch($conn, "SELECT id, judul, kategori, foto, tanggal FROM portofolio WHERE status = 'tampil' OR status IS NULL OR status = '' ORDER BY id DESC")
    : dbFetch($conn, "SELECT id, judul, kategori, foto, tanggal FROM portofolio ORDER BY id DESC");

// Bersihkan path foto jika sudah ada prefix
foreach ($daftarPortofolio as &$p) {
    $p['foto'] = ltrim(str_replace('uploads/portofolio/', '', $p['foto']), '/\\');
}
unset($p);

// Testimoni - cek kolom status dulu
$cekStatusT = mysqli_query($conn, "SHOW COLUMNS FROM testimoni LIKE 'status'");
$adaStatusT = $cekStatusT && mysqli_num_rows($cekStatusT) > 0;
$daftarTestimoni = $adaStatusT
    ? dbFetch($conn, "SELECT id, nama, layanan, rating, ulasan FROM testimoni WHERE status = 'tampil' ORDER BY rating DESC, id DESC")
    : dbFetch($conn, "SELECT id, nama, layanan, rating, ulasan FROM testimoni ORDER BY rating DESC, id DESC");

// Pengaturan bisnis
$pengaturanRaw = dbFetch($conn, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan    = $pengaturanRaw[0] ?? [];

$namaBisnis = htmlspecialchars($pengaturan['nama_bisnis'] ?? 'Aza MUA');
$whatsapp   = htmlspecialchars($pengaturan['whatsapp']    ?? '081234567890');
$instagram  = htmlspecialchars($pengaturan['instagram']   ?? '@aza.mua');
$lokasi     = htmlspecialchars($pengaturan['lokasi']      ?? 'Surabaya, Jawa Timur');
$bio        = $pengaturan['bio'] ?? '';

$waClean = preg_replace('/\D/', '', $whatsapp);
if (str_starts_with($waClean, '0')) {
    $waClean = '62' . substr($waClean, 1);
}

// Kumpulkan kategori unik untuk filter (case-insensitive deduplicate)
$kategoris    = ['Semua'];
$katLowerSeen = [];
foreach ($daftarPortofolio as $p) {
    $kat      = trim($p['kategori']);
    $katLower = strtolower($kat);
    if ($kat && !in_array($katLower, $katLowerSeen)) {
        $katLowerSeen[] = $katLower;
        $kategoris[]    = $kat;
    }
}

// Icon otomatis berdasarkan nama layanan
function getIcon($nama) {
    $map = [
        'wisuda'     => 'fa-graduation-cap',
        'wedding'    => 'fa-rings-wedding',
        'party'      => 'fa-champagne-glasses',
        'engagement' => 'fa-heart',
        'photoshoot' => 'fa-camera',
    ];
    $lower = strtolower($nama);
    foreach ($map as $key => $icon) {
        if (str_contains($lower, $key)) return $icon;
    }
    return 'fa-star';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $namaBisnis ?> - Makeup Artist Professional</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/LandingPageMua.css" />
</head>

<body>

<!-- NAVBAR -->
<nav id="navbar">
  <a href="#" class="nav-logo"><?= $namaBisnis ?></a>
  <ul class="nav-links">
    <li><a href="#about">About</a></li>
    <li><a href="#services">Layanan</a></li>
    <li><a href="#portfolio">Portfolio</a></li>
    <li><a href="#testimonials">Testimoni</a></li>
    <li><a href="#contact">Kontak</a></li>
    <li><a href="Register.php" class="nav-cta">Register</a></li>
    <li><a href="Login.php" class="nav-cta">Login</a></li>
  </ul>
  <div class="hamburger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </div>
</nav>

<!-- HERO -->
<section id="hero">
  <div class="hero-left">
    <div class="hero-badge">Professional Makeup Artist</div>
    <h1 class="hero-title">
      Tampil <em>Cantik</em><br />di Momen<br />Terbaik Anda
    </h1>
    <p class="hero-desc">
      Layanan makeup profesional untuk wisuda, pernikahan, pesta, dan
      photoshoot. Reservasi mudah, jadwal fleksibel, hasil memukau.
    </p>
    <div class="hero-actions">
      <a href="Register.php" class="btn-primary">
        <i class="fa-regular fa-calendar-check"></i> Reservasi Sekarang
      </a>
      <a href="#portfolio" class="btn-outline">
        <i class="fa-regular fa-images"></i> Lihat Portfolio
      </a>
    </div>
    <div class="hero-stats">
      <div>
        <div class="stat-num">200+</div>
        <div class="stat-label">Pelanggan Puas</div>
      </div>
      <div>
        <div class="stat-num">4.9</div>
        <div class="stat-label">Rating Google</div>
      </div>
      <div>
        <div class="stat-num">3 Thn</div>
        <div class="stat-label">Pengalaman</div>
      </div>
    </div>
  </div>
  <div class="hero-right">
    <div class="hero-img-bg"></div>
    <div class="hero-shape"></div>
    <div class="hero-img-placeholder">
      <img src="https://i.pinimg.com/736x/67/4e/ca/674eca2a4f492a3bc230e1cf1fef1b8f.jpg" alt="MUA" />
    </div>
    <div class="hero-badge-float">
      <div class="badge-icon"><i class="fa-solid fa-star"></i></div>
      <div>
        <div class="badge-text">Makeup Artist Terpercaya</div>
        <div class="badge-sub"><?= $lokasi ?></div>
      </div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about" class="reveal">
  <div class="about-img">
    <img src="https://i.pinimg.com/1200x/d5/3a/34/d53a345e11a11dfcb90c6b7d67fa1fa2.jpg" alt="<?= $namaBisnis ?>" />
    <div class="exp-badge">3+<span>Tahun Exp</span></div>
  </div>
  <div class="about-content">
    <div class="section-tag">Tentang Saya</div>
    <h2 class="section-title">Makeup Artist dengan<br /><em>Passion &amp; Dedikasi</em></h2>
    <?php if ($bio): ?>
      <p class="section-desc"><?= nl2br(htmlspecialchars($bio)) ?></p>
    <?php else: ?>
      <p class="section-desc">
        Halo! Saya Aza, makeup artist profesional asal Surabaya yang siap
        membantu Anda tampil cantik dan percaya diri di setiap momen spesial.
      </p>
      <p class="section-desc" style="margin-top:12px">
        Dengan pengalaman lebih dari 3 tahun, saya telah menangani ratusan
        klien untuk berbagai acara dari wisuda sederhana hingga pernikahan mewah.
      </p>
    <?php endif; ?>
    <div class="about-features">
      <div class="feature-item"><i class="fa-solid fa-check-circle"></i> Menggunakan produk makeup berkualitas premium</div>
      <div class="feature-item"><i class="fa-solid fa-check-circle"></i> Berpengalaman untuk semua jenis kulit</div>
      <div class="feature-item"><i class="fa-solid fa-check-circle"></i> On-time dan profesional</div>
      <div class="feature-item"><i class="fa-solid fa-check-circle"></i> Tersedia untuk area <?= $lokasi ?></div>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section id="services">
  <div class="services-header reveal">
    <div class="section-tag">Layanan</div>
    <h2 class="section-title">Paket <em>Makeup</em> Kami</h2>
    <p class="section-desc">Pilih paket yang sesuai kebutuhan Anda. Semua paket termasuk konsultasi gratis.</p>
  </div>
  <div class="services-grid">
    <?php if (empty($daftarLayanan)): ?>
      <div class="service-card reveal">
        <div class="service-icon"><i class="fa-solid fa-graduation-cap"></i></div>
        <div class="service-name">Makeup Wisuda</div>
        <p class="service-desc">Tampil memukau di hari kelulusan Anda. Makeup tahan lama untuk sesi foto dan acara.</p>
        <div class="service-price">Rp 250.000 <span>/ sesi</span></div>
      </div>
      <div class="service-card reveal">
        <div class="service-icon"><i class="fa-solid fa-rings-wedding"></i></div>
        <div class="service-name">Makeup Wedding</div>
        <p class="service-desc">Tampil sempurna di hari terbaik Anda. Termasuk riasan pengantin dan touch-up.</p>
        <div class="service-price">Rp 800.000 <span>/ sesi</span></div>
      </div>
      <div class="service-card reveal">
        <div class="service-icon"><i class="fa-solid fa-champagne-glasses"></i></div>
        <div class="service-name">Makeup Party</div>
        <p class="service-desc">Jadilah pusat perhatian di pesta dengan makeup glamor dan tahan lama.</p>
        <div class="service-price">Rp 300.000 <span>/ sesi</span></div>
      </div>
      <div class="service-card reveal">
        <div class="service-icon"><i class="fa-solid fa-heart"></i></div>
        <div class="service-name">Makeup Engagement</div>
        <p class="service-desc">Tampil cantik saat momen lamaran yang tak terlupakan bersama pasangan.</p>
        <div class="service-price">Rp 400.000 <span>/ sesi</span></div>
      </div>
      <div class="service-card reveal">
        <div class="service-icon"><i class="fa-solid fa-camera"></i></div>
        <div class="service-name">Makeup Photoshoot</div>
        <p class="service-desc">Makeup khusus foto dengan teknik contouring untuk hasil foto yang sempurna.</p>
        <div class="service-price">Rp 350.000 <span>/ sesi</span></div>
      </div>
    <?php else: ?>
      <?php foreach ($daftarLayanan as $lyr): ?>
        <div class="service-card reveal">
          <div class="service-icon">
            <i class="fa-solid <?= getIcon($lyr['nama']) ?>"></i>
          </div>
          <div class="service-name"><?= htmlspecialchars($lyr['nama']) ?></div>
          <p class="service-desc"><?= htmlspecialchars($lyr['deskripsi'] ?? '') ?></p>
          <div class="service-price">
            Rp <?= number_format((int)$lyr['harga'], 0, ',', '.') ?> <span>/ sesi</span>
          </div>
          <?php if (!empty($lyr['yang_termasuk'])): ?>
            <ul style="list-style:none;padding:0;margin-top:10px;font-size:12px;color:#888;">
              <?php foreach (array_filter(explode("\n", $lyr['yang_termasuk'])) as $inc): ?>
                <li style="padding:2px 0;">
                  <i class="fa-solid fa-check" style="color:var(--rose);margin-right:5px;font-size:10px;"></i>
                  <?= htmlspecialchars(trim($inc)) ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- PORTFOLIO -->
<section id="portfolio">
  <div class="portfolio-header">
    <div>
      <div class="section-tag">Portfolio</div>
      <h2 class="section-title">Karya <em>Terbaik</em> Kami</h2>
      <p class="section-desc">Setiap karya mencerminkan dedikasi kami dalam menghadirkan keindahan terbaik.</p>
    </div>
    <div class="portfolio-filters reveal">
      <?php
        foreach ($kategoris as $kat):
          $filterVal = ($kat === 'Semua') ? 'all' : strtolower(trim($kat));
          $isActive  = ($kat === 'Semua') ? 'active' : '';
      ?>
        <button
          class="filter-btn <?= $isActive ?>"
          onclick="filterPortfolio('<?= $filterVal ?>', this)"
        ><?= htmlspecialchars($kat) ?></button>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="portfolio-grid reveal">
    <?php if (empty($daftarPortofolio)): ?>
      <div class="portfolio-item" data-cat="wedding">
        <img src="https://i.pinimg.com/736x/70/02/03/70020339eb950e8791f0e425cd4d9ea8.jpg" alt="Wedding Makeup" />
        <div class="portfolio-overlay"><h4>Ayu</h4><p>Wedding Makeup</p></div>
      </div>
      <div class="portfolio-item" data-cat="wisuda">
        <img src="https://i.pinimg.com/736x/1f/82/ba/1f82bae61b35c836c9ee7b82008f29e1.jpg" alt="Wisuda Makeup" />
        <div class="portfolio-overlay"><h4>Rea R.</h4><p>Makeup Wisuda</p></div>
      </div>
      <div class="portfolio-item" data-cat="party">
        <img src="https://i.pinimg.com/736x/f2/38/b1/f238b15ff47ebcc25c78cacf9f164fc6.jpg" alt="Party Makeup" />
        <div class="portfolio-overlay"><h4>Dewi K.</h4><p>Party Makeup</p></div>
      </div>
      <div class="portfolio-item" data-cat="engagement">
        <img src="https://i.pinimg.com/1200x/ed/89/02/ed8902737bca3b13152de4400507dddb.jpg" alt="Engagement Makeup" />
        <div class="portfolio-overlay"><h4>Nadia S.</h4><p>Makeup Engagement</p></div>
      </div>
      <div class="portfolio-item" data-cat="photoshoot">
        <img src="https://i.pinimg.com/736x/92/18/ff/9218ff8ba96f1a7fc55cb6922c231591.jpg" alt="Photoshoot Makeup" />
        <div class="portfolio-overlay"><h4>Zea</h4><p>Photoshoot Makeup</p></div>
      </div>
    <?php else: ?>
      <?php foreach ($daftarPortofolio as $item):
          $katLower = strtolower(trim($item['kategori']));
      ?>
        <div class="portfolio-item" data-cat="<?= htmlspecialchars($katLower) ?>">
          <img
            src="uploads/portofolio/<?= htmlspecialchars($item['foto']) ?>"
            alt="<?= htmlspecialchars($item['judul']) ?>"
            loading="lazy"
          />
          <div class="portfolio-overlay">
            <h4><?= htmlspecialchars($item['judul']) ?></h4>
            <p><?= htmlspecialchars($item['kategori']) ?> Makeup</p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- TESTIMONIALS -->
<section id="testimonials">
  <div class="testi-header reveal">
    <div class="section-tag">Testimoni</div>
    <h2 class="section-title">Kata Mereka yang<br /><em>Sudah Merasakan</em></h2>
    <p class="section-desc">Kepercayaan pelanggan adalah motivasi terbesar kami untuk terus berkembang.</p>
  </div>
  <div class="testi-grid">
    <?php if (empty($daftarTestimoni)): ?>
      <div class="testi-card reveal">
        <div class="testi-quote">"</div>
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">Hasilnya luar biasa! Makeup-nya tahan dari pagi sampai malam dan hasilnya natural banget.</p>
        <div class="testi-author">
          <div class="testi-avatar">AN</div>
          <div>
            <div class="testi-name">Anisa Nur</div>
            <div class="testi-event">Makeup Wisuda</div>
          </div>
        </div>
      </div>
      <div class="testi-card reveal">
        <div class="testi-quote">"</div>
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">Kak Aza sabar banget njelasinnya, hasil makeupnya sesuai sama yang aku mau.</p>
        <div class="testi-author">
          <div class="testi-avatar">DS</div>
          <div>
            <div class="testi-name">Dina Safira</div>
            <div class="testi-event">Makeup Engagement</div>
          </div>
        </div>
      </div>
      <div class="testi-card reveal">
        <div class="testi-quote">"</div>
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">Buat pernikahan saya, hasilnya benar-benar melebihi ekspektasi. Makasih kak Aza!</p>
        <div class="testi-author">
          <div class="testi-avatar">RP</div>
          <div>
            <div class="testi-name">Rini Pratiwi</div>
            <div class="testi-event">Wedding Makeup</div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($daftarTestimoni as $t): ?>
        <?php
          $words   = explode(' ', trim($t['nama']));
          $inisial = substr(implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), $words)), 0, 2);
          $rating  = max(1, min(5, (int)$t['rating']));
          $bintang = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
        ?>
        <div class="testi-card reveal">
          <div class="testi-quote">"</div>
          <div class="testi-stars"><?= $bintang ?></div>
          <p class="testi-text"><?= htmlspecialchars($t['ulasan']) ?></p>
          <div class="testi-author">
            <div class="testi-avatar"><?= $inisial ?></div>
            <div>
              <div class="testi-name"><?= htmlspecialchars($t['nama']) ?></div>
              <div class="testi-event"><?= htmlspecialchars($t['layanan']) ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- CONTACT -->
<section id="contact">
  <div class="contact-wrapper">
    <div class="contact-info reveal">
      <div class="section-tag">Kontak</div>
      <h2 class="section-title">Yuk <em>Terhubung</em> dengan Kami</h2>
      <p class="section-desc">Siap tampil cantik di hari spesialmu? Hubungi kami sekarang atau langsung lakukan reservasi.</p>
      <div class="contact-cards">
        <a href="https://wa.me/<?= $waClean ?>" target="_blank" class="contact-card" style="text-decoration:none;color:inherit;">
          <i class="fa-brands fa-whatsapp"></i>
          <h4>WhatsApp</h4>
          <p><?= $whatsapp ?></p>
        </a>
        <a href="https://instagram.com/<?= ltrim($instagram, '@') ?>" target="_blank" class="contact-card" style="text-decoration:none;color:inherit;">
          <i class="fa-brands fa-instagram"></i>
          <h4>Instagram</h4>
          <p><?= $instagram ?></p>
        </a>
        <div class="contact-card">
          <i class="fa-solid fa-location-dot"></i>
          <h4>Lokasi</h4>
          <p><?= $lokasi ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-top">
    <div class="footer-logo"><?= $namaBisnis ?></div>
    <div class="footer-socials">
      <a href="https://instagram.com/<?= ltrim($instagram, '@') ?>" target="_blank" class="social-link">
        <i class="fa-brands fa-instagram"></i>
      </a>
      <a href="https://wa.me/<?= $waClean ?>" target="_blank" class="social-link">
        <i class="fa-brands fa-whatsapp"></i>
      </a>
      <a href="#" class="social-link"><i class="fa-brands fa-tiktok"></i></a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> <?= $namaBisnis ?>. All rights reserved.</span>
    <span>Made with <i class="fa-solid fa-heart" style="color:var(--rose)"></i> in Surabaya</span>
  </div>
</footer>

<script>
  window.addEventListener('scroll', function() {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 40);
  });

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(e, i) {
      if (e.isIntersecting) {
        setTimeout(function() { e.target.classList.add('visible'); }, i * 80);
      }
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(function(el) { observer.observe(el); });

  function filterPortfolio(cat, btn) {
    document.querySelectorAll('.filter-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.portfolio-item').forEach(function(item) {
      var itemCat   = (item.dataset.cat || '').trim().toLowerCase();
      var filterCat = cat.trim().toLowerCase();
      var match     = filterCat === 'all' || itemCat === filterCat;
      item.style.display = match ? '' : 'none';
      item.style.opacity = match ? '1' : '0';
    });
  }

  function toggleMenu() {
    var links  = document.querySelector('.nav-links');
    var isOpen = links.style.display === 'flex';
    if (isOpen) {
      links.style.display = '';
    } else {
      links.style.cssText = 'display:flex;flex-direction:column;position:absolute;top:70px;left:0;right:0;background:var(--white);padding:20px 24px;box-shadow:0 8px 24px rgba(0,0,0,0.08);';
    }
  }
</script>

</body>
</html>