        <!-- ════════════════════ VIEW: PROFIL ════════════════════ -->
        <div class="view" id="view-profil">
          <div class="main-grid">
            <div class="card">
              <div class="card-header"><span class="card-title">Informasi Akun</span></div>
              <div class="card-body">
                <?php if (!empty($profilSuccess)): ?>
                  <div style="background:#e8f5ee;color:#2d7a50;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;">✓ Profil berhasil diperbarui.</div>
                <?php endif; ?>
                <?php if (!empty($profilError)): ?>
                  <div style="background:#fdecea;color:#b00020;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;">✗ <?= htmlspecialchars($profilError) ?></div>
                <?php endif; ?>

                <form method="post" action="">
                  <input type="hidden" name="action" value="update_profile" />
                  <div style="text-align:center;margin-bottom:20px;">
                    <div class="profil-avatar"><?= strtoupper(substr($fullnameSafe, 0, 1)) ?></div>
                    <div class="profile-avatar-name"><?= $fullnameSafe ?></div>
                    <div class="profile-avatar-email"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-input" name="fullname" value="<?= $fullnameSafe ?>" />
                  </div>
                  <div class="form-grid">
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label">Email</label>
                      <input type="email" class="form-input" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" />
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label">No. WhatsApp</label>
                      <input type="text" class="form-input" name="phone" value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" />
                    </div>
                  </div>
                  <div class="form-group" style="margin-top:14px;">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-textarea" name="address" placeholder="Alamat lengkap"><?= htmlspecialchars($_SESSION['address'] ?? '') ?></textarea>
                  </div>
                  <div class="subsection-title">Ganti Password</div>
                  <div class="form-group">
                    <label class="form-label">Password Saat Ini</label>
                    <input type="password" class="form-input" name="old_password" placeholder="••••••••" />
                  </div>
                  <div class="form-grid">
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label">Password Baru</label>
                      <input type="password" class="form-input" name="new_password" placeholder="••••••••" />
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label">Konfirmasi Password</label>
                      <input type="password" class="form-input" name="confirm_password" placeholder="••••••••" />
                    </div>
                  </div>
                  <button type="submit" class="form-submit" style="margin-top:18px;">Simpan Perubahan</button>
                </form>
              </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:20px;">
              <div class="card">
                <div class="card-header"><span class="card-title">Preferensi Notifikasi</span></div>
                <div class="card-body">
                  <div class="toggle-row">
                    <div><div class="toggle-label">Pengingat Reservasi</div><div class="toggle-desc">Kirim pengingat H-1 sebelum jadwal acara</div></div>
                    <label class="switch"><input type="checkbox" checked /><span class="switch-slider"></span></label>
                  </div>
                  <div class="toggle-row">
                    <div><div class="toggle-label">Update Status Reservasi</div><div class="toggle-desc">Notifikasi saat status reservasi berubah</div></div>
                    <label class="switch"><input type="checkbox" checked /><span class="switch-slider"></span></label>
                  </div>
                  <div class="toggle-row">
                    <div><div class="toggle-label">Promo &amp; Penawaran</div><div class="toggle-desc">Info promo dan diskon layanan terbaru</div></div>
                    <label class="switch"><input type="checkbox" /><span class="switch-slider"></span></label>
                  </div>
                  <div class="toggle-row">
                    <div><div class="toggle-label">Notifikasi via Email</div><div class="toggle-desc">Kirim semua notifikasi juga ke email</div></div>
                    <label class="switch"><input type="checkbox" checked /><span class="switch-slider"></span></label>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header"><span class="card-title">Akun</span></div>
                <div class="card-body" style="font-size:13px;color:var(--text-mid);line-height:1.8;">
                  <p>Bergabung sejak <strong><?php
                    $joinDate = $_SESSION['created_at'] ?? null;
                    if ($joinDate) {
                        $bpj = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                                7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        $jd  = new DateTime($joinDate);
                        echo $bpj[(int)$jd->format('n')] . ' ' . $jd->format('Y');
                    } else { echo '-'; }
                  ?></strong></p>
                  <p style="margin-top:6px;">Total reservasi: <strong><?= $totalReservasi ?></strong></p>
                  <a href="logout.php" class="btn-outline" style="display:block;text-align:center;margin-top:14px;">⎋ Keluar dari Akun</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /PROFIL -->
