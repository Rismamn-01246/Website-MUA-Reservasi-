    <!-- TESTIMONI PAGE -->
    <div class="page" id="page-testimoni">
      <div class="page-header">
        <div>
          <div class="page-heading">Testimoni</div>
          <div class="page-sub">Ulasan dari pelanggan setia Aza MUA</div>
        </div>
      </div>

      <?= $testimoniMsg ?>

      <div class="filter-bar">
        <button class="filter-btn active">Semua (<?= $totalTestimoni ?>)</button>
        <button class="filter-btn">Ditampilkan (<?= $totalTampil ?>)</button>
        <button class="filter-btn">Disembunyikan (<?= $totalSembunyikan ?>)</button>
        <button class="filter-btn">⭐ 5 Bintang (<?= $totalBintang5 ?>)</button>
        <button class="filter-btn">⭐ 4 Bintang (<?= $totalBintang4 ?>)</button>
      </div>

      <div class="testi-grid">
        <?php foreach ($daftarTestimoni as $t): ?>
          <?php
            $inisialT = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $t['nama'])));
            $inisialT = substr($inisialT, 0, 2);
            $rating   = (int)$t['rating'];
            $bintang  = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
          ?>
          <div class="testi-card" <?= $t['status'] === 'sembunyikan' ? 'style="opacity:.6"' : '' ?>>
            <div class="testi-header">
              <div class="mini-avatar"><?= $inisialT ?></div>
              <div>
                <div style="font-size:13px;font-weight:500;color:var(--text-dark)"><?= htmlspecialchars($t['nama']) ?></div>
                <div class="testi-stars"><?= $bintang ?></div>
              </div>
            </div>
            <div class="testi-text">"<?= htmlspecialchars($t['ulasan']) ?>"</div>
            <div style="font-size:11px;color:var(--text-muted)">
              <?= htmlspecialchars($t['layanan']) ?> • <?= date('d M Y', strtotime($t['created_at'])) ?>
            </div>
            <?php if ($t['status'] === 'sembunyikan'): ?>
              <div style="margin-top:10px"><span class="badge cancelled">Disembunyikan</span></div>
            <?php endif; ?>
            <div class="testi-actions">
              <?php if ($t['status'] === 'tampil'): ?>
                <button class="btn-secondary" style="flex:1;padding:7px 12px;font-size:12px"
                  onclick="ubahStatusTestimoni(<?= (int)$t['id'] ?>, 'sembunyikan')">Sembunyikan</button>
              <?php else: ?>
                <button class="btn-secondary" style="flex:1;padding:7px 12px;font-size:12px"
                  onclick="ubahStatusTestimoni(<?= (int)$t['id'] ?>, 'tampil')">Tampilkan</button>
              <?php endif; ?>
              <button class="btn-danger" style="flex:1;padding:7px 12px;font-size:12px"
                onclick="hapusTestimoni(<?= (int)$t['id'] ?>)">Hapus</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div><!-- /page-testimoni -->
