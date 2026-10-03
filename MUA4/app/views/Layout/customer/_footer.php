      </div><!-- /container-inner -->
    </div><!-- /content -->
  </div><!-- /main -->

  <!-- ════════════════════ MODAL DETAIL RIWAYAT ════════════════════ -->
  <div id="modalOverlay">
    <div id="modalBox">
      <div class="modal-header">
        <div>
          <div class="modal-title" id="mdlTitle"></div>
          <div id="mdlBadge" style="margin-top:5px;"></div>
        </div>
        <button class="modal-close" onclick="tutupModal()">✕</button>
      </div>
      <div class="modal-body">

        <!-- Info reservasi -->
        <div class="modal-info-grid">
          <div class="modal-info-cell">
            <div class="mic-label">Tanggal</div>
            <div class="mic-val" id="mdlDate"></div>
          </div>
          <div class="modal-info-cell">
            <div class="mic-label">Waktu</div>
            <div class="mic-val" id="mdlTime"></div>
          </div>
          <div class="modal-info-cell full">
            <div class="mic-label">Lokasi</div>
            <div class="mic-val" id="mdlAddress"></div>
          </div>
          <div class="modal-info-cell full" id="mdlNotesCell" style="display:none;">
            <div class="mic-label">Catatan</div>
            <div class="mic-val" id="mdlNotes" style="font-weight:400;color:#6b4c43;"></div>
          </div>
        </div>

        <!-- Form Testimoni — hanya muncul jika status done -->
        <div class="testi-section" id="mdlTestiSection" style="display:none;">
          <div class="testi-title">Bagikan Pengalamanmu 🌸</div>
          <form method="post" action="" id="formTestimoni">
            <input type="hidden" name="action" value="kirim_testimoni" />
            <input type="hidden" name="reservasi_id" id="mdlResId" value="" />

            <div style="font-size:12px;color:#a07a71;margin-bottom:6px;">Rating Layanan</div>
            <div class="star-wrap" id="starWrap">
              <span class="star" data-v="1" onclick="pilihStar(1)" onmouseenter="hoverStar(1)" onmouseleave="resetHover()">★</span>
              <span class="star" data-v="2" onclick="pilihStar(2)" onmouseenter="hoverStar(2)" onmouseleave="resetHover()">★</span>
              <span class="star" data-v="3" onclick="pilihStar(3)" onmouseenter="hoverStar(3)" onmouseleave="resetHover()">★</span>
              <span class="star" data-v="4" onclick="pilihStar(4)" onmouseenter="hoverStar(4)" onmouseleave="resetHover()">★</span>
              <span class="star" data-v="5" onclick="pilihStar(5)" onmouseenter="hoverStar(5)" onmouseleave="resetHover()">★</span>
            </div>
            <input type="hidden" name="rating" id="mdlRating" value="0" />

            <textarea class="testi-textarea" name="komentar" rows="3"
              placeholder="Ceritakan pengalamanmu... (misalnya: pelayanannya ramah, hasil makeup tahan lama)"></textarea>

            <button type="submit" class="testi-submit">Kirim Testimoni ✦</button>
          </form>
        </div>

        <!-- Pesan untuk non-done -->
        <div class="modal-info-note" id="mdlInfoNote" style="display:none;">
          Ulasan hanya bisa diberikan setelah reservasi selesai.
        </div>

      </div>
    </div>
  </div>

  <script>
    /* ── SIDEBAR ── */
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('overlay').classList.toggle('show');
    }
    function closeSidebar() {
      document.getElementById('sidebar').classList.remove('open');
      document.getElementById('overlay').classList.remove('show');
    }

    /* ── VIEW SWITCHER ── */
    const VMETA = {
      beranda:  ['Beranda',              'Selamat datang kembali, <?= $fullnameSafe ?>'],
      reservasi:['Buat Reservasi Baru',  'Pilih layanan dan jadwalkan sesi makeup-mu'],
      riwayat:  ['Riwayat Reservasi',    'Pantau status semua reservasi kamu'],
      katalog:  ['Katalog Layanan',      'Semua layanan &amp; harga tersedia'],
      profil:   ['Profil &amp; Pengaturan','Kelola informasi akun dan preferensi notifikasi'],
    };
    function sv(id) {
      document.querySelectorAll('.view').forEach(v => v.classList.remove('show'));
      document.getElementById('view-' + id).classList.add('show');
      document.querySelectorAll('.nav-item[data-view]').forEach(a => {
        a.classList.toggle('active', a.dataset.view === id);
      });
      document.getElementById('pageTitle').textContent = VMETA[id][0];
      document.getElementById('pageDate').innerHTML    = VMETA[id][1];
      if (window.innerWidth <= 768) closeSidebar();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* Auto-buka view setelah POST */
    <?php if (!empty($profilSuccess) || !empty($profilError)): ?>
      document.addEventListener('DOMContentLoaded', () => sv('profil'));
    <?php endif; ?>
    <?php if (!empty($reservasiSuccess) || !empty($reservasiError)): ?>
      document.addEventListener('DOMContentLoaded', () => sv('reservasi'));
    <?php endif; ?>
    <?php if (!empty($testimoniSuccess) || !empty($testimoniError)): ?>
      document.addEventListener('DOMContentLoaded', () => sv('riwayat'));
    <?php endif; ?>

    /* ── PILIH LAYANAN ── */
    function pilihLayanan(nama) {
      sv('reservasi');
      const sel = document.getElementById('resLayanan');
      if (!sel) return;
      for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value === nama) {
          sel.selectedIndex = i;
          updateHargaInfo(sel);
          break;
        }
      }
    }

    /* ── INFO HARGA LAYANAN ── */
    function updateHargaInfo(sel) {
      const opt    = sel.options[sel.selectedIndex];
      const harga  = parseInt(opt.dataset.harga || '0', 10);
      const id     = parseInt(opt.dataset.id    || '0', 10);
      const idEl   = document.getElementById('resLayananId');
      const infoEl = document.getElementById('resHargaInfo');
      if (idEl)   idEl.value = id;
      if (infoEl) {
        if (harga > 0) {
          infoEl.textContent = '💰 Harga: Rp ' + harga.toLocaleString('id-ID');
          infoEl.style.display = 'block';
        } else {
          infoEl.style.display = 'none';
        }
      }
    }

    /* ── FILTER RIWAYAT ── */
    function filterRiwayat(btn, status) {
      document.querySelectorAll('.riwayat-tabs .cat-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const items = document.querySelectorAll('#riwayatList .reservasi-item');
      let visible = 0;
      items.forEach(it => {
        const show = status === 'semua' || it.dataset.status === status;
        it.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      document.getElementById('riwayatEmpty').style.display = visible === 0 ? 'block' : 'none';
    }

    /* ── FILTER KATALOG ── */
    function filterKatalog(btn, cat) {
      document.querySelectorAll('.kf-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('#katalogGrid .k-card').forEach(card => {
        card.style.display = (cat === 'semua' || card.dataset.cat === cat) ? '' : 'none';
      });
    }

    /* ── MODAL DETAIL ── */
    const BADGE_CLS = { pending:'pending', confirmed:'confirmed', done:'done-b', cancelled:'cancelled' };
    const BADGE_LBL = { pending:'Menunggu', confirmed:'Dikonfirmasi', done:'Selesai', cancelled:'Dibatalkan' };

    function bukaDetail(data) {
      document.getElementById('mdlTitle').textContent   = data.service;
      document.getElementById('mdlDate').textContent    = data.date;
      document.getElementById('mdlTime').textContent    = data.time + ' WIB';
      document.getElementById('mdlAddress').textContent = data.address;

      const notesCell = document.getElementById('mdlNotesCell');
      if (data.notes && data.notes.trim()) {
        document.getElementById('mdlNotes').textContent = data.notes;
        notesCell.style.display = '';
      } else {
        notesCell.style.display = 'none';
      }

      const cls = BADGE_CLS[data.status] || 'pending';
      document.getElementById('mdlBadge').innerHTML =
        `<span class="badge-modal ${cls}">${data.label}</span>`;

      const testiSection = document.getElementById('mdlTestiSection');
      const infoNote     = document.getElementById('mdlInfoNote');

      if (data.status === 'done') {
        testiSection.style.display = '';
        infoNote.style.display     = 'none';
        document.getElementById('mdlResId').value = data.id;
        resetStars();
      } else if (data.status === 'pending' || data.status === 'confirmed') {
        testiSection.style.display = 'none';
        infoNote.style.display     = 'block';
      } else {
        testiSection.style.display = 'none';
        infoNote.style.display     = 'none';
      }

      document.getElementById('modalOverlay').classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function tutupModal() {
      document.getElementById('modalOverlay').classList.remove('show');
      document.body.style.overflow = '';
    }

    document.getElementById('modalOverlay').addEventListener('click', function(e) {
      if (e.target === this) tutupModal();
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupModal(); });

    /* ── RATING BINTANG ── */
    let selectedStar = 0;

    function hoverStar(val) {
      document.querySelectorAll('#starWrap .star').forEach((s, i) => {
        s.style.color = i < val ? '#c9a96e' : '#e0d0cb';
      });
    }
    function resetHover() {
      document.querySelectorAll('#starWrap .star').forEach((s, i) => {
        s.style.color = i < selectedStar ? '#c9a96e' : '#e0d0cb';
      });
    }
    function pilihStar(val) {
      selectedStar = val;
      document.getElementById('mdlRating').value = val;
      resetHover();
    }
    function resetStars() {
      selectedStar = 0;
      document.getElementById('mdlRating').value = 0;
      document.querySelectorAll('#starWrap .star').forEach(s => s.style.color = '#e0d0cb');
    }
  </script>
</body>
