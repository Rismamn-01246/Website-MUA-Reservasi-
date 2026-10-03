    <!-- RESERVASI PAGE -->
    <div class="page" id="page-reservasi">
      <div class="page-header">
        <div>
          <div class="page-heading">Reservasi</div>
          <div class="page-sub" id="reservasi-sub"><?= $countSemua ?> total reservasi</div>
        </div>
        <button class="btn-primary" onclick="openModal('modal-tambah-reservasi')">+ Tambah Reservasi</button>
      </div>

      <?= $reservasiMsg ?>

      <div class="filter-bar">
        <button class="filter-btn active" id="filter-btn-semua"     onclick="filterReservasi('semua', this)">Semua (<span id="count-semua"><?= $countSemua ?></span>)</button>
        <button class="filter-btn"        id="filter-btn-confirmed" onclick="filterReservasi('confirmed', this)">Dikonfirmasi (<span id="count-confirmed"><?= $countConfirmed ?></span>)</button>
        <button class="filter-btn"        id="filter-btn-pending"   onclick="filterReservasi('pending', this)">Menunggu (<span id="count-pending"><?= $countPending ?></span>)</button>
        <button class="filter-btn"        id="filter-btn-done"      onclick="filterReservasi('done', this)">Selesai (<span id="count-done"><?= $countDone ?></span>)</button>
        <button class="filter-btn"        id="filter-btn-cancelled" onclick="filterReservasi('cancelled', this)">Dibatalkan (<span id="count-cancelled"><?= $countCancelled ?></span>)</button>
        <div class="filter-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" placeholder="Cari nama, layanan..." id="search-reservasi" oninput="searchReservasi(this.value)" />
        </div>
      </div>

      <div class="card">
        <div class="card-body" style="padding:0">
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>#</th><th>Pelanggan</th><th>Layanan</th><th>Tanggal</th>
                  <th>Waktu</th><th>Lokasi</th><th>Harga</th><th>Status</th><th>Aksi</th>
                </tr>
              </thead>
              <tbody id="reservasi-tbody">
                <?php foreach ($daftarReservasi as $index => $r): ?>
                  <?php
                    $s        = $r['status'];
                    $inisialR = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $r['fullname'])));
                    $inisialR = substr($inisialR, 0, 2);
                  ?>
                  <tr data-status="<?= $s ?>" data-nama="<?= strtolower($r['fullname']) ?>" data-layanan="<?= strtolower($r['service']) ?>">
                    <td style="color:var(--text-muted);font-size:12px"><?= str_pad($index + 1, 3, '0', STR_PAD_LEFT) ?></td>
                    <td>
                      <div class="cust-cell">
                        <div class="mini-avatar"><?= $inisialR ?></div>
                        <?= htmlspecialchars($r['fullname']) ?>
                      </div>
                    </td>
                    <td><?= htmlspecialchars($r['service']) ?></td>
                    <td><?= date('d M Y', strtotime($r['reservasi_date'])) ?></td>
                    <td><?= htmlspecialchars($r['reservasi_time']) ?></td>
                    <td><?= htmlspecialchars($r['address']) ?></td>
                    <td>Rp <?= number_format($r['price'] ?? 0, 0, ',', '.') ?></td>
                    <td>
                      <span class="badge <?= $badgeMap[$s] ?? '' ?>">
                        <?= $labelMap[$s] ?? ucfirst($s) ?>
                      </span>
                    </td>
                    <td style="display:flex;gap:6px">
                      <button class="action-btn" title="Detail" onclick="openDetailModal(<?= $r['id'] ?>)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                          <circle cx="12" cy="12" r="3"/>
                        </svg>
                      </button>
                      <?php if ($s === 'pending'): ?>
                        <a href="<?= $basePath ?>/reservasi-action.php?action=confirm&id=<?= $r['id'] ?>"
                           class="action-btn" title="Konfirmasi"
                           style="background:var(--pink-50);border-color:var(--pink-300);color:var(--pink-500)"
                           onclick="return confirm('Konfirmasi reservasi <?= htmlspecialchars($r['fullname'], ENT_QUOTES) ?>?')">
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                          </svg>
                        </a>
                      <?php endif; ?>
                      <a href="<?= $basePath ?>/reservasi-action.php?action=delete&id=<?= $r['id'] ?>"
                         class="action-btn" title="Hapus" style="color:#a33030"
                         onclick="return confirm('Hapus reservasi <?= htmlspecialchars($r['fullname'], ENT_QUOTES) ?>?')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <polyline points="3 6 5 6 21 6"/>
                          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div><!-- /page-reservasi -->
