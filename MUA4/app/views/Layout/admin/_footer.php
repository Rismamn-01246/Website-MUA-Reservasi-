  </div><!-- /content -->
</div><!-- /main -->

<!-- MODALS -->

<!-- Modal Detail Reservasi -->
<div class="modal-overlay" id="modal-detail-reservasi">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Detail Reservasi</div>
      <button class="modal-close" onclick="closeModal('modal-detail-reservasi')">✕</button>
    </div>
    <div class="detail-grid">
      <div class="detail-item"><div class="dk">Pelanggan</div><div class="dv" id="dd-name">—</div></div>
      <div class="detail-item"><div class="dk">Layanan</div><div class="dv" id="dd-service">—</div></div>
      <div class="detail-item"><div class="dk">Tanggal</div><div class="dv" id="dd-date">—</div></div>
      <div class="detail-item"><div class="dk">Waktu</div><div class="dv" id="dd-time">—</div></div>
      <div class="detail-item"><div class="dk">Lokasi</div><div class="dv" id="dd-loc">—</div></div>
      <div class="detail-item">
        <div class="dk">Total Harga</div>
        <div class="dv" id="dd-price" style="color:var(--pink-500);font-weight:600">—</div>
      </div>
    </div>
    <input type="hidden" id="dd-id" value="">
    <div style="padding:0 20px;margin-top:16px">
      <div class="form-group">
        <label class="form-label">Status</label>
        <select class="form-select" id="dd-status-select">
          <option value="pending">Menunggu</option>
          <option value="confirmed">Dikonfirmasi</option>
          <option value="done">Selesai</option>
          <option value="cancelled">Dibatalkan</option>
        </select>
      </div>
      <div class="form-group" style="margin-top:12px">
        <label class="form-label">Catatan</label>
        <textarea class="form-textarea" id="dd-notes" placeholder="Tambah catatan..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeModal('modal-detail-reservasi')">Tutup</button>
      <button class="btn-primary" onclick="saveDetailReservasi()">Simpan</button>
    </div>
  </div>
</div>

<!-- Modal Tambah Reservasi -->
<div class="modal-overlay" id="modal-tambah-reservasi">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Tambah Reservasi</div>
      <button class="modal-close" onclick="closeModal('modal-tambah-reservasi')">✕</button>
    </div>
    <form action="<?= $basePath ?>/tambah-reservasi.php" method="POST">
      <div style="padding:0 20px">
        <div class="form-group" style="margin-top:20px">
          <label class="form-label">Nama Pelanggan</label>
          <input class="form-input" type="text" name="fullname" placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Nomor WhatsApp</label>
          <input class="form-input" type="text" name="phone" placeholder="08xx-xxxx-xxxx">
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Layanan</label>
          <select class="form-select" name="service">
            <?php foreach ($daftarLayanan as $lyr): ?>
              <?php if ($lyr['status'] === 'aktif'): ?>
                <option value="<?= htmlspecialchars($lyr['nama']) ?>"><?= htmlspecialchars($lyr['nama']) ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row" style="margin-top:12px">
          <div class="form-group">
            <label class="form-label">Tanggal</label>
            <input class="form-input" type="date" name="reservasi_date" required>
          </div>
          <div class="form-group">
            <label class="form-label">Waktu</label>
            <input class="form-input" type="time" name="reservasi_time">
          </div>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Lokasi Acara</label>
          <input class="form-input" type="text" name="address" placeholder="Nama venue / alamat" required>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Catatan Tambahan</label>
          <textarea class="form-textarea" name="notes" placeholder="Permintaan khusus, tema, dll..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-tambah-reservasi')">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Pelanggan -->
<div class="modal-overlay" id="modal-tambah-pelanggan">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Tambah Pelanggan</div>
      <button class="modal-close" onclick="closeModal('modal-tambah-pelanggan')">✕</button>
    </div>
    <form action="<?= $basePath ?>/tambah-pelanggan.php" method="POST">
      <div style="padding:0 20px">
        <div class="form-row" style="margin-top:20px">
          <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input class="form-input" type="text" name="fullname" placeholder="Nama pelanggan" required>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor WhatsApp</label>
            <input class="form-input" type="text" name="phone" placeholder="08xx-xxxx-xxxx">
          </div>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Email (opsional)</label>
          <input class="form-input" type="email" name="email" placeholder="email@contoh.com">
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Alamat</label>
          <input class="form-input" type="text" name="address" placeholder="Surabaya, Malang, dll">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-tambah-pelanggan')">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Pelanggan -->
<div class="modal-overlay" id="modal-edit-pelanggan">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Pelanggan</div>
      <button class="modal-close" onclick="closeModal('modal-edit-pelanggan')">✕</button>
    </div>
    <form action="<?= $basePath ?>/aksi-pelanggan.php" method="POST">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" id="edit-pelanggan-id">
      <div style="padding:0 20px">
        <div class="form-row" style="margin-top:20px">
          <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input class="form-input" type="text" name="fullname" id="edit-pelanggan-nama" required>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor WhatsApp</label>
            <input class="form-input" type="text" name="phone" id="edit-pelanggan-phone">
          </div>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Email (opsional)</label>
          <input class="form-input" type="email" name="email" id="edit-pelanggan-email">
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Alamat</label>
          <input class="form-input" type="text" name="address" id="edit-pelanggan-address">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-pelanggan')">Batal</button>
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Upload Foto Portofolio -->
<div class="modal-overlay" id="modal-upload-foto">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Upload Foto Portofolio</div>
      <button class="modal-close" onclick="closeModal('modal-upload-foto')">✕</button>
    </div>
    <form action="<?= $basePath ?>/upload-portofolio.php" method="POST" enctype="multipart/form-data">
      <div style="padding:0 20px;margin-top:20px">
        <label style="display:block;border:2px dashed var(--nude-200);border-radius:10px;padding:32px;text-align:center;background:var(--nude-50);cursor:pointer;margin-bottom:16px">
          <input type="file" name="foto" accept="image/*" style="display:none" onchange="previewFoto(this)">
          <div id="foto-preview-wrap">
            <div style="font-size:28px;margin-bottom:8px">📷</div>
            <div style="font-size:14px;font-weight:500;color:var(--text-mid)">Klik untuk pilih foto</div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:4px">JPG, PNG maks 5MB</div>
          </div>
          <img id="foto-preview" src="" alt="" style="display:none;max-height:180px;border-radius:8px;object-fit:cover">
        </label>
        <div class="form-group">
          <label class="form-label">Judul Foto</label>
          <input class="form-input" type="text" name="judul" placeholder="Contoh: Wedding Look #5" required>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Kategori</label>
          <select class="form-select" name="kategori">
            <option>Wedding</option><option>Wisuda</option>
            <option>Engagement</option><option>Party</option><option>Photoshoot</option>
          </select>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Tanggal</label>
          <input class="form-input" type="date" name="tanggal">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-upload-foto')">Batal</button>
        <button type="submit" class="btn-primary">Upload</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Portofolio -->
<div class="modal-overlay" id="modal-edit-portofolio">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Portofolio</div>
      <button class="modal-close" onclick="closeModal('modal-edit-portofolio')">✕</button>
    </div>
    <form action="<?= $basePath ?>/edit-portofolio.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit-portofolio-id">
      <div style="padding:0 20px;margin-top:20px">
        <label style="display:block;border:2px dashed var(--nude-200);border-radius:10px;padding:32px;text-align:center;background:var(--nude-50);cursor:pointer;margin-bottom:16px">
          <input type="file" name="foto" accept="image/*" style="display:none" onchange="previewFotoEdit(this)">
          <div id="foto-edit-preview-wrap">
            <div style="font-size:28px;margin-bottom:8px">📷</div>
            <div style="font-size:14px;font-weight:500;color:var(--text-mid)">Klik untuk ganti foto (opsional)</div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:4px">Biarkan kosong jika tidak ingin mengganti foto</div>
          </div>
          <img id="foto-edit-preview" src="" alt="" style="display:none;max-height:180px;border-radius:8px;object-fit:cover">
        </label>
        <div class="form-group">
          <label class="form-label">Judul Foto</label>
          <input class="form-input" type="text" name="judul" id="edit-portofolio-judul" required>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Kategori</label>
          <select class="form-select" name="kategori" id="edit-portofolio-kategori">
            <option>Wedding</option><option>Wisuda</option>
            <option>Engagement</option><option>Party</option><option>Photoshoot</option>
          </select>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Tanggal</label>
          <input class="form-input" type="date" name="tanggal" id="edit-portofolio-tanggal">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-portofolio')">Batal</button>
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Layanan -->
<div class="modal-overlay" id="modal-tambah-layanan">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Tambah Layanan</div>
      <button class="modal-close" onclick="closeModal('modal-tambah-layanan')">✕</button>
    </div>
    <form action="<?= $basePath ?>/tambah-layanan.php" method="POST">
      <div style="padding:0 20px">
        <div class="form-group" style="margin-top:20px">
          <label class="form-label">Nama Layanan</label>
          <input class="form-input" type="text" name="nama" placeholder="Contoh: Bridal Makeup Premium" required>
        </div>
        <div class="form-row" style="margin-top:12px">
          <div class="form-group">
            <label class="form-label">Harga (Rp)</label>
            <input class="form-input" type="number" name="harga" placeholder="0" required>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              <option value="aktif">Aktif</option>
              <option value="nonaktif">Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Deskripsi</label>
          <textarea class="form-textarea" name="deskripsi" placeholder="Deskripsi singkat layanan..."></textarea>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Yang Termasuk (pisahkan dengan Enter)</label>
          <textarea class="form-textarea" name="yang_termasuk" style="min-height:100px"
            placeholder="Makeup Full Coverage&#10;Hair do&#10;Free touch up"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-tambah-layanan')">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Layanan -->
<div class="modal-overlay" id="modal-edit-layanan">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Layanan</div>
      <button class="modal-close" onclick="closeModal('modal-edit-layanan')">✕</button>
    </div>
    <form action="<?= $basePath ?>/layanan-action.php" method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit-layanan-id">
      <div style="padding:0 20px">
        <div class="form-group" style="margin-top:20px">
          <label class="form-label">Nama Layanan</label>
          <input class="form-input" type="text" name="nama" id="edit-layanan-nama" required>
        </div>
        <div class="form-row" style="margin-top:12px">
          <div class="form-group">
            <label class="form-label">Harga (Rp)</label>
            <input class="form-input" type="number" name="harga" id="edit-layanan-harga" required>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" id="edit-layanan-status">
              <option value="aktif">Aktif</option>
              <option value="nonaktif">Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Deskripsi</label>
          <textarea class="form-textarea" name="deskripsi" id="edit-layanan-deskripsi"></textarea>
        </div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Yang Termasuk (pisahkan dengan Enter)</label>
          <textarea class="form-textarea" name="yang_termasuk" id="edit-layanan-termasuk" style="min-height:100px"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-layanan')">Batal</button>
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<div id="toast"></div>

<script>
const reservasiData    = <?= $reservasiJson ?>;
const daftarLayananJs  = <?= $layananJson ?>;
const pelangganDataJs  = <?= $pelangganJson ?>;
const portofolioDataJs = <?= $portofolioJson ?>;
const BASE_PATH        = '<?= $basePath ?>';

let currentFilter = 'semua';
function filterReservasi(status, btn) {
  currentFilter = status;
  document.querySelectorAll('#page-reservasi .filter-bar .filter-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  applyFilter();
}
function searchReservasi(q) { applyFilter(q); }
function globalSearch(q) {
  if (!q) return;
  navigate('reservasi', document.getElementById('nav-reservasi'));
  document.getElementById('search-reservasi').value = q;
  applyFilter(q);
}
function applyFilter(query) {
  query = (query !== undefined ? query : document.getElementById('search-reservasi').value).toLowerCase();
  document.querySelectorAll('#reservasi-tbody tr').forEach(function(row) {
    const matchFilter = currentFilter === 'semua' || row.dataset.status === currentFilter;
    const matchSearch = !query || (row.dataset.nama||'').includes(query) || (row.dataset.layanan||'').includes(query);
    row.style.display = (matchFilter && matchSearch) ? '' : 'none';
  });
}

let currentPelangganFilter = 'semua';
function filterPelanggan(tipe, btn) {
  currentPelangganFilter = tipe;
  document.querySelectorAll('#page-pelanggan .filter-bar .filter-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  applyPelangganFilter();
}
function searchPelanggan(q) { applyPelangganFilter(q); }
function applyPelangganFilter(query) {
  const q = (query !== undefined ? query : (document.getElementById('search-pelanggan')?.value||'')).toLowerCase();
  document.querySelectorAll('#page-pelanggan .pelanggan-card').forEach(function(card) {
    const nama  = (card.dataset.nama  || '');
    const phone = (card.dataset.phone || '').toLowerCase();
    const rsv   = parseInt(card.dataset.reservasi || '0', 10);
    let matchFilter = true;
    if (currentPelangganFilter === 'baru')   matchFilter = rsv <= 1;
    if (currentPelangganFilter === 'repeat') matchFilter = rsv > 1;
    const matchSearch = !q || nama.includes(q) || phone.includes(q);
    card.style.display = (matchFilter && matchSearch) ? '' : 'none';
  });
}

function editPelanggan(id) {
  const p = pelangganDataJs.find(x => x.id == id);
  if (!p) return;
  document.getElementById('edit-pelanggan-id').value      = p.id;
  document.getElementById('edit-pelanggan-nama').value    = p.nama;
  document.getElementById('edit-pelanggan-phone').value   = p.phone;
  document.getElementById('edit-pelanggan-email').value   = p.email;
  document.getElementById('edit-pelanggan-address').value = p.address;
  openModal('modal-edit-pelanggan');
}

let currentPortoFilter = 'semua';
function filterPortofolio(kategori, btn) {
  currentPortoFilter = kategori;
  document.querySelectorAll('#page-portofolio .filter-bar .filter-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  document.querySelectorAll('#page-portofolio .portfolio-item').forEach(function(item) {
    const kat = (item.dataset.kategori || '').toLowerCase();
    item.style.display = (kategori === 'semua' || kat === kategori.toLowerCase()) ? '' : 'none';
  });
}

function editPortofolio(id) {
  const item = portofolioDataJs.find(x => x.id == id);
  if (!item) return;
  document.getElementById('edit-portofolio-id').value       = item.id;
  document.getElementById('edit-portofolio-judul').value    = item.judul;
  document.getElementById('edit-portofolio-kategori').value = item.kategori;
  document.getElementById('edit-portofolio-tanggal').value  = item.tanggal;
  document.getElementById('foto-edit-preview').style.display = 'none';
  document.getElementById('foto-edit-preview-wrap').style.display = 'block';
  openModal('modal-edit-portofolio');
}
function previewFotoEdit(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('foto-edit-preview').src = e.target.result;
      document.getElementById('foto-edit-preview').style.display = 'block';
      document.getElementById('foto-edit-preview-wrap').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function openDetailModal(id) {
  const r = reservasiData.find(x => x.id == id);
  if (!r) return;
  document.getElementById('dd-id').value            = r.id;
  document.getElementById('dd-name').textContent    = r.nama;
  document.getElementById('dd-service').textContent = r.layanan;
  document.getElementById('dd-date').textContent    = r.tanggal_display || r.tanggal;
  document.getElementById('dd-time').textContent    = r.waktu;
  document.getElementById('dd-loc').textContent     = r.lokasi;
  document.getElementById('dd-price').textContent   = r.harga;
  document.getElementById('dd-status-select').value = r.status;
  document.getElementById('dd-notes').value         = r.catatan || '';
  openModal('modal-detail-reservasi');
}
function saveDetailReservasi() {
  const id     = document.getElementById('dd-id').value;
  const status = document.getElementById('dd-status-select').value;
  const notes  = document.getElementById('dd-notes').value;
  const fd     = new FormData();
  fd.append('action', 'update');
  fd.append('id', id);
  fd.append('status', status);
  fd.append('notes', notes);
  fetch(BASE_PATH + '/reservasi-action.php', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        closeModal('modal-detail-reservasi');
        showToast('Status reservasi diperbarui!');
        setTimeout(() => location.reload(), 800);
      } else {
        showToast('Gagal menyimpan: ' + (data.message || 'Error'));
      }
    })
    .catch(() => showToast('Terjadi kesalahan koneksi.'));
}

function editLayanan(id) {
  const l = daftarLayananJs.find(x => x.id == id);
  if (!l) return;
  document.getElementById('edit-layanan-id').value        = l.id;
  document.getElementById('edit-layanan-nama').value      = l.nama;
  document.getElementById('edit-layanan-harga').value     = l.harga;
  document.getElementById('edit-layanan-deskripsi').value = l.deskripsi;
  document.getElementById('edit-layanan-termasuk').value  = l.yang_termasuk;
  document.getElementById('edit-layanan-status').value    = l.status;
  openModal('modal-edit-layanan');
}

function ubahStatusLayanan(id, status) {
  if (confirm('Ubah status layanan ini menjadi ' + status + '?')) {
    window.location.href = BASE_PATH + '/layanan-action.php?action=status&id=' + id + '&status=' + status;
  }
}
function hapusLayanan(id, nama) {
  if (confirm('Yakin ingin menghapus layanan "' + nama + '"?')) {
    window.location.href = BASE_PATH + '/layanan-action.php?action=delete&id=' + id;
  }
}
function hapusPelanggan(id, nama) {
  if (confirm('Yakin ingin menghapus pelanggan "' + nama + '"?\nSemua data reservasi terkait juga akan terhapus.')) {
    window.location.href = BASE_PATH + '/aksi-pelanggan.php?action=delete&id=' + id;
  }
}
function ubahStatusTestimoni(id, status) {
  window.location.href = BASE_PATH + '/testimoni-action.php?action=status&id=' + id + '&status=' + status;
}
function hapusTestimoni(id) {
  if (confirm('Hapus testimoni ini?')) {
    window.location.href = BASE_PATH + '/testimoni-action.php?action=delete&id=' + id;
  }
}
function hapusPortofolio(id) {
  if (confirm('Yakin ingin menghapus portofolio ini?')) {
    window.location.href = BASE_PATH + '/hapus-portofolio.php?id=' + id;
  }
}
function previewFoto(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('foto-preview').src = e.target.result;
      document.getElementById('foto-preview').style.display = 'block';
      document.getElementById('foto-preview-wrap').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.modal-overlay').forEach(function(m) {
  m.addEventListener('click', function(e) { if (e.target === m) closeModal(m.id); });
});

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = '✓ ' + msg;
  t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:var(--text-dark);color:white;padding:12px 20px;border-radius:10px;font-size:13px;z-index:9999;opacity:1;transform:translateY(0);transition:all 0.3s;pointer-events:none';
  clearTimeout(t._timer);
  t._timer = setTimeout(function() { t.style.opacity='0'; t.style.transform='translateY(10px)'; }, 2800);
}

const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const now = new Date();
let currentYear = now.getFullYear(), currentMonth = now.getMonth();

function getEventDaysForMonth(year, month) {
  return reservasiData
    .filter(function(r) { const d = new Date(r.tanggal+'T00:00:00'); return d.getFullYear()===year && d.getMonth()===month; })
    .map(function(r) { return new Date(r.tanggal+'T00:00:00').getDate(); });
}
function renderCalendar() {
  const firstDay    = new Date(currentYear, currentMonth, 1).getDay();
  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  document.getElementById('calMonth').textContent = months[currentMonth] + ' ' + currentYear;
  const eventDays = getEventDaysForMonth(currentYear, currentMonth);
  const grid = document.getElementById('calGrid');
  const headers = Array.from(grid.querySelectorAll('.cal-dow'));
  grid.innerHTML = '';
  headers.forEach(function(h) { grid.appendChild(h.cloneNode(true)); });
  for (let i = 0; i < firstDay; i++) { const e=document.createElement('div'); e.className='cal-day empty'; grid.appendChild(e); }
  for (let d = 1; d <= daysInMonth; d++) {
    const e = document.createElement('div');
    let cls = 'cal-day';
    if (eventDays.includes(d)) cls += ' has-event';
    if (currentYear===now.getFullYear() && currentMonth===now.getMonth() && d===now.getDate()) cls += ' today';
    e.className = cls; e.textContent = d; grid.appendChild(e);
  }
}
function changeMonth(dir) {
  currentMonth += dir;
  if (currentMonth > 11) { currentMonth = 0; currentYear++; }
  if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
  renderCalendar();
}

const pageTitles = { dashboard:'Dashboard', reservasi:'Reservasi', pelanggan:'Pelanggan', portofolio:'Portofolio', layanan:'Layanan & Harga', testimoni:'Testimoni', pengaturan:'Pengaturan' };
const navIds = { dashboard:null, reservasi:'nav-reservasi', pelanggan:'nav-pelanggan', portofolio:'nav-portofolio', layanan:'nav-layanan', testimoni:'nav-testimoni', pengaturan:'nav-pengaturan' };

function navigate(page, el) {
  if (typeof event !== 'undefined' && event && event.preventDefault) event.preventDefault();
  document.querySelectorAll('.page').forEach(function(p) { p.classList.remove('active'); });
  const target = document.getElementById('page-' + page);
  if (target) target.classList.add('active');
  document.querySelectorAll('.nav-item').forEach(function(n) { n.classList.remove('active'); });
  let activeNav = el;
  if (!activeNav && navIds[page]) activeNav = document.getElementById(navIds[page]);
  if (!activeNav) { document.querySelectorAll('.nav-item').forEach(function(n) { const oc=n.getAttribute('onclick')||''; if(oc.includes("'"+page+"'")) activeNav=n; }); }
  if (activeNav) activeNav.classList.add('active');
  document.getElementById('pageTitle').textContent = pageTitles[page] || page;
  closeSidebar();
}

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('show');
}

const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
document.getElementById('currentDate').textContent = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();

renderCalendar();

(function() {
  const params = new URLSearchParams(window.location.search);
  const pages  = ['layanan','portofolio','testimoni','reservasi','pelanggan'];
  for (const p of pages) { if (params.has(p)) { navigate(p, null); break; } }
})();
</script>
</body>
