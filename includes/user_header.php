<?php
require_once __DIR__ . '/auth.php';
requireLogin();   // every page in the farmer area needs a login

$pageTitle  = $pageTitle ?? 'My Dashboard';
$pageStyles = $pageStyles ?? [];
$extraHead  = $extraHead ?? '';

$base = '../';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="stylesheet" href="<?= $base ?>css/base.css">
    <link rel="stylesheet" href="<?= $base ?>css/sidebar.css">

    <?php foreach ($pageStyles as $style): ?>
        <link rel="stylesheet" href="<?= $base ?>css/<?= htmlspecialchars($style, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>

    <?= $extraHead ?>
</head>
<body class="user-body">

<header class="user-topbar">
    <span class="user-brand">Conflict Warning System</span>
<div class="user-topbar-right">
    <span class="user-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($_SESSION['name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
    <span class="user-name"><?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></span>
    <a href="../Pages/logout.php">Logout</a>
</div>
</header>

<div class="user-layout">

    <aside class="sidebar">
<nav>
    <p class="sidebar-label">Menu</p>
    <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
        <span class="nav-icon">📊</span> My Dashboard
    </a>
    <a href="report.php" class="<?= $currentPage === 'report.php' || $currentPage === 'submit_report.php' ? 'active' : '' ?>">
        <span class="nav-icon">📝</span> Report
    </a>
    <a href="chat.php" class="<?= $currentPage === 'chat.php' ? 'active' : '' ?>">
        <span class="nav-icon">💬</span> Chat
    </a>
    <a href="map.php" class="<?= $currentPage === 'map.php' ? 'active' : '' ?>">
        <span class="nav-icon">🗺️</span> Live Map
    </a>
</nav>

<?php if (isset($user) && is_array($user)): ?>
    <details class="sidebar-account">
        <summary>
            <span class="nav-icon">👤</span> My Account
        </summary>
        <div class="sidebar-account-panel">
            <div class="profile-head">
                <span class="profile-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($user['full_name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                <div>
                    <strong><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <span>Farmer account</span>
                </div>
            </div>

            <dl class="dash-profile">
                <dt>Email</dt>
                <dd><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Phone</dt>
                <dd><?= htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Member since</dt>
                <dd><?= htmlspecialchars(date('d M Y', strtotime($user['created_at'])), ENT_QUOTES, 'UTF-8') ?></dd>
            </dl>
        </div>
    </details>
<?php endif; ?>
    </aside>

    <main class="user-main">