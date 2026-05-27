<?php
require_once 'config.php';
requireLogin();
$pageTitle    = 'Bank Soal';
$pageSubtitle = 'Kelola Bank Soal & Buat Paket Ujian';
$user = currentUser();
include 'includes/header.php';
?>

<div class="section-header">
  <div>
    <h2><span class="sh-icon">📚</span> Bank Soal</h2>
    <p>Kelola bank soal untuk semua mata pelajaran</p>
  </div>
  <div style="display:flex;gap:8px">
    <button class="btn btn-light btn-sm" onclick="exportSoal()">📥 Export</button>
    <button class="btn btn-primary" onclick="openTambahSoal()">➕ Tambah Soal</button>
  </div>
</div>

<!-- FILTER -->
<div class="card" style="margin-bottom:16px">
  <div class="card-body" style="padding:14px 18px">
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
      <div style="flex:1;min-width:180px">
        <label class="form-label">🔍 Cari Soal</label>
        <div class="search-input-wrap">
          <span class="si-icon">🔍</span>
          <input type="text" id="search-soal" class="form-control" placeholder="Kata kunci pertanyaan...">
        </div>
      </div>
      <div style="min-width:140px">
        <label class="form-label">Mata Pelajaran</label>
        <select class="form-select" id="filter-mapel" onchange="loadSoal()">
          <option value="">Semua Mapel</option>
          <?php
          $mapels = ['Pendidikan Agama','PPKn','Bahasa Indonesia','Matematika','IPA','IPS','SBdP','PJOK','Bahasa Inggris','Muatan Lokal'];
          foreach ($mapels as $m) echo "<option>$m</option>";
          ?>
        </select>
      </div>
      <div style="min-width:120px">
        <label class="form-label">Jenjang/Kelas</label>
        <select class="form-select" id="filter-kelas-soal" onchange="loadSoal()">
          <option value="">Semua</option>
          <?php for ($i=1;$i<=6;$i++) echo "<option value='$i'>Kelas $i</option>"; ?>
        </select>
      </div>
      <div style="min-width:120px">
        <label class="form-label">Tipe Soal</label>
        <select class="form-select" id="filter-tipe" onchange="loadSoal()">
          <option value="">Semua</option>
          <option value="pg">Pilihan Ganda</option>
          <option value="isian">Isian Singkat</option>
          <option value="uraian">Uraian</option>
        </select>
      </div>
      <button class="btn btn-outline-primary" onclick="loadSoal()">🔄 Refresh</button>
    </div>
  </div>
</div>

<!-- SOAL LIST -->
<div id="soal-container">
  <div class="text-center" style="padding:40px">
    <div class="spinner"></div>
    <p class="text-muted mt-2">Memuat bank soal...</p>
  </div>
</div>
<div class="pagination" id="pagination-soal" style="margin-top:16px"></div>

<!-- ============ MODAL TAMBAH SOAL ============ -->
<div class="modal-overlay" id="modal-tambah-soal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h4 id="modal-soal-title">➕ Tambah Soal ke Bank Soal</h4>
      <button class="modal-close" onclick="SISKO.modal.close('modal-tambah-soal')">✕</button>
    </div>
    <div class="modal-body">
      <form id="form-soal" onsubmit="simpanSoal(event)">
        <input type="hidden" id="soal-id" name="id">
        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label">Mata Pelajaran <span class="req">*</span></label>
              <select name="mapel" id="soal-mapel" class="form-select" required>
                <option value="">Pilih Mapel</option>
                <?php foreach ($mapels as $m) echo "<option>$m</option>"; ?>
              </select>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label">Kelas/Jenjang <span class="req">*</span></label>
              <select name="kelas" id="soal-kelas" class="form-select" required>
                <option value="">Pilih Kelas</option>
                <?php for ($i=1;$i<=6;$i++) echo "<option value='$i'>Kelas $i</option>"; ?>
              </select>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label">Tipe Soal <span class="req">*</span></label>
              <select name="tipe" id="soal-tipe" class="form-select" required onchange="toggleTipeForm(this.value)">
                <option value="">Pilih Tipe</option>
                <option value="pg">Pilihan Ganda</option>
                <option value="isian">Isian Singkat</option>
                <option value="uraian">Uraian</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label">Semester</label>
              <select name="semester" class="form-select">
                <option value="1">Semester 1 (Ganjil)</option>
                <option value="2">Semester 2 (Genap)</option>
              </select>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label">Tahun Pelajaran</label>
              <input type="text" name="tahun_ajaran" class="form-control" placeholder="2024/2025">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label">Tingkat Kesulitan</label>
              <select name="kesulitan" class="form-select">
                <option value="Mudah">Mudah</option>
                <option value="Sedang" selected>Sedang</option>
                <option value="Sulit">Sulit</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Pertanyaan / Soal <span class="req">*</span></label>
          <textarea name="pertanyaan" id="soal-pertanyaan" class="form-control" rows="4"
                    placeholder="Tulis pertanyaan / soal di sini..." required></textarea>
        </div>

        <!-- PILIHAN GANDA -->
        <div id="form-pg" style="display:none">
          <label class="form-label">Pilihan Jawaban <span class="req">*</span></label>
          <div id="pilihan-container">
            <?php foreach (['A','B','C','D'] as $opt): ?>
            <div class="pilihan-input-row" style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
              <div style="width:30px;height:30px;background:var(--primary);color:#fff;border-radius:50%;
                          display:flex;align-items:center;justify-content:center;font-weight:700;
                          font-size:.8rem;flex-shrink:0"><?= $opt ?></div>
              <input type="text" name="pilihan_<?= strtolower($opt) ?>" class="form-control" placeholder="Pilihan <?= $opt ?>">
              <label style="display:flex;align-items:center;gap:5px;white-space:nowrap;font-size:.8rem;cursor:pointer">
                <input type="radio" name="jawaban_benar" value="<?= strtolower($opt) ?>" style="cursor:pointer">
                Benar
              </label>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ISIAN/URAIAN -->
        <div id="form-isian">
          <div class="form-group">
            <label class="form-label">Kunci Jawaban <span class="req">*</span></label>
            <textarea name="kunci_jawaban" id="soal-kunci" class="form-control" rows="3"
                      placeholder="Tulis kunci jawaban di sini..."></textarea>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Pembahasan (Opsional)</label>
          <textarea name="pembahasan" class="form-control" rows="2"
                    placeholder="Penjelasan/pembahasan jawaban..."></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-light" onclick="SISKO.modal.close('modal-tambah-soal')">Batal</button>
      <button type="submit" form="form-soal" class="btn btn-primary" id="btn-simpan-soal">💾 Simpan Soal</button>
    </div>
  </div>
</div>

<?php
$extraJs = <<<'JS'
<script>
let allSoal = [];
let soalPage = 1;
const soalPerPage = 10;

function toggleTipeForm(val) {
  document.getElementById('form-pg').style.display = val === 'pg' ? 'block' : 'none';
  document.getElementById('form-isian').style.display = val !== 'pg' ? 'block' : 'none';
  // Required attrs
  document.querySelector('[name="kunci_jawaban"]').required = (val !== 'pg');
}

function openTambahSoal() {
  document.getElementById('soal-id').value = '';
  document.getElementById('modal-soal-title').textContent = '➕ Tambah Soal ke Bank Soal';
  document.getElementById('form-soal').reset();
  toggleTipeForm('');
  SISKO.modal.open('modal-tambah-soal');
}

async function loadSoal() {
  document.getElementById('soal-container').innerHTML =
    '<div class="text-center" style="padding:40px"><div class="spinner"></div></div>';

  const filters = {
    mapel:  document.getElementById('filter-mapel').value,
    kelas:  document.getElementById('filter-kelas-soal').value,
    tipe:   document.getElementById('filter-tipe').value,
  };
  const r = await GS.getData('BankSoal', filters);
  allSoal = r.data || [];

  const q = document.getElementById('search-soal').value.toLowerCase();
  let filtered = allSoal;
  if (q) filtered = allSoal.filter(s => (s.pertanyaan||'').toLowerCase().includes(q));

  const p = SISKO.paginate(filtered, soalPage, soalPerPage);
  renderSoalList(p.items, (soalPage-1)*soalPerPage);
  SISKO.renderPagination(document.getElementById('pagination-soal'), p.page, p.pages,
    'function(pg){soalPage=pg;loadSoal()}'
  );
}

function renderSoalList(rows, offset) {
  const cont = document.getElementById('soal-container');
  if (!rows.length) {
    cont.innerHTML = '<div class="empty-state"><div class="es-icon">📝</div><h4>Belum ada soal</h4><p>Tambah soal baru dengan tombol di atas</p></div>';
    return;
  }
  cont.innerHTML = rows.map((s, i) => {
    let pilihanHtml = '';
    if (s.tipe === 'pg') {
      ['a','b','c','d','e'].forEach(opt => {
        if (!s[`pilihan_${opt}`]) return;
        const isBenar = s.jawaban_benar === opt;
        pilihanHtml += `<div class="pilihan-item ${isBenar?'correct':''}">
          <span class="pilihan-label">${opt.toUpperCase()}</span>
          <span>${s[`pilihan_${opt}`]}</span>
          ${isBenar ? '<span style="margin-left:auto;font-size:.75rem">✅ Jawaban Benar</span>' : ''}
        </div>`;
      });
    } else {
      pilihanHtml = `<div style="background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;font-size:.82rem;margin-top:8px">
        <strong style="color:var(--success)">Kunci:</strong> ${s.kunci_jawaban||'-'}
      </div>`;
    }
    return `<div class="soal-card">
      <div class="soal-header">
        <div class="soal-number">${offset+i+1}</div>
        <div style="flex:1">
          <div style="font-size:.87rem;font-weight:500;line-height:1.5">${s.pertanyaan||''}</div>
          <div class="soal-meta">
            <span class="badge badge-primary">${s.mapel||'-'}</span>
            <span class="badge badge-info">Kelas ${s.kelas||'-'}</span>
            <span class="badge ${s.tipe==='pg'?'badge-success':s.tipe==='isian'?'badge-orange':'badge-purple'}">${s.tipe==='pg'?'Pilihan Ganda':s.tipe==='isian'?'Isian':'Uraian'}</span>
            <span class="badge ${s.kesulitan==='Mudah'?'badge-success':s.kesulitan==='Sulit'?'badge-danger':'badge-warning'}">${s.kesulitan||'Sedang'}</span>
            ${s.tahun_ajaran?`<span class="badge badge-info">${s.tahun_ajaran} Sem.${s.semester||'1'}</span>`:''}
          </div>
        </div>
        <div style="display:flex;gap:4px">
          <button class="btn btn-sm btn-warning" onclick="editSoal('${s.id}')">✏️</button>
          <button class="btn btn-sm btn-danger" onclick="hapusSoal('${s.id}')">🗑</button>
        </div>
      </div>
      ${s.tipe==='pg'?`<div class="pilihan-list">${pilihanHtml}</div>`:pilihanHtml}
      ${s.pembahasan?`<div style="margin-top:8px;padding:8px 12px;background:#fffbeb;border-radius:6px;font-size:.78rem;border-left:3px solid var(--warning)"><strong>💡 Pembahasan:</strong> ${s.pembahasan}</div>`:''}
    </div>`;
  }).join('');
}

function editSoal(id) {
  const s = allSoal.find(x => x.id == id);
  if (!s) return;
  document.getElementById('soal-id').value = s.id;
  document.getElementById('modal-soal-title').textContent = '✏️ Edit Soal';
  const fields = ['mapel','kelas','tipe','semester','tahun_ajaran','kesulitan','pertanyaan','kunci_jawaban','pembahasan',
    'pilihan_a','pilihan_b','pilihan_c','pilihan_d'];
  fields.forEach(f => {
    const el = document.querySelector(`#form-soal [name="${f}"]`);
    if (el) el.value = s[f] || '';
  });
  if (s.jawaban_benar) {
    const rb = document.querySelector(`[name="jawaban_benar"][value="${s.jawaban_benar}"]`);
    if (rb) rb.checked = true;
  }
  toggleTipeForm(s.tipe);
  SISKO.modal.open('modal-tambah-soal');
}

async function simpanSoal(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-simpan-soal');
  SISKO.btnLoading(btn, true);
  const fd = new FormData(document.getElementById('form-soal'));
  const data = Object.fromEntries(fd.entries());
  const id = data.id; delete data.id;
  let r;
  if (id) { r = await GS.updateRow('BankSoal', id, data); }
  else { data.id = Date.now().toString(); data.dibuat_oleh = '<?= $user['nama'] ?>'; r = await GS.addRow('BankSoal', data); }
  SISKO.btnLoading(btn, false);
  if (r.success) {
    SISKO.toast(id ? 'Soal diperbarui' : 'Soal berhasil ditambahkan', 'success');
    SISKO.modal.close('modal-tambah-soal');
    loadSoal();
  } else { SISKO.toast(r.error||'Gagal menyimpan', 'error'); }
}

function hapusSoal(id) {
  SISKO.confirm('Hapus soal ini dari bank soal?', async () => {
    const r = await GS.deleteRow('BankSoal', id);
    if (r.success) { SISKO.toast('Soal berhasil dihapus','success'); loadSoal(); }
    else SISKO.toast(r.error||'Gagal menghapus','error');
  });
}

function exportSoal() {
  SISKO.exportCSV(allSoal, 'bank_soal_'+new Date().toISOString().slice(0,10)+'.csv');
}

let stimer;
document.getElementById('search-soal').addEventListener('input', () => {
  clearTimeout(stimer);
  stimer = setTimeout(() => { soalPage=1; loadSoal(); }, 300);
});

document.addEventListener('DOMContentLoaded', loadSoal);
</script>
JS;
?>
<?php include 'includes/footer.php'; ?>
