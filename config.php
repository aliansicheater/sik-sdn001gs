<?php
// ============================================================
// SISKO - Sistem Informasi Sekolah
// SD Negeri 001 Gunung Sari
// ============================================================

session_start();

// Google Sheets Configuration
define('GSHEET_API_URL', ''); // Diisi dari pengaturan admin
define('GDRIVE_FOLDER_ID', '1QjncEdoe1hFtLjklm2Dy2eLFDRZ89_MA');

// Google Sheets Tab Names
define('SHEET_SISWA',   'DataSiswa');
define('SHEET_PTK',     'DataPTK');
define('SHEET_SOAL',    'BankSoal');
define('SHEET_NILAI',   'DaftarNilai');
define('SHEET_USERS',   'Users');
define('SHEET_ROMBEL',  'Rombel');
define('SHEET_JADWAL',  'JadwalPelajaran');
define('SHEET_SETTINGS','Settings');

// App Info
define('APP_NAME', 'SISKO');
define('APP_FULL_NAME', 'Sistem Informasi Sekolah');
define('APP_VERSION', '1.0.0');

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_GURU',  'guru');

// Helper: cek login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN;
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php?error=unauthorized');
        exit;
    }
}

function currentUser() {
    return [
        'id'       => $_SESSION['user_id']   ?? '',
        'nama'     => $_SESSION['nama']       ?? '',
        'role'     => $_SESSION['role']       ?? '',
        'email'    => $_SESSION['email']      ?? '',
        'foto'     => $_SESSION['foto']       ?? '',
        'rombel'   => $_SESSION['rombel']     ?? '',
        'nip'      => $_SESSION['nip']        ?? '',
    ];
}

// Load settings (Google Sheet URL dll) dari file lokal atau environment variables
function getSettings() {
    $file = __DIR__ . '/data/settings.json';
    $settings = [];
    if (file_exists($file)) {
        $settings = json_decode(file_get_contents($file), true) ?? [];
    }
    
    // Override dengan Environment Variable Vercel
    if (getenv('GS_URL')) {
        $settings['gs_url'] = getenv('GS_URL');
    }
    return $settings;
}

function saveSettings($data) {
    $dir = __DIR__ . '/data';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($dir . '/settings.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// ============================================================
// Google Apps Script Web App API
// ============================================================
function gsRequest($action, $payload = []) {
    $settings = getSettings();
    $url = $settings['gs_url'] ?? '';
    if (empty($url)) return ['error' => 'Google Script URL belum dikonfigurasi'];

    $payload['action'] = $action;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 30,
    ]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) return ['error' => $err];
    $decoded = json_decode($response, true);
    return $decoded ?? ['error' => 'Invalid response', 'raw' => $response];
}

// ============================================================
// Local User Store (fallback jika GSheet belum tersambung)
// ============================================================
function getLocalUsers() {
    $file = __DIR__ . '/data/users.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?? [];
    }
    // Default admin
    return [
        [
            'id'       => '1',
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'nama'     => 'Administrator',
            'role'     => 'admin',
            'email'    => 'admin@sekolah.sch.id',
            'foto'     => '',
            'nip'      => '',
            'rombel'   => '',
            'aktif'    => '1',
        ]
    ];
}

function saveLocalUsers($users) {
    $dir = __DIR__ . '/data';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($dir . '/users.json', json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function findUser($username) {
    // Coba dari GSheet dulu
    $settings = getSettings();
    if (!empty($settings['gs_url'])) {
        $res = gsRequest('getUser', ['username' => $username]);
        if (!isset($res['error']) && !empty($res['data'])) return $res['data'];
    }
    // Fallback local
    foreach (getLocalUsers() as $u) {
        if ($u['username'] === $username) return $u;
    }
    return null;
}

// ============================================================
// Upload Helper (Google Drive via GAS)
// ============================================================
function uploadToGDrive($fileBase64, $fileName, $mimeType, $folderId = '') {
    if (empty($folderId)) $folderId = GDRIVE_FOLDER_ID;
    $res = gsRequest('uploadFile', [
        'fileData'  => $fileBase64,
        'fileName'  => $fileName,
        'mimeType'  => $mimeType,
        'folderId'  => $folderId,
    ]);
    return $res;
}
