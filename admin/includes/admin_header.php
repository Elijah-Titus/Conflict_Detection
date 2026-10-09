<?php
require_once __DIR__ . '/auth.php';
requireAdminAccount();

$pageTitle  = $pageTitle ?? 'Admin';
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
    <title><?= htmlspecialchars($pageTitle) ?> | Admin</title>

    <link rel="stylesheet" href="<?= $base ?>css/base.css">
    <link rel="stylesheet" href="<?= $base ?>css/sidebar.css">
    <link rel="stylesheet" href="<?= $base ?>css/dashboard.css">
    <link rel="stylesheet" href="<?= $base ?>admin/css/admin.css">

    <?php foreach ($pageStyles as $style): ?>
        <link rel="stylesheet" href="<?= $base ?>css/<?= htmlspecialchars($style, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>

    <?= $extraHead ?>
</head>
<body class="user-body admin-body">

<header class="user-topbar">
    <span class="user-brand">Conflict Warning System</span>
    <div class="user-topbar-right">
<span class="user-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($_SESSION['admin_name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
<span class="user-name"><?= htmlspecialchars($_SESSION['admin_name'], ENT_QUOTES, 'UTF-8') ?></span>
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="user-layout">

    <aside class="sidebar">
        <nav>
            <p class="sidebar-label">Admin</p>
            <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span> Overview
            </a>
            <a href="reports.php" class="<?= $currentPage === 'reports.php' ? 'active' : '' ?>">
                <span class="nav-icon">📋</span> All Reports
            </a>
            <a href="chat.php" class="<?= $currentPage === 'chat.php' ? 'active' : '' ?>">
                <span class="nav-icon">💬</span> Messages
            </a>

            <a href="map.php" class="<?= $currentPage === 'map.php' ? 'active' : '' ?>">
                <span class="nav-icon">🗺️</span> Live Map
            </a>
        </nav>
    </aside>

    <main class="user-main">