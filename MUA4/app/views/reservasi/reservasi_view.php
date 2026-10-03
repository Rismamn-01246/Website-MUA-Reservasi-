<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservasi Makeup — Aza MUA</title>
    <link rel="stylesheet" href="assets/css/reservasi.css" />
  </head>

  <body>
    <div class="reservation-container">
      <div class="left-side">
        <h1>Reservasi Makeup</h1>
        <p class="subtitle">Isi data reservasi untuk booking jadwal makeup</p>

        <?php if ($error): ?>
          <div class="error-message"><?= htmlspecialchars($error) ?></div>
          <?php if (!empty($slotStatusHariIni)): ?>
            <p style="font-size:13px;color:#888;margin-top:4px;">
              Slot tersedia pada tanggal tersebut:
            </p>
            <div class="slot-grid" style="margin-bottom:12px">
              <?php foreach ($slotStatusHariIni as $s): ?>
                <?php if ((int)$s['tersedia'] > 0): ?>
                  <span class="slot-btn" style="cursor:default">
                    <?= date('H:i', strtotime($s['jam'])) ?>
                    <span class="slot-label"><?= htmlspecialchars($s['keterangan'] ?? '') ?></span>
                  </span>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        <?php elseif ($success): ?>
          <div class="success-message">
            Reservasi berhasil disimpan. Kami akan menghubungi Anda segera. ✓
          </div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" id="formReservasi">

          <!-- Input tersembunyi untuk nilai jam yang dipilih -->
          <input type="hidden" name="time" id="hiddenTime" value="<?= htmlspecialchars($time) ?>" required />

          <div class="input-group">
            <label>Nama Lengkap</label>
            <input type="text" name="fullname" placeholder="Masukkan nama lengkap"
                   value="<?= htmlspecialchars($fullname) ?>" required />
          </div>

          <div class="input-group">
            <label>Nomor HP</label>
            <input type="text" name="phone" placeholder="Masukkan nomor HP"
                   value="<?= htmlspecialchars($phone) ?>" required />
          </div>

          <div class="input-group">
            <label>Jenis Layanan</label>
            <select name="service" required>
              <option value="">Pilih Layanan</option>
              <?php foreach ($layananList as $lyr): ?>
                <option value="<?= htmlspecialchars($lyr['nama']) ?>"
                  <?= $service === $lyr['nama'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($lyr['nama']) ?>
                  (Rp <?= number_format($lyr['harga'], 0, ',', '.') ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Tanggal -->
          <div class="input-group">
            <label>Tanggal Reservasi</label>
            <input type="date" id="inputTanggal" name="date"
                   value="<?= htmlspecialchars($date) ?>"
                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                   required />
          </div>

          <!-- Slot Jam (dinamis) -->
          <div class="input-group">
            <label>Pilih Jam</label>
            <div id="slotContainer">
              <p class="slot-hint">← Pilih tanggal dulu untuk melihat slot yang tersedia.</p>
            </div>
          </div>

          <div class="input-group">
            <label>Alamat</label>
            <textarea name="address" placeholder="Masukkan alamat lengkap" required><?= htmlspecialchars($address) ?></textarea>
          </div>

          <div class="input-group">
            <label>Catatan Tambahan</label>
            <textarea name="notes" placeholder="Tambahkan catatan jika ada"><?= htmlspecialchars($notes) ?></textarea>
          </div>

          <button type="submit">Reservasi Sekarang</button>
        </form>
      </div>

      <!-- RIGHT SIDE -->
      <div class="right-side">
        <div class="booking-card">
          <h2>Informasi Reservasi</h2>
          <div class="info-item">
            <i class="fa-solid fa-clock"></i>
            <p>Jam Operasional 08.00 - 18.00</p>
          </div>
          <div class="info-item">
            <i class="fa-solid fa-calendar"></i>
            <p>Reservasi minimal H-1 acara</p>
          </div>
          <div class="info-item">
            <i class="fa-solid fa-circle-xmark" style="color:#e57373"></i>
            <p>Slot yang <s>dicoret</s> sudah penuh</p>
          </div>
          <div class="info-item">
            <i class="fa-solid fa-location-dot"></i>
            <p>Melayani area kota &amp; luar kota</p>
          </div>
          <div class="info-item">
            <i class="fa-solid fa-star"></i>
            <p>Professional Makeup Artist</p>
          </div>
        </div>
      </div>
    </div>

    <script>
    // Daftar tanggal yang diblokir admin (dari PHP)
    const tanggalDiblokir = <?= json_encode($tanggalDiblokir) ?>;

    const inputTanggal   = document.getElementById('inputTanggal');
    const slotContainer  = document.getElementById('slotContainer');
    const hiddenTime     = document.getElementById('hiddenTime');

    // Nonaktifkan tanggal yang diblokir langsung di input date
    inputTanggal.addEventListener('input', function () {
      const tgl = this.value;
      if (!tgl) return;

      if (tanggalDiblokir.includes(tgl)) {
        slotContainer.innerHTML = '<div class="tanggal-blokir-msg">⚠️ Tanggal ini ditutup oleh admin. Silakan pilih tanggal lain.</div>';
        hiddenTime.value = '';
        return;
      }

      muat_slot(tgl);
    });

    async function muat_slot(tgl) {
      slotContainer.innerHTML = '<p class="slot-loading">Memuat slot tersedia…</p>';
      hiddenTime.value = '';

      try {
        const res  = await fetch(`reservasi.php?cek_slot=${tgl}`);
        const data = await res.json();

        if (data.diblokir) {
          slotContainer.innerHTML = '<div class="tanggal-blokir-msg">⚠️ Tanggal ini ditutup oleh admin. Silakan pilih tanggal lain.</div>';
          return;
        }

        if (!data.slots || data.slots.length === 0) {
          slotContainer.innerHTML = '<p class="slot-hint">Tidak ada slot tersedia untuk tanggal ini.</p>';
          return;
        }

        // Render tombol slot
        let html = '<div class="slot-grid">';
        data.slots.forEach(s => {
          const jam      = s.jam.substring(0, 5); // "09:00"
          const penuh    = parseInt(s.tersedia) <= 0;
          const klass    = penuh ? 'penuh' : '';
          const label    = s.keterangan ? `<span class="slot-label">${s.keterangan}</span>` : '';
          const onclick  = penuh ? '' : `onclick="pilihSlot(this, '${s.jam}')"`;
          html += `<button type="button" class="slot-btn ${klass}" data-jam="${s.jam}" ${onclick}>
                     ${jam}${label}
                   </button>`;
        });
        html += '</div>';

        slotContainer.innerHTML = html;

        // Kalau sebelumnya sudah ada jam terpilih (misal setelah error POST), restore seleksi
        const prevTime = hiddenTime.value;
        if (prevTime) {
          const btn = slotContainer.querySelector(`[data-jam="${prevTime}"]`);
          if (btn && !btn.classList.contains('penuh')) {
            btn.classList.add('selected');
          }
        }

      } catch (e) {
        slotContainer.innerHTML = '<p class="slot-hint">Gagal memuat slot. Coba refresh halaman.</p>';
      }
    }

    function pilihSlot(btn, jam) {
      // Hapus seleksi sebelumnya
      slotContainer.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      hiddenTime.value = jam;
    }

    // PERBAIKAN: nonaktifkan tombol submit setelah diklik supaya tidak
    // bisa terkirim dua kali (mencegah reservasi duplikat).
    document.getElementById('formReservasi').addEventListener('submit', function () {
      const btn = this.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
      }
    });

    // Kalau ada tanggal + jam terpilih dari POST sebelumnya (error validasi),
    // langsung muat ulang slot supaya customer tidak perlu klik tanggal lagi
    (function () {
      const tgl = inputTanggal.value;
      if (tgl) muat_slot(tgl);
    })();
    </script>
  </body>
</html>
