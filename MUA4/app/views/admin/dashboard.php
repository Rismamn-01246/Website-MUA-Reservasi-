    <!-- DASHBOARD PAGE -->
    <div class="page active" id="page-dashboard">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-top">
            <div class="stat-icon pink">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
            </div>
            <span class="stat-badge up">+12%</span>
          </div>
          <div class="stat-value"><?= $totalReservasi ?></div>
          <div class="stat-label">Total Reservasi Bulan Ini</div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <div class="stat-icon nude">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
              </svg>
            </div>
            <span class="stat-badge up">+5</span>
          </div>
          <div class="stat-value"><?= $totalPelanggan ?></div>
          <div class="stat-label">Total Pelanggan</div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <div class="stat-icon pink">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <line x1="12" y1="1" x2="12" y2="23"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
              </svg>
            </div>
            <span class="stat-badge up">+18%</span>
          </div>
          <div class="stat-value">Rp 6,4jt</div>
          <div class="stat-label">Pendapatan Bulan Ini</div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <div class="stat-icon nude">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
              </svg>
            </div>
            <span class="stat-badge up">4.9</span>
          </div>
          <div class="stat-value">4.9 / 5</div>
          <div class="stat-label">Rating Rata-rata</div>
        </div>
      </div>

      <div class="grid-2">
        <div class="card">
          <div class="card-header">
            <span class="card-title">Reservasi Terbaru</span>
            <button class="btn-link" onclick="navigate('reservasi', document.getElementById('nav-reservasi'))">Lihat Semua →</button>
          </div>
          <div class="card-body" style="padding:0">
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Pelanggan</th><th>Layanan</th><th>Tanggal</th><th>Status</th><th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="dashboard-tbody">
                  <?php foreach (array_slice($daftarReservasi, 0, 5) as $r): ?>
                    <?php
                      $nama     = htmlspecialchars($r['fullname']);
                      $inisialD = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $r['fullname'])));
                      $inisialD = substr($inisialD, 0, 2);
                      $s = $r['status'];
                    ?>
                    <tr>
                      <td>
                        <div class="cust-cell">
                          <div class="mini-avatar"><?= $inisialD ?></div>
                          <?= $nama ?>
                        </div>
                      </td>
                      <td><?= htmlspecialchars($r['service']) ?></td>
                      <td><?= date('d M Y', strtotime($r['reservasi_date'])) ?></td>
                      <td>
                        <span class="badge <?= $badgeMap[$s] ?? '' ?>">
                          <?= $labelMap[$s] ?? ucfirst($s) ?>
                        </span>
                      </td>
                      <td>
                        <button class="action-btn" onclick="openDetailModal(<?= $r['id'] ?>)" title="Detail">
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                          </svg>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><span class="card-title">Kalender Jadwal</span></div>
          <div class="card-body">
            <div class="calendar-mini">
              <div class="cal-nav">
                <button class="cal-nav-btn" onclick="changeMonth(-1)">‹</button>
                <span class="cal-month" id="calMonth"></span>
                <button class="cal-nav-btn" onclick="changeMonth(1)">›</button>
              </div>
              <div class="cal-grid" id="calGrid">
                <div class="cal-dow">Min</div><div class="cal-dow">Sen</div>
                <div class="cal-dow">Sel</div><div class="cal-dow">Rab</div>
                <div class="cal-dow">Kam</div><div class="cal-dow">Jum</div>
                <div class="cal-dow">Sab</div>
              </div>
            </div>
            <div style="margin-top:16px">
              <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;letter-spacing:0.5px;text-transform:uppercase">
                Jadwal Mendatang
              </div>
              <div id="upcoming-list">
                <?php
                  $bln = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
                  $upcoming = array_filter($daftarReservasi, fn($r) => !in_array($r['status'] ?? '', ['cancelled','done']));
                  $upcoming = array_slice(array_values($upcoming), 0, 5);
                ?>
                <?php foreach ($upcoming as $r): ?>
                  <?php [$y, $m, $d] = explode('-', date('Y-m-d', strtotime($r['reservasi_date']))); ?>
                  <div class="upcoming-item">
                    <div class="upcoming-date">
                      <div class="dd"><?= (int)$d ?></div>
                      <div class="mm"><?= $bln[(int)$m - 1] ?></div>
                    </div>
                    <div class="upcoming-info">
                      <div class="title">
                        <?= htmlspecialchars(str_replace(' Makeup', '', $r['service'])) ?>
                        — <?= htmlspecialchars($r['fullname']) ?>
                      </div>
                      <div class="sub">
                        <?= htmlspecialchars($r['reservasi_time']) ?>
                        • <?= htmlspecialchars($r['address']) ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid-2b">
        <div class="card">
          <div class="card-header">
            <span class="card-title">Layanan Populer</span>
            <button class="btn-link" onclick="navigate('layanan', document.getElementById('nav-layanan'))">Detail →</button>
          </div>
          <div class="card-body">
            <div class="service-stack">
              <?php foreach (array_slice($daftarLayanan, 0, 5) as $lyr): ?>
                <div class="service-chip">
                  <span><?= htmlspecialchars($lyr['nama']) ?></span>
                  <strong>Rp <?= number_format($lyr['harga'], 0, ',', '.') ?></strong>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><span class="card-title">Aktivitas Terbaru</span></div>
          <div class="card-body" style="padding-top:8px">
            <div id="activity-log">
              <?php foreach (array_slice($daftarReservasi, 0, 5) as $i => $r): ?>
                <div style="display:flex;gap:12px;padding:10px 0;<?= $i < 4 ? 'border-bottom:1px solid var(--nude-50)' : '' ?>">
                  <div style="width:32px;height:32px;border-radius:50%;background:var(--pink-50);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--pink-500)" stroke-width="2">
                      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                      <circle cx="9" cy="7" r="4"/>
                    </svg>
                  </div>
                  <div>
                    <div style="font-size:13px;color:var(--text-dark)">
                      Reservasi baru dari <b><?= htmlspecialchars($r['fullname']) ?></b>
                      untuk <b><?= htmlspecialchars($r['service']) ?></b>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px">
                      <?= date('d M Y, H:i', strtotime($r['created_at'] ?? $r['reservasi_date'])) ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /page-dashboard -->
