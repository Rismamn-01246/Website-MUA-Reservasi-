    <!-- PORTOFOLIO PAGE -->
    <div class="page" id="page-portofolio">
      <div class="page-header">
        <div>
          <div class="page-heading">Portofolio</div>
          <div class="page-sub">Galeri karya makeup terbaik</div>
        </div>
        <button class="btn-primary" onclick="openModal('modal-upload-foto')">+ Upload Foto</button>
      </div>

      <?= $portofolioMsg ?>

      <div class="filter-bar">
        <button class="filter-btn active" onclick="filterPortofolio('semua', this)">Semua</button>
        <button class="filter-btn" onclick="filterPortofolio('Wedding', this)">Wedding</button>
        <button class="filter-btn" onclick="filterPortofolio('Wisuda', this)">Wisuda</button>
        <button class="filter-btn" onclick="filterPortofolio('Engagement', this)">Engagement</button>
        <button class="filter-btn" onclick="filterPortofolio('Party', this)">Party</button>
        <button class="filter-btn" onclick="filterPortofolio('Photoshoot', this)">Photoshoot</button>
      </div>

      <div class="portfolio-grid">
        <?php if (empty($daftarPortofolio)): ?>
          <div style="grid-column:1/-1;text-align:center;padding:40px;color:#888">
            Belum ada portofolio yang diupload.
          </div>
        <?php else: ?>
          <?php foreach ($daftarPortofolio as $item): ?>
            <div class="portfolio-item" data-kategori="<?= htmlspecialchars($item['kategori']) ?>">
              <div class="portfolio-img">
                <img src="uploads/portofolio/<?= htmlspecialchars($item['foto']) ?>"
                  alt="<?= htmlspecialchars($item['judul']) ?>"
                  style="width:100%;height:100%;object-fit:cover">
                <div class="portfolio-img-overlay">
                  <button title="Lihat"
                    onclick="window.open('uploads/portofolio/<?= htmlspecialchars($item['foto'], ENT_QUOTES) ?>','_blank')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--pink-500)" stroke-width="2">
                      <path d="M1 12s4-8 11-8s11 8 11 8s-4 8-11 8s-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                  <button title="Edit" onclick="editPortofolio(<?= (int)$item['id'] ?>)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--pink-500)" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button title="Hapus" onclick="hapusPortofolio(<?= (int)$item['id'] ?>)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#a33030" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="portfolio-info">
                <div class="title"><?= htmlspecialchars($item['judul']) ?></div>
                <div class="cat">
                  <?= htmlspecialchars($item['kategori']) ?>
                  • <?= !empty($item['tanggal']) ? date('d M Y', strtotime($item['tanggal'])) : '-' ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div><!-- /page-portofolio -->
