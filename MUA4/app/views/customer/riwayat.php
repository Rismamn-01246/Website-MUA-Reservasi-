        <!-- ════════════════════ VIEW: RIWAYAT ════════════════════ -->
        <div class="view" id="view-riwayat">

          <?php if (!empty($testimoniSuccess)): ?>
            <div style="background:#e8f5ee;color:#2d7a50;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;">
              ✓ Terima kasih! Testimoni kamu telah berhasil dikirim 🌸
            </div>
          <?php endif; ?>
          <?php if (!empty($testimoniError)): ?>
            <div style="background:#fdecea;color:#b00020;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;">
              ✗ <?= htmlspecialchars($testimoniError) ?>
            </div>
          <?php endif; ?>

          <div class="riwayat-tabs">
            <button class="cat-btn active" onclick="filterRiwayat(this,'semua')">Semua</button>
            <button class="cat-btn" onclick="filterRiwayat(this,'pending')">Menunggu</button>
            <button class="cat-btn" onclick="filterRiwayat(this,'confirmed')">Dikonfirmasi</button>
            <button class="cat-btn" onclick="filterRiwayat(this,'done')">Selesai</button>
            <button class="cat-btn" onclick="filterRiwayat(this,'cancelled')">Dibatalkan</button>
          </div>

          <div class="card">
            <div class="card-header"><span class="card-title">Riwayat Reservasi</span></div>
            <div class="card-body" id="riwayatList">
              <?php if (empty($riwayatReservasi)): ?>
                <div class="empty-state"><div class="icon">🗂️</div><p>Belum ada riwayat reservasi.</p></div>
              <?php else: ?>
                <?php
                $statusInfo2 = [
                    'pending'   => ['box'=>'upcoming', 'badge'=>'pending',   'label'=>'Menunggu',     'btn'=>'Lihat Detail'],
                    'confirmed' => ['box'=>'upcoming', 'badge'=>'confirmed', 'label'=>'Dikonfirmasi', 'btn'=>'Lihat Detail'],
                    'done'      => ['box'=>'done',     'badge'=>'done-b',    'label'=>'Selesai',      'btn'=>'Beri Ulasan'],
                    'cancelled' => ['box'=>'cancelled','badge'=>'cancelled', 'label'=>'Dibatalkan',   'btn'=>'Lihat Detail'],
                ];
                $bln2 = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                         7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                foreach ($riwayatReservasi as $item):
                    $status   = $item['status'] ?? 'pending';
                    $info     = $statusInfo2[$status] ?? $statusInfo2['pending'];
                    $tanggal  = new DateTime($item['reservasi_date']);
                    $tglHari  = $tanggal->format('d');
                    $tglBulan = $bln2[(int)$tanggal->format('n')];
                    $tglFull  = $tglHari . ' ' . $tglBulan . ' ' . $tanggal->format('Y');
                    $jam      = (new DateTime($item['reservasi_time']))->format('H:i');
                    $btnColor = ($status === 'done') ? 'var(--pink-500)' : ($status === 'cancelled' ? 'var(--text-muted)' : 'var(--pink-500)');
                ?>
                  <div class="reservasi-item" data-status="<?= htmlspecialchars($status) ?>">
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
                             cursor:pointer;white-space:nowrap;text-decoration:underline;
                             padding:0;font-family:inherit;">
                      <?= htmlspecialchars($info['btn']) ?> →
                    </button>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <div class="empty-state" id="riwayatEmpty" style="display:none;">
              <div class="icon">🗂️</div><p>Tidak ada reservasi pada kategori ini.</p>
            </div>
          </div>
        </div>
        <!-- /RIWAYAT -->
