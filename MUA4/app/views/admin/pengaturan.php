    <!-- PENGATURAN PAGE -->
    <div class="page" id="page-pengaturan">
      <form action="<?= $basePath ?>/pengaturan-action.php" method="POST">
        <div class="page-header">
          <div>
            <div class="page-heading">Pengaturan</div>
            <div class="page-sub">Kelola profil dan konfigurasi sistem</div>
          </div>
          <button class="btn-primary" type="submit">Simpan Perubahan</button>
        </div>

        <?= $pengaturanMsg ?>

        <div class="setting-section">
          <div class="setting-section-header">
            <div class="setting-section-title">Profil Bisnis</div>
            <div class="setting-section-sub">Informasi yang ditampilkan di halaman publik</div>
          </div>
          <div class="setting-body">
            <div class="avatar-row">
              <div class="profile-pic"><?= substr($pengaturan['nama_bisnis'] ?? 'Aza', 0, 3) ?></div>
              <div>
                <div style="font-size:13px;font-weight:500;color:var(--text-dark);margin-bottom:4px">Foto Profil</div>
                <div style="font-size:12px;color:var(--text-muted)">JPG, PNG maks 2MB</div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nama Bisnis</label>
                <input class="form-input" name="nama_bisnis" type="text" value="<?= htmlspecialchars($pengaturan['nama_bisnis'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Nama Admin</label>
                <input class="form-input" name="nama_admin" type="text" value="<?= htmlspecialchars($pengaturan['nama_admin'] ?? '') ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nomor WhatsApp</label>
                <input class="form-input" name="whatsapp" type="text" value="<?= htmlspecialchars($pengaturan['whatsapp'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Email</label>
                <input class="form-input" name="email" type="email" value="<?= htmlspecialchars($pengaturan['email'] ?? '') ?>">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Kota / Lokasi</label>
              <input class="form-input" name="lokasi" type="text" value="<?= htmlspecialchars($pengaturan['lokasi'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Bio / Deskripsi</label>
              <textarea class="form-textarea" name="bio"><?= htmlspecialchars($pengaturan['bio'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <div class="setting-section">
          <div class="setting-section-header">
            <div class="setting-section-title">Jam Operasional</div>
            <div class="setting-section-sub">Atur ketersediaan booking</div>
          </div>
          <div class="setting-body">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Jam Buka</label>
                <input class="form-input" name="jam_buka" type="time" value="<?= htmlspecialchars($pengaturan['jam_buka'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Jam Tutup</label>
                <input class="form-input" name="jam_tutup" type="time" value="<?= htmlspecialchars($pengaturan['jam_tutup'] ?? '') ?>">
              </div>
            </div>
          </div>
        </div>

        <div class="setting-section">
          <div class="setting-section-header">
            <div class="setting-section-title">Keamanan</div>
            <div class="setting-section-sub">Kelola akses dan kata sandi</div>
          </div>
          <div class="setting-body">
            <div class="form-group">
              <label class="form-label">Kata Sandi Lama</label>
              <input class="form-input" name="old_password" type="password">
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Kata Sandi Baru</label>
                <input class="form-input" name="new_password" type="password">
              </div>
              <div class="form-group">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <input class="form-input" name="confirm_password" type="password">
              </div>
            </div>
          </div>
        </div>
      </form>
    </div><!-- /page-pengaturan -->
