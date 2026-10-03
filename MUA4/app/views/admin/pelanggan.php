    <!-- PELANGGAN PAGE -->
    <div class="page" id="page-pelanggan">
      <div class="page-header">
        <div>
          <div class="page-heading">Pelanggan</div>
          <div class="page-sub"><?= $totalPelanggan ?> pelanggan terdaftar</div>
        </div>
        <button class="btn-primary" onclick="openModal('modal-tambah-pelanggan')">+ Tambah Pelanggan</button>
      </div>

      <?= $pelangganMsg ?>

      <div class="filter-bar">
        <button class="filter-btn active" onclick="filterPelanggan('semua', this)">Semua</button>
        <button class="filter-btn" onclick="filterPelanggan('baru', this)">Pelanggan Baru</button>
        <button class="filter-btn" onclick="filterPelanggan('repeat', this)">Repeat Order</button>
        <div class="filter-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" id="search-pelanggan" placeholder="Cari nama, nomor HP..."
            oninput="searchPelanggan(this.value)" />
        </div>
      </div>

      <div class="pelanggan-grid">
        <?php foreach ($daftarPelanggan as $p): ?>
          <?php
            $nama     = $p['fullname'] ?? $p['name'] ?? 'Pelanggan';
            $inisialP = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $nama)));
            $inisialP = substr($inisialP, 0, 2);
            $phone    = $p['phone'] ?? '';
            $phoneWa  = ltrim($phone, '0');
            $jumlahRsv = $reservasiModel->countByUserId($p['id']);
          ?>
          <div class="pelanggan-card"
               data-nama="<?= strtolower(htmlspecialchars($nama)) ?>"
               data-phone="<?= htmlspecialchars($phone) ?>"
               data-reservasi="<?= $jumlahRsv ?>">
            <div class="pelanggan-avatar"><?= $inisialP ?></div>
            <div class="pelanggan-name"><?= htmlspecialchars($nama) ?></div>
            <div class="pelanggan-phone"><?= htmlspecialchars($phone ?: '-') ?></div>
            <div class="pelanggan-stats">
              <div class="p-stat"><span><?= $jumlahRsv ?></span>Reservasi</div>
              <div class="p-stat"><span><?= $p['rating'] ?? '-' ?></span>Rating</div>
            </div>
            <div class="pelanggan-actions">
              <button class="action-btn" title="Edit" onclick="editPelanggan(<?= (int)$p['id'] ?>)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
              </button>
              <?php if ($phone): ?>
                <button class="action-btn" title="WhatsApp"
                  onclick="window.open('https://wa.me/62<?= htmlspecialchars($phoneWa, ENT_QUOTES) ?>','_blank')">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                  </svg>
                </button>
                <button class="action-btn" title="Telepon"
                  onclick="window.location.href='tel:<?= htmlspecialchars($phone, ENT_QUOTES) ?>'">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.38 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.87a16 16 0 0 0 5.97 5.97l1.45-1.45a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                  </svg>
                </button>
              <?php endif; ?>
              <button class="action-btn" title="Hapus" style="color:#a33030"
                onclick="hapusPelanggan(<?= (int)$p['id'] ?>, '<?= htmlspecialchars($nama, ENT_QUOTES) ?>')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"/>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div><!-- /page-pelanggan -->
