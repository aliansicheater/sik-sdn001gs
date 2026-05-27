<?php
require_once 'config.php';
requireAdmin();
$pageTitle    = 'Data PTK';
$pageSubtitle = 'Pendidik & Tenaga Kependidikan';
$user = currentUser();
include 'includes/header.php';
?>

<div class="section-header">
  <div>
    <h2><span class="sh-icon">👨‍🏫</span> Data PTK</h2>
    <p>Manajemen data Pendidik & Tenaga Kependidikan</p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <button class="btn btn-light btn-sm" onclick="exportPTK()">📥 Export CSV</button>
    <button class="btn btn-primary" onclick="openTambahPTK()">➕ Tambah PTK</button>
  </div>
</div>

<!-- FILTER -->
<div class="card" style="margin-bottom:16px">
  <div class="card-body" style="padding:14px 18px">
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
      <div style="flex:1;min-width:180px">
        <label class="form-label">🔍 Cari PTK</label>
        <div class="search-input-wrap">
          <span class="si-icon">🔍</span>
          <input type="text" id="search-ptk" class="form-control" placeholder="Nama, NIP, NUPTK...">
        </div>
      </div>
      <div style="min-width:140px">
        <label class="form-label">Status Kepegawaian</label>
        <select class="form-select" id="filter-status-ptk" onchange="loadPTK()">
          <option value="">Semua</option>
          <option>PNS</option><option>PPPK</option><option>GTT/PTT</option><option>Honorer</option>
        </select>
      </div>
      <div style="min-width:130px">
        <label class="form-label">Jenis PTK</label>
        <select class="form-select" id="filter-jenis-ptk" onchange="loadPTK()">
          <option value="">Semua</option>
          <option>Kepala Sekolah</option>
          <option>Guru Kelas</option>
          <option>Guru Mapel</option>
          <option>Tenaga Kependidikan</option>
        </select>
      </div>
      <button class="btn btn-outline-primary" onclick="loadPTK()">🔄 Refresh</button>
    </div>
  </div>
</div>

<!-- TABLE -->
<div class="card">
  <div class="card-header">
    <h3>📋 Daftar PTK</h3>
    <span class="badge badge-primary" id="total-ptk">0 orang</span>
  </div>
  <div class="table-wrapper">
    <table class="data-table" id="tbl-ptk">
      <thead>
        <tr>
          <th>No</th>
          <th>Foto</th>
          <th>NIP / NUPTK</th>
          <th>Nama Lengkap</th>
          <th>JK</th>
          <th>Jenis PTK</th>
          <th>Status</th>
          <th>Mapel / Tugas</th>
          <th>Rombel Diampu</th>
          <th>HP</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tbody-ptk">
        <tr><td colspan="11" class="text-center" style="padding:40px">
          <div class="spinner"></div>
          <p class="text-muted mt-2">Memuat data...</p>
        </td></tr>
      </tbody>
    </table>
  </div>
  <div class="card-footer">
    <div class="pagination" id="pagination-ptk"></div>
  </div>
</div>

<!-- ======= MODAL TAMBAH/EDIT PTK ======= -->
<div class="modal-overlay" id="modal-tambah-ptk">
  <div class="modal modal-xl">
    <div class="modal-header">
      <h4 id="modal-ptk-title">➕ Tambah Data PTK</h4>
      <button class="modal-close" onclick="SISKO.modal.close('modal-tambah-ptk')">✕</button>
    </div>
    <div class="modal-body">
      <form id="form-ptk" onsubmit="simpanPTK(event)">
        <input type="hidden" id="ptk-id" name="id">
        <div class="tabs-container">
          <div class="tabs">
            <button type="button" class="tab-btn active" data-tab="p-identitas">👤 Identitas</button>
            <button type="button" class="tab-btn" data-tab="p-kepeg">📋 Kepegawaian</button>
            <button type="button" class="tab-btn" data-tab="p-akademik">🎓 Akademik</button>
            <button type="button" class="tab-btn" data-tab="p-alamat">📍 Alamat & Kontak</button>
            <button type="button" class="tab-btn" data-tab="p-tugas">🏫 Tugas Mengajar</button>
          </div>

          <!-- IDENTITAS -->
          <div class="tab-content active" data-tab-content="p-identitas">
            <div class="form-row">
              <div class="form-col" style="flex:2">
                <div class="form-group">
                  <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                  <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap dengan gelar">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Jenis Kelamin <span class="req">*</span></label>
                  <select name="jk" class="form-select" required>
                    <option value="">Pilih</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">NIK <span class="req">*</span></label>
                  <input type="text" name="nik" class="form-control" maxlength="16" placeholder="16 digit NIK">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Tempat Lahir</label>
                  <input type="text" name="tempat_lahir" class="form-control">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Tanggal Lahir</label>
                  <input type="date" name="tgl_lahir" class="form-control">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Agama</label>
                  <select name="agama" class="form-select">
                    <option value="">Pilih</option>
                    <option>Islam</option><option>Kristen</option><option>Katolik</option>
                    <option>Hindu</option><option>Buddha</option><option>Konghucu</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Status Pernikahan</label>
                  <select name="status_nikah" class="form-select">
                    <option value="">Pilih</option>
                    <option>Belum Menikah</option>
                    <option>Menikah</option>
                    <option>Cerai Hidup</option>
                    <option>Cerai Mati</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Jumlah Anak</label>
                  <input type="number" name="jml_anak" class="form-control" min="0">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Upload Foto (via Google Drive)</label>
              <input type="file" name="foto_file" id="ptk-foto-file" class="form-control" accept="image/*">
              <p class="form-hint">Foto akan tersimpan ke Google Drive. Maks 2MB.</p>
              <input type="hidden" name="foto" id="ptk-foto-id">
              <div style="margin-top:8px">
                <img id="ptk-foto-preview" src="" alt="preview" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--border);display:none">
              </div>
            </div>
          </div>

          <!-- KEPEGAWAIAN -->
          <div class="tab-content" data-tab-content="p-kepeg">
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">NIP</label>
                  <input type="text" name="nip" class="form-control" placeholder="Kosongkan jika non-PNS">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">NUPTK</label>
                  <input type="text" name="nuptk" class="form-control" maxlength="16">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">NRG (Guru Bersertifikat)</label>
                  <input type="text" name="nrg" class="form-control">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Jenis PTK <span class="req">*</span></label>
                  <select name="jenis_ptk" class="form-select" required>
                    <option value="">Pilih</option>
                    <option>Kepala Sekolah</option>
                    <option>Guru Kelas</option>
                    <option>Guru Mapel</option>
                    <option>Guru Pendamping</option>
                    <option>Tenaga Kependidikan</option>
                    <option>Pustakawan</option>
                    <option>Penjaga Sekolah</option>
                    <option>Operator Sekolah</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Status Kepegawaian <span class="req">*</span></label>
                  <select name="status_kepeg" class="form-select" required>
                    <option value="">Pilih</option>
                    <option>PNS</option>
                    <option>PPPK</option>
                    <option>GTT/PTT</option>
                    <option>Honorer</option>
                    <option>Sukarela</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Status Aktif</label>
                  <select name="status_aktif" class="form-select">
                    <option value="Aktif">Aktif</option>
                    <option value="Non Aktif">Non Aktif</option>
                    <option value="Pensiun">Pensiun</option>
                    <option value="Mutasi">Mutasi</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">TMT Pengangkatan</label>
                  <input type="date" name="tmt_pengangkatan" class="form-control">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">TMT di Sekolah Ini</label>
                  <input type="date" name="tmt_sekolah" class="form-control">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Golongan / Pangkat</label>
                  <select name="golongan" class="form-select">
                    <option value="">-</option>
                    <option>II/a</option><option>II/b</option><option>II/c</option><option>II/d</option>
                    <option>III/a</option><option>III/b</option><option>III/c</option><option>III/d</option>
                    <option>IV/a</option><option>IV/b</option><option>IV/c</option><option>IV/d</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Sertifikasi</label>
                  <select name="sertifikasi" class="form-select">
                    <option value="Belum">Belum Sertifikasi</option>
                    <option value="Sudah">Sudah Sertifikasi</option>
                    <option value="Proses">Dalam Proses</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Tahun Sertifikasi</label>
                  <input type="text" name="tahun_sertif" class="form-control" placeholder="2020">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Gaji Pokok (Rp)</label>
                  <input type="number" name="gaji_pokok" class="form-control" min="0">
                </div>
              </div>
            </div>
          </div>

          <!-- AKADEMIK -->
          <div class="tab-content" data-tab-content="p-akademik">
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Pendidikan Terakhir</label>
                  <select name="pendidikan" class="form-select">
                    <option value="">Pilih</option>
                    <option>SMA/Sederajat</option>
                    <option>D1</option><option>D2</option><option>D3</option>
                    <option>S1</option><option>S2</option><option>S3</option>
                  </select>
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Jurusan / Prodi</label>
                  <input type="text" name="jurusan" class="form-control" placeholder="PGSD, Matematika, dll">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Nama Perguruan Tinggi</label>
                  <input type="text" name="perguruan_tinggi" class="form-control">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Tahun Lulus</label>
                  <input type="text" name="tahun_lulus" class="form-control" placeholder="2010">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">No. Ijazah</label>
                  <input type="text" name="no_ijazah" class="form-control">
                </div>
              </div>
            </div>
          </div>

          <!-- ALAMAT & KONTAK -->
          <div class="tab-content" data-tab-content="p-alamat">
            <div class="form-group">
              <label class="form-label">Alamat Lengkap</label>
              <textarea name="alamat" class="form-control" rows="3" placeholder="Jalan, RT/RW, Nomor Rumah..."></textarea>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">RT / RW</label>
                  <input type="text" name="rt_rw" class="form-control" placeholder="001/002">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Kelurahan/Desa</label>
                  <input type="text" name="kelurahan" class="form-control">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Kecamatan</label>
                  <input type="text" name="kecamatan" class="form-control">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Kabupaten/Kota</label>
                  <input type="text" name="kabupaten" class="form-control">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Provinsi</label>
                  <input type="text" name="provinsi" class="form-control">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Kode Pos</label>
                  <input type="text" name="kode_pos" class="form-control" maxlength="5">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">No. HP / WA <span class="req">*</span></label>
                  <input type="text" name="hp" class="form-control" placeholder="08xxx" required>
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" placeholder="nama@email.com">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">No. Rekening Bank</label>
                  <input type="text" name="no_rekening" class="form-control">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Nama Bank</label>
                  <input type="text" name="nama_bank" class="form-control" placeholder="BRI, BNI, Mandiri...">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">NPWP</label>
                  <input type="text" name="npwp" class="form-control">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">No. BPJS Kesehatan</label>
                  <input type="text" name="bpjs" class="form-control">
                </div>
              </div>
            </div>
          </div>

          <!-- TUGAS MENGAJAR -->
          <div class="tab-content" data-tab-content="p-tugas">
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Mata Pelajaran Diampu</label>
                  <input type="text" name="mapel_diampu" class="form-control" placeholder="Matematika, IPA, Semua Mapel...">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Rombel / Kelas Diampu</label>
                  <input type="text" name="rombel_diampu" class="form-control" placeholder="Kelas 1A, 2B, ...">
                  <p class="form-hint">Untuk Guru Kelas, isi rombel yang menjadi tanggung jawabnya</p>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Jumlah Jam Mengajar / Minggu</label>
                  <input type="number" name="jam_mengajar" class="form-control" min="0">
                </div>
              </div>
              <div class="form-col">
                <div class="form-group">
                  <label class="form-label">Tugas Tambahan</label>
                  <input type="text" name="tugas_tambahan" class="form-control" placeholder="Bendahara, Waka Kurikulum, dll">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Username Login Sistem</label>
              <input type="text" name="username_sistem" class="form-control" placeholder="Username untuk login ke SISKO">
              <p class="form-hint">Biarkan kosong jika PTK tidak perlu akses sistem</p>
            </div>
            <div class="form-group">
              <label class="form-label">Catatan</label>
              <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-light" onclick="SISKO.modal.close('modal-tambah-ptk')">Batal</button>
      <button type="submit" form="form-ptk" class="btn btn-primary" id="btn-simpan-ptk">💾 Simpan Data PTK</button>
    </div>
  </div>
</div>

<?php
$extraJs = <<<'JS'
<script>
let allPTK = [];
let ptkPage = 1;
const ptkPerPage = 15;

function openTambahPTK() {
  document.getElementById('ptk-id').value = '';
  document.getElementById('modal-ptk-title').textContent = '➕ Tambah Data PTK';
  document.getElementById('form-ptk').reset();
  document.getElementById('ptk-foto-preview').style.display = 'none';
  SISKO.modal.open('modal-tambah-ptk');
}

async function loadPTK() {
  document.getElementById('tbody-ptk').innerHTML =
    '<tr><td colspan="11" class="text-center" style="padding:30px"><div class="spinner"></div></td></tr>';
  const filters = {
    status_kepeg: document.getElementById('filter-status-ptk').value,
    jenis_ptk:    document.getElementById('filter-jenis-ptk').value,
  };
  const r = await GS.getData('DataPTK', filters);
  allPTK = r.data || [];
  const q = document.getElementById('search-ptk').value.toLowerCase();
  let filtered = allPTK;
  if (q) filtered = allPTK.filter(p =>
    (p.nama||'').toLowerCase().includes(q) ||
    (p.nip||'').toLowerCase().includes(q) ||
    (p.nuptk||'').toLowerCase().includes(q)
  );
  document.getElementById('total-ptk').textContent = filtered.length + ' orang';
  const p = SISKO.paginate(filtered, ptkPage, ptkPerPage);
  renderPTKTable(p.items, (ptkPage-1)*ptkPerPage);
  SISKO.renderPagination(document.getElementById('pagination-ptk'), p.page, p.pages,
    'function(pg){ptkPage=pg;loadPTK()}'
  );
}

function renderPTKTable(rows, offset) {
  const tbody = document.getElementById('tbody-ptk');
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="11"><div class="empty-state"><div class="es-icon">👨‍🏫</div><h4>Belum ada data PTK</h4></div></td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((p, i) => {
    const fotoUrl = p.foto ? `https://drive.google.com/thumbnail?id=${p.foto}&sz=w60` : '';
    const statusColor = p.status_kepeg==='PNS'?'badge-success':p.status_kepeg==='PPPK'?'badge-info':'badge-warning';
    return `<tr>
      <td>${offset+i+1}</td>
      <td>
        <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem">
          ${fotoUrl ? `<img src="${fotoUrl}" style="width:100%;height:100%;object-fit:cover">` : (p.nama||'?')[0].toUpperCase()}
        </div>
      </td>
      <td style="font-size:.78rem"><code>${p.nip||p.nuptk||'-'}</code></td>
      <td><div style="font-weight:600">${p.nama||'-'}</div><div style="font-size:.7rem;color:var(--text-muted)">${p.email||''}</div></td>
      <td><span class="badge ${p.jk==='L'?'badge-info':'badge-purple'}">${p.jk||'-'}</span></td>
      <td style="font-size:.8rem">${p.jenis_ptk||'-'}</td>
      <td><span class="badge ${statusColor}">${p.status_kepeg||'-'}</span></td>
      <td style="font-size:.8rem">${p.mapel_diampu||'-'}</td>
      <td><span class="badge badge-primary">${p.rombel_diampu||'-'}</span></td>
      <td style="font-size:.8rem">${p.hp||'-'}</td>
      <td>
        <div style="display:flex;gap:4px">
          <button class="btn btn-sm btn-warning" onclick="editPTK('${p.id}')">✏️</button>
          <button class="btn btn-sm btn-danger" onclick="hapusPTK('${p.id}','${p.nama}')">🗑</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function editPTK(id) {
  const p = allPTK.find(x => x.id == id);
  if (!p) return;
  document.getElementById('ptk-id').value = p.id;
  document.getElementById('modal-ptk-title').textContent = '✏️ Edit Data PTK';
  const fields = ['nama','jk','nik','tempat_lahir','tgl_lahir','agama','status_nikah','jml_anak',
    'nip','nuptk','nrg','jenis_ptk','status_kepeg','status_aktif','tmt_pengangkatan','tmt_sekolah',
    'golongan','sertifikasi','tahun_sertif','gaji_pokok',
    'pendidikan','jurusan','perguruan_tinggi','tahun_lulus','no_ijazah',
    'alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos',
    'hp','email','no_rekening','nama_bank','npwp','bpjs',
    'mapel_diampu','rombel_diampu','jam_mengajar','tugas_tambahan','username_sistem','catatan'];
  fields.forEach(f => {
    const el = document.querySelector(`#form-ptk [name="${f}"]`);
    if (el) el.value = p[f] || '';
  });
  if (p.foto) {
    document.getElementById('ptk-foto-id').value = p.foto;
    const prev = document.getElementById('ptk-foto-preview');
    prev.src = `https://drive.google.com/thumbnail?id=${p.foto}&sz=w100`;
    prev.style.display = 'block';
  }
  SISKO.modal.open('modal-tambah-ptk');
}

async function simpanPTK(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-simpan-ptk');
  SISKO.btnLoading(btn, true);

  // Handle foto upload
  const fotoFile = document.getElementById('ptk-foto-file').files[0];
  if (fotoFile) {
    const b64 = await SISKO.fileToBase64(fotoFile);
    const up = await GS.call('uploadFile', {
      fileData: b64, fileName: fotoFile.name,
      mimeType: fotoFile.type, folderId: '1QjncEdoe1hFtLjklm2Dy2eLFDRZ89_MA'
    });
    if (up.fileId) document.getElementById('ptk-foto-id').value = up.fileId;
  }

  const fd = new FormData(document.getElementById('form-ptk'));
  const data = Object.fromEntries(fd.entries());
  delete data.foto_file;
  const id = data.id; delete data.id;
  let r;
  if (id) { r = await GS.updateRow('DataPTK', id, data); }
  else { data.id = Date.now().toString(); r = await GS.addRow('DataPTK', data); }
  SISKO.btnLoading(btn, false);
  if (r.success) {
    SISKO.toast(id?'Data PTK diperbarui':'PTK berhasil ditambahkan','success');
    SISKO.modal.close('modal-tambah-ptk');
    loadPTK();
  } else SISKO.toast(r.error||'Gagal menyimpan','error');
}

function hapusPTK(id, nama) {
  SISKO.confirm(`Hapus data PTK <strong>${nama}</strong>?`, async () => {
    const r = await GS.deleteRow('DataPTK', id);
    if (r.success) { SISKO.toast('Data PTK berhasil dihapus','success'); loadPTK(); }
    else SISKO.toast(r.error||'Gagal','error');
  });
}

function exportPTK() {
  SISKO.exportCSV(allPTK, 'data_ptk_'+new Date().toISOString().slice(0,10)+'.csv');
}

// Foto preview
document.getElementById('ptk-foto-file').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const prev = document.getElementById('ptk-foto-preview');
  const reader = new FileReader();
  reader.onload = e => { prev.src = e.target.result; prev.style.display='block'; };
  reader.readAsDataURL(file);
});

let stimer;
document.getElementById('search-ptk').addEventListener('input', () => {
  clearTimeout(stimer);
  stimer = setTimeout(() => { ptkPage=1; loadPTK(); }, 300);
});

document.addEventListener('DOMContentLoaded', loadPTK);
</script>
JS;
?>
<?php include 'includes/footer.php'; ?>
