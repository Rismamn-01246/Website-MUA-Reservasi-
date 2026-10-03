        <!-- ════════════════════ VIEW: KATALOG LAYANAN ════════════════════ -->
        <div class="view" id="view-katalog">

          <!-- Hero -->
          <div class="katalog-hero">
            <div class="katalog-hero-text">
              <h2>Katalog Layanan &amp; Harga</h2>
              <p>Temukan layanan makeup yang paling cocok untuk momen spesialmu. Semua harga sudah termasuk riasan penuh dan produk premium.</p>
            </div>
            <div class="katalog-hero-badge">✦ <?= count($layananKatalog) ?> Layanan Tersedia</div>
          </div>

          <!-- Filter — label disesuaikan dengan nilai 'cat' di $layananKatalog -->
          <div class="katalog-filter">
            <button class="kf-btn active" onclick="filterKatalog(this,'semua')">Semua</button>
            <button class="kf-btn" onclick="filterKatalog(this,'wedding')">Pernikahan</button>
            <button class="kf-btn" onclick="filterKatalog(this,'engagement')">Engagement</button>
            <button class="kf-btn" onclick="filterKatalog(this,'wisuda')">Wisuda</button>
            <button class="kf-btn" onclick="filterKatalog(this,'party')">Pesta &amp; Event</button>
            <button class="kf-btn" onclick="filterKatalog(this,'photoshoot')">Photoshoot</button>
          </div>

          <!-- Grid kartu layanan -->
          <div class="katalog-grid" id="katalogGrid">
            <?php foreach ($layananKatalog as $lay): ?>
              <div class="k-card" data-cat="<?= htmlspecialchars($lay['cat']) ?>">
                <div class="k-thumb <?= $lay['foto'] ? 'has-photo' : htmlspecialchars($lay['warna']) ?>"
                     <?php if ($lay['foto']): ?>
                       style="background-image:url('<?= htmlspecialchars($lay['foto']) ?>')"
                     <?php endif; ?>>
                  <?php if ($lay['foto']): ?>
                    <span class="k-emoji-chip"><?= $lay['emoji'] ?></span>
                  <?php else: ?>
                    <?= $lay['emoji'] ?>
                  <?php endif; ?>
                  <?php if ($lay['badge']): ?>
                    <span class="k-pop"><?= htmlspecialchars($lay['badge']) ?></span>
                  <?php endif; ?>
                </div>
                <div class="k-body">
                  <div class="k-cat"><?= htmlspecialchars(ucfirst($lay['cat'])) ?></div>
                  <div class="k-name"><?= htmlspecialchars($lay['nama']) ?></div>
                  <div class="k-desc"><?= htmlspecialchars($lay['desc']) ?></div>
                  <ul class="k-includes">
                    <?php foreach ($lay['includes'] as $inc): ?>
                      <li><?= htmlspecialchars($inc) ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <div class="k-footer">
                  <div>
                    <div class="k-price-label">Mulai dari</div>
                    <div class="k-price-val"><?= htmlspecialchars($lay['harga']) ?></div>
                  </div>
                  <button class="k-book-btn" onclick="pilihLayanan('<?= htmlspecialchars($lay['nama']) ?>')">Pesan Sekarang</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div><!-- /katalogGrid -->

          <!-- Catatan -->
          <div class="katalog-note">
            <div class="katalog-note-icon">💬</div>
            <div>
              <strong style="color:#2c1a14;">Butuh paket khusus atau harga grup?</strong><br>
              Untuk paket bridesmaids, grup, atau lokasi di luar kota, silakan
              <a href="https://wa.me/6281234567890" target="_blank">hubungi kami via WhatsApp</a>
              untuk mendapatkan penawaran terbaik. Tim Aza MUA siap membantu 💖
            </div>
          </div>

        </div>
        <!-- /KATALOG -->
