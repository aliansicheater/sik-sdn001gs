<?php
require_once 'config.php';
if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
$settings = getSettings();
$schoolName = $settings['school_name'] ?? 'SD Negeri 001 Gunung Sari';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $user = findUser($username);
        if ($user && password_verify($password, $user['password'] ?? '') && ($user['aktif'] ?? '1') === '1') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['email']   = $user['email'] ?? '';
            $_SESSION['foto']    = $user['foto'] ?? '';
            $_SESSION['rombel']  = $user['rombel'] ?? '';
            $_SESSION['nip']     = $user['nip'] ?? '';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — SISKO</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div id="toast-container"></div>
<div class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <div class="logo-icon">🏫</div>
      <h2>SISKO</h2>
      <p>Sistem Informasi Sekolah</p>
      <p style="margin-top:4px;font-size:.72rem;color:var(--text-light)"><?= htmlspecialchars($schoolName) ?></p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger">
      <span class="alert-icon">❌</span>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" id="login-form">
      <div class="form-group">
        <label class="form-label">Username <span class="req">*</span></label>
        <div class="input-group">
          <input type="text" name="username" class="form-control"
                 placeholder="Masukkan username"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autocomplete="username">
          <span class="ig-btn">👤</span>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Password <span class="req">*</span></label>
        <div class="input-group">
          <input type="password" name="password" id="pwd" class="form-control"
                 placeholder="Masukkan password" required autocomplete="current-password">
          <span class="ig-btn" onclick="togglePwd()" style="cursor:pointer" id="eye-btn">👁️</span>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg" id="login-btn">
        🔐 Masuk
      </button>
    </form>

    <div style="text-align:center;margin-top:20px">
      <p class="text-muted" style="font-size:.72rem">
        Default: <code>admin</code> / <code>admin123</code>
      </p>
    </div>

    <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--border);text-align:center">
      <p style="font-size:.68rem;color:var(--text-light)">
        © <?= date('Y') ?> SISKO v<?= APP_VERSION ?> — <?= htmlspecialchars($schoolName) ?>
      </p>
    </div>
  </div>
</div>

<script src="assets/js/app.js"></script>
<script>
function togglePwd() {
  const p = document.getElementById('pwd');
  const e = document.getElementById('eye-btn');
  if (p.type === 'password') { p.type = 'text'; e.textContent = '🙈'; }
  else { p.type = 'password'; e.textContent = '👁️'; }
}
document.getElementById('login-form').addEventListener('submit', function() {
  const btn = document.getElementById('login-btn');
  SISKO.btnLoading(btn, true);
});
</script>
</body>
</html>
