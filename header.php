<?php
if (!defined('APP_NAME')) { require_once dirname(__DIR__) . '/config.php'; }
requireLogin();
$user = currentUser();
$settings = getSettings();
$schoolName  = $settings['school_name']  ?? 'SD Negeri 001 Gunung Sari';
$schoolShort = $settings['school_short'] ?? 'SDN 001 GUNUNG SARI';
$instansi     = $settings['instansi']    ?? 'Pemerintah Kabupaten Berau';
$dinasName   = $settings['dinas']        ?? 'Dinas Pendidikan';
$logoKiri    = $settings['logo_kiri']    ?? '';
$logoKanan   = $settings['logo_kanan']   ?? '';

$initials = strtoupper(substr($user['nama'] ?? 'U', 0, 1));
$fotoUrl  = '';
if (!empty($user['foto'])) {
    $fotoUrl = 'https://drive.google.com/thumbnail?id=' . $user['foto'] . '&sz=w100';
}

// Active page
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$menuItems = [
    ['href' => 'index.php',     'icon' => '📊', 'label' => 'Dashboard',          'group' => 'main'],
    ['href' => 'siswa.php',     'icon' => '👨‍🎓', 'label' => 'Data Siswa',          'group' => 'akademik'],
    ['href' => 'ptk.php',       'icon' => '👨‍🏫', 'label' => 'Data PTK',            'group' => 'akademik', 'admin' => true],
    ['href' => 'jadwal.php',    'icon' => '📅', 'label' => 'Jadwal Pelajaran',    'group' => 'akademik'],
    ['href' => 'bank_soal.php', 'icon' => '📝', 'label' => 'Bank Soal',           'group' => 'akademik'],
    ['href' => 'buat_soal.php', 'icon' => '✍️',  'label' => 'Buat Soal Ujian',    'group' => 'akademik'],
    ['href' => 'nilai.php',     'icon' => '📋', 'label' => 'Daftar Nilai',        'group' => 'akademik'],
    ['href' => 'kop.php',       'icon' => '🏫', 'label' => 'Profil Sekolah',      'group' => 'pengaturan', 'admin' => true],
    ['href' => 'users.php',     'icon' => '👥', 'label' => 'Manajemen User',      'group' => 'pengaturan', 'admin' => true],
    ['href' => 'settings.php',  'icon' => '⚙️',  'label' => 'Pengaturan',          'group' => 'pengaturan', 'admin' => true],
    ['href' => 'profil.php',    'icon' => '👤', 'label' => 'Profil Saya',         'group' => 'akun'],
];

$groups = ['main' => 'Menu Utama', 'akademik' => 'Akademik', 'pengaturan' => 'Pengaturan', 'akun' => 'Akun'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Dashboard' ?> — <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <?php if (isset($extraCss)) echo $extraCss; ?>
</head>
<body>
<div id="toast-container"></div>
<div class="app-layout">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon">🏫</div>
      <div class="brand-text">
        <h1><?= APP_NAME ?></h1>
        <p><?= APP_FULL_NAME ?></p>
      </div>
    </div>
    <nav class="sidebar-menu">
      <?php
      $prevGroup = null;
      foreach ($menuItems as $item):
        if (!empty($item['admin']) && !isAdmin()) continue;
        if ($item['group'] !== $prevGroup):
          if ($prevGroup !== null) echo '</ul></div>';
          echo '<div class="menu-group"><div class="menu-group-label">' . ($groups[$item['group']] ?? '') . '</div><ul>';
          $prevGroup = $item['group'];
        endif;
        $active = (basename($item['href'], '.php') === $currentPage) ? 'active' : '';
      ?>
      <li>
        <a href="<?= $item['href'] ?>" class="menu-item <?= $active ?>">
          <span class="menu-icon"><?= $item['icon'] ?></span>
          <?= $item['label'] ?>
        </a>
      </li>
      <?php endforeach; ?>
      <?php if ($prevGroup) echo '</ul></div>'; ?>
    </nav>
    <div class="sidebar-footer">
      <a href="logout.php" class="sidebar-user" onclick="return confirm('Yakin ingin keluar?')">
        <div class="avatar">
          <?php if ($fotoUrl): ?>
            <img src="<?= $fotoUrl ?>" alt="foto">
          <?php else: ?>
            <?= $initials ?>
          <?php endif; ?>
        </div>
        <div class="user-info">
          <div class="uname"><?= htmlspecialchars($user['nama']) ?></div>
          <div class="urole"><?= $user['role'] === 'admin' ? '🛡 Admin' : '👩‍🏫 Guru' ?></div>
        </div>
        <span class="logout-btn">🚪</span>
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main-content">
    <!-- TOPBAR -->
    <header class="topbar">
      <button class="topbar-menu-btn" id="menu-toggle">☰</button>
      <div class="topbar-title">
        <?= $pageTitle ?? 'Dashboard' ?>
        <small><?= $pageSubtitle ?? $schoolShort ?></small>
      </div>
      <div class="topbar-actions">
        <a href="profil.php" class="topbar-profile">
          <div class="tp-avatar">
            <?php if ($fotoUrl): ?>
              <img src="<?= $fotoUrl ?>" alt="foto">
            <?php else: ?>
              <?= $initials ?>
            <?php endif; ?>
          </div>
        </a>
      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
