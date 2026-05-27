<?php
require_once 'config.php';
requireLogin();
$pageTitle    = 'Dashboard';
$pageSubtitle = 'Ringkasan Informasi Sekolah';

// Get stats from GSheet or local
$stats = ['siswa' => 0, 'ptk' => 0, 'rombel' => 0, 'soal' => 0];
$settings = getSettings();
if (!empty($settings['gs_url'])) {
    $r = gsRequest('getStats', []);
    if (!isset($r['error'])) $stats = array_merge($stats, $r);
}

include 'includes/header.php';
?>

<!-- STAT CARDS -->
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon blue">👨‍🎓</div>
    <div>
      <div class="stat-val" id="stat-siswa"><?= $stats['siswa'] ?></div>
      <div class="stat-label">Total Siswa</div>
      <div class="stat-trend up">📈 Aktif</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple">👨‍🏫</div>
    <div>
      <div class="stat-val" id="stat-ptk"><?= $stats['ptk'] ?></div>
      <div class="stat-label">Pendidik & Tenaga Kependidikan</div>
      <div class="stat-trend up">📈 Aktif</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">🏫</div>
    <div>
      <div class="stat-val" id="stat-rombel"><?= $stats['rombel'] ?></div>
      <div class="stat-label">Rombongan Belajar</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange">📝</div>
    <div>
      <div class="stat-val" id="stat-soal"><?= $stats['soal'] ?></div>
      <div class="stat-label">Bank Soal</div>
      <div class="stat-trend up">📈 Total</div>
    </div>
  </div>
</div>

<!-- QUICK ACCESS -->
<div class="section-header">
  <div>
    <h2><span class="sh-icon">⚡</span> Akses Cepat</h2>
    <p>Menu yang sering digunakan</p>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:28px">
  <?php
  $quick = [
    ['href'=>'siswa.php',     'icon'=>'👨‍🎓', 'label'=>'Data Siswa',       'color'=>'blue'],
    ['href'=>'ptk.php',       'icon'=>'👨‍🏫', 'label'=>'Data PTK',         'color'=>'purple', 'admin'=>true],
    ['href'=>'jadwal.php',    'icon'=>'📅', 'label'=>'Jadwal',            'color'=>'cyan'],
    ['href'=>'bank_soal.php', 'icon'=>'📚', 'label'=>'Bank Soal',        'color'=>'orange'],
    ['href'=>'buat_soal.php', 'icon'=>'✍️',  'label'=>'Buat Soal Ujian',  'color'=>'green'],
    ['href'=>'nilai.php',     'icon'=>'📋', 'label'=>'Daftar Nilai',     'color'=>'pink'],
    ['href'=>'kop.php',       'icon'=>'🏫', 'label'=>'Profil Sekolah',   'color'=>'blue', 'admin'=>true],
    ['href'=>'settings.php',  'icon'=>'⚙️',  'label'=>'Pengaturan',       'color'=>'purple', 'admin'=>true],
  ];
  foreach ($quick as $q):
    if (!empty($q['admin']) && !isAdmin()) continue;
    $colors = ['blue'=>'#2563eb','purple'=>'#7c3aed','cyan'=>'#0891b2','orange'=>'#ea580c','green'=>'#059669','pink'=>'#db2777'];
    $c = $colors[$q['color']] ?? '#2563eb';
  ?>
  <a href="<?= $q['href'] ?>" style="
    background:#fff;border-radius:12px;padding:20px 16px;text-align:center;
    border:1px solid var(--border);box-shadow:var(--shadow-sm);
    transition:all .2s;display:block;text-decoration:none;color:var(--text);"
    onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='var(--shadow)';this.style.borderColor='<?= $c ?>'"
    onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)'">
    <div style="font-size:1.8rem;margin-bottom:8px"><?= $q['icon'] ?></div>
    <div style="font-size:.78rem;font-weight:600;color:var(--text)"><?= $q['label'] ?></div>
  </a>
  <?php endforeach; ?>
</div>

<!-- BOTTOM ROW: Info + GSheet Status -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;flex-wrap:wrap">
  <!-- School Info -->
  <div class="card">
    <div class="card-header">
      <h3>🏫 Informasi Sekolah</h3>
    </div>
    <div class="card-body" style="padding:16px 20px">
      <?php
      $s = getSettings();
      $rows = [
        'Nama Sekolah'   => $s['school_name'] ?? '-',
        'NPSN'           => $s['npsn'] ?? '-',
        'NSS'            => $s['nss']  ?? '-',
        'Akreditasi'     => $s['akreditasi'] ?? '-',
        'Kepala Sekolah' => $s['kepsek'] ?? '-',
        'Alamat'         => $s['alamat'] ?? '-',
        'Email'          => $s['email'] ?? '-',
      ];
      foreach ($rows as $k => $v):
      ?>
      <div style="display:flex;gap:10px;padding:7px 0;border-bottom:1px solid var(--border);font-size:.82rem">
        <span style="color:var(--text-muted);width:130px;flex-shrink:0"><?= $k ?></span>
        <span style="font-weight:500"><?= htmlspecialchars($v) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- GSheet Status -->
  <div class="card">
    <div class="card-header">
      <h3>🔗 Status Koneksi</h3>
      <?php if (isAdmin()): ?>
      <a href="settings.php" class="btn btn-sm btn-outline-primary">⚙️ Atur</a>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <?php
      $gsUrl = $settings['gs_url'] ?? '';
      $connected = !empty($gsUrl);
      if ($connected) {
          $ping = gsRequest('ping', []);
          $connected = !isset($ping['error']);
      }
      ?>
      <div style="text-align:center;padding:20px">
        <div style="font-size:3rem;margin-bottom:12px"><?= $connected ? '✅' : '⚠️' ?></div>
        <div style="font-weight:700;font-size:1rem;margin-bottom:8px">
          Google Sheets <?= $connected ? '<span style="color:var(--success)">Terhubung</span>' : '<span style="color:var(--warning)">Belum Terhubung</span>' ?>
        </div>
        <p style="font-size:.78rem;color:var(--text-muted);margin-bottom:16px">
          <?= $connected ? 'Data tersinkronisasi ke Google Sheets' : 'Konfigurasi Google Apps Script URL di Pengaturan untuk sinkronisasi data' ?>
        </p>
        <?php if (!$connected && isAdmin()): ?>
        <a href="settings.php" class="btn btn-primary btn-sm">⚙️ Konfigurasi Sekarang</a>
        <?php endif; ?>
      </div>

      <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:8px">
        <p style="font-size:.72rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">Status Penyimpanan</p>
        <?php
        $storages = [
          ['Google Sheet - Siswa',   $connected],
          ['Google Sheet - PTK',     $connected],
          ['Google Sheet - Soal',    $connected],
          ['Google Drive - Foto',    $connected],
          ['Local Backup',           true],
        ];
        foreach ($storages as [$name, $ok]):
        ?>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;font-size:.78rem">
          <span><?= $name ?></span>
          <span class="badge <?= $ok ? 'badge-success' : 'badge-warning' ?>"><?= $ok ? '✅ OK' : '⚠️ Offline' ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
