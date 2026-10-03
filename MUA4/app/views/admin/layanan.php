    <!-- LAYANAN PAGE -->
    <div class="page" id="page-layanan">
      <div class="page-header">
        <div>
          <div class="page-heading">Layanan & Harga</div>
          <div class="page-sub"><?= count($daftarLayanan) ?> layanan tersedia</div>
        </div>
        <button class="btn-primary" onclick="openModal('modal-tambah-layanan')">+ Tambah Layanan</button>
      </div>

      <?= $layananMsg ?>

      <div class="layanan-grid">
        <?php foreach ($daftarLayanan as $layanan): ?>
          <?php $includes = array_filter(explode("\n", $layanan['yang_termasuk'] ?? '')); ?>
          <div class="layanan-card">
            <div class="layanan-card-top">
              <div class="layanan-name"><?= htmlspecialchars($layanan['nama']) ?></div>
              <div class="badge <?= $layanan['status'] === 'aktif' ? 'confirmed' : 'cancelled' ?>">
                <?= ucfirst($layanan['status']) ?>
              </div>
            </div>
            <div class="layanan-price">Rp <?= number_format($layanan['harga'], 0, ',', '.') ?></div>
            <div class="layanan-desc"><?= htmlspecialchars($layanan['deskripsi']) ?></div>
            <ul class="layanan-includes">
              <?php foreach ($includes as $item): ?>
                <li><?= htmlspecialchars(trim($item)) ?></li>
              <?php endforeach; ?>
            </ul>
            <div class="layanan-footer">
              <button class="btn-secondary" style="flex:1" onclick="editLayanan(<?= (int)$layanan['id'] ?>)">Edit</button>
              <?php if ($layanan['status'] === 'aktif'): ?>
                <button class="btn-danger" style="flex:1"
                  onclick="ubahStatusLayanan(<?= (int)$layanan['id'] ?>, 'nonaktif')">Nonaktifkan</button>
              <?php else: ?>
                <button class="btn-primary" style="flex:1"
                  onclick="ubahStatusLayanan(<?= (int)$layanan['id'] ?>, 'aktif')">Aktifkan</button>
              <?php endif; ?>
              <button class="btn-danger" style="flex:1"
                onclick="hapusLayanan(<?= (int)$layanan['id'] ?>, '<?= htmlspecialchars($layanan['nama'], ENT_QUOTES) ?>')">Hapus</button>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="layanan-card"
          style="border:2px dashed var(--nude-200);box-shadow:none;display:flex;align-items:center;justify-content:center;min-height:220px;cursor:pointer"
          onclick="openModal('modal-tambah-layanan')">
          <div style="text-align:center">
            <div style="font-size:32px;color:var(--nude-300);margin-bottom:8px">+</div>
            <div style="font-size:13px;color:var(--text-muted)">Tambah Layanan Baru</div>
          </div>
        </div>
      </div>
    </div><!-- /page-layanan -->
