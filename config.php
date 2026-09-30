<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'capstone_system');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_check() {
    if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        die('Invalid request.');
    }
}

function flash($msg, $type = 'success') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

// $active: 'home' | 'account' | 'reports' | 'risk'
// $body_class: e.g. 'dash-bg' to show a background image behind the main area
function page_top($title, $back = null, $active = '', $body_class = '') {
    $gmail = $_SESSION['gmail'] ?? '';
    $initial = $gmail !== '' ? strtoupper(substr($gmail, 0, 1)) : '?';

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>' . e($title) . ' | AssetVault</title>
    <link rel="stylesheet" href="style.css"></head>
    <body class="app-body">
    <div class="app-shell">

      <aside class="sidebar">
        <div class="sidebar-brand">
          <img src="assets/logo.png" alt="AssetVault" class="brand-logo" onerror="this.style.display=\'none\'">
          <div>
            <div class="brand-name">ASSET<span>VAULT</span></div>
            <div class="brand-tag">Manage &middot; Track &middot; Secure</div>
          </div>
        </div>

        <button class="menu-toggle" onclick="document.querySelector(\'.app-shell\').classList.toggle(\'sidebar-open\')">&#9776;</button>

        <nav class="side-nav">
          <a class="side-link ' . ($active === 'home' ? 'active' : '') . '" href="dashboard.php">
            <span class="side-icon">🏠</span> Home
          </a>
          <a class="side-link ' . ($active === 'reports' ? 'active' : '') . '" href="reports.php">
            <span class="side-icon">📊</span> Reports
          </a>
          <a class="side-link ' . ($active === 'risk' ? 'active' : '') . '" href="risk.php">
            <span class="side-icon">⚠️</span> Risk
          </a>
          <a class="side-link ' . ($active === 'account' ? 'active' : '') . '" href="account_settings.php">
            <span class="side-icon">⚙️</span> Settings
          </a>
        </nav>

        <div class="sidebar-footer">
          <div class="sidebar-quote">Better Tracking.<br>Brighter Records.</div>
          <a class="btn btn-gold sidebar-logout" href="logout.php">Logout</a>
        </div>
      </aside>

      <div class="main-area ' . e($body_class) . '">
        <header class="topbar">
          <span class="topbar-title">' . e($title) . '</span>
          <div class="user-area">
            <span class="user-mail">' . e($gmail) . '</span>
            <div class="avatar">' . e($initial) . '</div>
          </div>
        </header>
        <main class="container">';

    if ($back) {
        echo '<a class="btn btn-ghost back" href="' . e($back) . '">&larr; Back</a>';
    }
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert alert-' . e($f['type']) . '">' . e($f['msg']) . '</div>';
        unset($_SESSION['flash']);
    }
}

function page_bottom() {
    echo '</main></div></div></body></html>';
}