        <!-- ════════════════════ VIEW: RESERVASI ════════════════════ -->
        <div class="view" id="view-reservasi">
          <?php if ($reservasiSuccess): ?>
            <div style="background:#e8f5ee;color:#2d7a50;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;">
              ✓ Reservasi berhasil dikirim! Tim kami akan segera menghubungi kamu.
            </div>
          <?php endif; ?>
          <?php if ($reservasiError): ?>
            <div style="background:#fdecea;color:#b00020;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;">
              ✗ <?= htmlspecialchars($reservasiError) ?>
            </div>
          <?php endif; ?>

          <div class="main-grid">
            <div class="card">
              <div class="card-header"><span class="card-title">Buat Reservasi Baru</span></div>
              <div class="card-body">
                <form method="post" action="">
                  <input type="hidden" name="action" value="create_reservasi" />
                  <div class="form-group">
                    <label class="form-label">Jenis Layanan</label>
                    <select class="form-select" name="service" id="resLayanan" required
                            onchange="updateHargaInfo(this)">
                      <option value="" data-harga="0" data-id="0">Pilih layanan...</option>
                      <?php foreach ($layananKatalog as $lay): ?>
                        <option value="<?= htmlspecialchars($lay['nama']) ?>"
                                data-harga="<?= (int)($lay['harga_num'] ?? 0) ?>"
                                data-id="<?= (int)($lay['id'] ?? 0) ?>">
                          <?= htmlspecialchars($lay['nama']) ?>
                          (<?= htmlspecialchars($lay['harga']) ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="layanan_id" id="resLayananId" value="0">
                    <div id="resHargaInfo" style="display:none;margin-top:6px;font-size:12px;color:var(--pink-500);font-weight:500;"></div>
                  </div>
                  <div class="form-grid">
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label">Tanggal</label>
                      <input type="date" class="form-input" name="reservasi_date" required />
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label">Lokasi</label>
                      <input type="text" class="form-input" name="address" placeholder="Kota / Alamat" required />
                    </div>
                  </div>
                  <div class="form-group" style="margin-top:14px;">
                    <label class="form-label">Waktu</label>
                    <?php if (empty($slotAktif)): ?>
                      <p style="font-size:13px;color:#b00020;">Belum ada slot waktu tersedia. Silakan hubungi admin.</p>
                      <input type="hidden" name="reservasi_time" value="" />
                    <?php else: ?>
                      <select class="form-select" name="reservasi_time" required>
                        <option value="">Pilih jam...</option>
                        <?php foreach ($slotAktif as $slot):
                          $jamTampil = (new DateTime($slot['jam']))->format('H:i');
                          $ket       = $slot['keterangan'] ? ' — ' . htmlspecialchars($slot['keterangan']) : '';
                        ?>
                          <option value="<?= htmlspecialchars($slot['jam']) ?>"><?= $jamTampil . ' WIB' . $ket ?></option>
                        <?php endforeach; ?>
                      </select>
                      <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Pilih salah satu slot yang tersedia.</div>
                    <?php endif; ?>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea class="form-textarea" name="notes" placeholder="Misalnya: tema makeup, referensi foto, dll..."></textarea>
                  </div>
                  <button type="submit" class="form-submit">Kirim Reservasi 🌸</button>
                </form>
              </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:20px;">
              <div class="card">
                <div class="card-header"><span class="card-title">Tips Reservasi</span></div>
                <div class="card-body" style="font-size:13px;color:var(--text-mid);line-height:1.8;">
                  <p>✦ Reservasi minimal <strong>3 hari</strong> sebelum tanggal acara.</p>
                  <p style="margin-top:8px;">✦ Lampirkan referensi foto pada kolom catatan.</p>
                  <p style="margin-top:8px;">✦ Status dapat dipantau di menu <em>Riwayat</em>.</p>
                </div>
              </div>
              <div class="card">
                <div class="card-header"><span class="card-title">Butuh Bantuan?</span></div>
                <div class="card-body">
                  <p style="font-size:13px;color:var(--text-mid);margin-bottom:12px;">Tim Aza MUA siap membantu pemilihan layanan dan jadwal terbaik.</p>
                  <a href="https://wa.me/6281234567890" target="_blank" class="btn-outline" style="display:block;text-align:center;">💬 Chat via WhatsApp</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /RESERVASI -->
