        <div class="view show" id="view-beranda">
          <?php
          $bulanSingkat = [
              1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
              7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des',
          ];
          $statusInfo = [
              'pending'   => ['box'=>'upcoming',  'badge'=>'pending',   'label'=>'Menunggu',     'link'=>'Detail →'],
              'confirmed' => ['box'=>'upcoming',  'badge'=>'confirmed', 'label'=>'Dikonfirmasi', 'link'=>'Detail →'],
              'done'      => ['box'=>'done',      'badge'=>'done-b',    'label'=>'Selesai',      'link'=>'Ulasan →'],
              'cancelled' => ['box'=>'cancelled', 'badge'=>'cancelled', 'label'=>'Dibatalkan',   'link'=>'Detail →'],
          ];

          $allReservasi   = $riwayatReservasi ?? [];
          $totalReservasi = count($allReservasi);
          $jumlahMenunggu = 0;
          $jumlahSelesai  = 0;
          foreach ($allReservasi as $r) {
              $st = $r['status'] ?? 'pending';
              if ($st === 'pending')  $jumlahMenunggu++;
              if ($st === 'done')     $jumlahSelesai++;
          }

          $upcoming = array_filter($allReservasi, fn($r) => in_array($r['status'] ?? 'pending', ['pending','confirmed']));
          usort($upcoming, fn($a,$b) => strtotime($a['reservasi_date']) - strtotime($b['reservasi_date']));
          $nextReservasi = array_values($upcoming)[0] ?? null;

          $recentReservasi = $allReservasi;
          usort($recentReservasi, fn($a,$b) => strtotime($b['reservasi_date']) - strtotime($a['reservasi_date']));
          $recentReservasi = array_slice($recentReservasi, 0, 3);
          ?>

          <div class="hero-welcome">
            <div class="hero-text">
              <div class="hero-greeting">Selamat datang kembali ✦</div>
              <div class="hero-name"><?= $fullnameSafe ?></div>
              <div class="hero-sub">
                <?php if ($nextReservasi):
                  $nStatus  = $nextReservasi['status'] ?? 'pending';
                  $nTanggal = new DateTime($nextReservasi['reservasi_date']);
                  $nTgl     = $nTanggal->format('d') . ' ' . $bulanSingkat[(int)$nTanggal->format('n')] . ' ' . $nTanggal->format('Y');
                ?>
                  <?= $nStatus === 'confirmed'
                    ? 'Reservasi ' . htmlspecialchars($nextReservasi['service']) . ' kamu sudah dikonfirmasi untuk ' . $nTgl . ' 🌸'
                    : 'Reservasi ' . htmlspecialchars($nextReservasi['service']) . ' untuk ' . $nTgl . ' sedang menunggu konfirmasi admin 🙏' ?>
                <?php else: ?>
                  Belum ada reservasi mendatang. Yuk buat reservasi sekarang! 🌸
                <?php endif; ?>
              </div>
            </div>
            <div class="hero-action">
              <a href="#" class="btn-primary" onclick="sv('reservasi'); return false;">Buat Reservasi Baru</a>
            </div>
          </div>

          <div class="stats-row">
            <div class="mini-stat">
              <div class="mini-stat-icon a">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="4" width="18" height="18" rx="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
              </div>
              <div><div class="mini-stat-val"><?= $totalReservasi ?></div><div class="mini-stat-label">Total Reservasi</div></div>
            </div>
            <div class="mini-stat">
              <div class="mini-stat-icon b">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
              </div>
              <div><div class="mini-stat-val"><?= $jumlahMenunggu ?></div><div class="mini-stat-label">Menunggu Konfirmasi</div></div>
            </div>
            <div class="mini-stat">
              <div class="mini-stat-icon c">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
              </div>
              <div><div class="mini-stat-val"><?= $jumlahSelesai ?></div><div class="mini-stat-label">Reservasi Selesai</div></div>
            </div>
          </div>

          <div class="main-grid">
            <div style="display:flex;flex-direction:column;gap:20px;">
              <!-- RESERVASI TERBARU -->
              <div class="card">
                <div class="card-header">
                  <span class="card-title">Reservasi Saya</span>
                  <button class="btn-link" onclick="sv('riwayat')">Semua →</button>
                </div>
                <div class="card-body">
                  <?php if (empty($recentReservasi)): ?>
                    <div class="empty-state"><div class="icon">🗂️</div><p>Belum ada reservasi. Yuk buat reservasi pertamamu!</p></div>
                  <?php else: ?>
                    <?php foreach ($recentReservasi as $item):
                      $status      = $item['status'] ?? 'pending';
                      $info        = $statusInfo[$status] ?? $statusInfo['pending'];
                      $tanggal     = new DateTime($item['reservasi_date']);
                      $tglHari     = $tanggal->format('d');
                      $tglBulan    = $bulanSingkat[(int)$tanggal->format('n')];
                      $tglFull     = $tglHari . ' ' . $tglBulan . ' ' . $tanggal->format('Y');
                      $jam         = (new DateTime($item['reservasi_time']))->format('H:i');
                      $btnColor    = ($status === 'cancelled') ? 'var(--text-muted)' : 'var(--pink-500)';
                    ?>
                      <div class="reservasi-item">
                        <div class="res-date-box <?= htmlspecialchars($info['box']) ?>">
                          <div class="dd"><?= htmlspecialchars($tglHari) ?></div>
                          <div class="mm"><?= htmlspecialchars($tglBulan) ?></div>
                        </div>
                        <div class="res-info">
                          <div class="res-service"><?= htmlspecialchars($item['service']) ?></div>
                          <div class="res-detail"><?= htmlspecialchars($jam) ?> WIB • <?= htmlspecialchars($item['address']) ?></div>
                          <span class="badge <?= htmlspecialchars($info['badge']) ?>"><?= htmlspecialchars($info['label']) ?></span>
                        </div>
                        <button onclick="bukaDetail(<?= htmlspecialchars(json_encode([
                          'id'      => $item['id']      ?? 0,
                          'service' => $item['service'] ?? '',
                          'date'    => $tglFull,
                          'time'    => $jam,
                          'address' => $item['address'] ?? '',
                          'notes'   => $item['notes']   ?? '',
                          'status'  => $status,
                          'label'   => $info['label'],
                        ])) ?>)"
                          style="font-size:12px;color:<?= $btnColor ?>;background:none;border:none;
                                 cursor:pointer;white-space:nowrap;text-decoration:underline;padding:0;font-family:inherit;">
                          <?= htmlspecialchars($info['link']) ?>
                        </button>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>

              <!-- PORTOFOLIO PREVIEW -->
              <div class="card">
                <div class="card-header">
                  <span class="card-title">Portofolio Hasil Makeup</span>
                  <a href="LandingPageMua.php#portfolio" class="btn-link">Lihat Semua →</a>
                </div>
                <div class="card-body">
                  <?php if (empty($portoPreview)): ?>
                    <div class="empty-state"><div class="icon">🖼️</div><p>Belum ada portofolio yang ditampilkan.</p></div>
                  <?php else: ?>
                    <div class="porto-grid" style="margin-bottom:0;">
                      <?php foreach ($portoPreview as $p): ?>
                        <div class="porto-item">
                          <div class="porto-thumb"
                               style="background-image:url('<?= htmlspecialchars($PORTO_FOLDER . $p['foto']) ?>');
                                      background-size:cover;background-position:center;
                                      height:200px;border-radius:10px;"></div>
                          <div class="porto-overlay">
                            <span class="porto-tag"><?= htmlspecialchars(strtoupper($p['kategori'])) ?></span>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- KANAN: LAYANAN POPULER -->
            <div style="display:flex;flex-direction:column;gap:20px;">
              <div class="card">
                <div class="card-header">
                  <span class="card-title">Layanan Populer</span>
                  <button class="btn-link" onclick="sv('katalog')">Lihat Semua →</button>
                </div>
                <div class="card-body" style="padding:12px 20px;">
                  <?php
                  $layananPopuler = array_slice($layananKatalog, 0, 5);
                  $lastIdx = count($layananPopuler) - 1;
                  foreach ($layananPopuler as $idx => $lay):
                  ?>
                    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;
                                <?= $idx < $lastIdx ? 'border-bottom:1px solid var(--nude-50);' : '' ?>">
                      <div style="font-size:22px;width:36px;text-align:center;"><?= $lay['emoji'] ?></div>
                      <div style="flex:1;">
                        <div style="font-size:13px;font-weight:500;color:var(--text-dark);"><?= htmlspecialchars($lay['nama']) ?></div>
                        <div style="font-size:11px;color:var(--text-muted);">Mulai <?= htmlspecialchars($lay['harga']) ?></div>
                      </div>
                      <button class="btn-outline" style="padding:5px 12px;font-size:11px;"
                              onclick="pilihLayanan('<?= htmlspecialchars($lay['nama']) ?>')">Pilih</button>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /BERANDA -->
