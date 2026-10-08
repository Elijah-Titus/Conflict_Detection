<?php
require_once __DIR__ . '/auth.php';

// Defaults, which each page can override before including this file
$pageTitle  = $pageTitle ?? 'Conflict Warning System';
$pageStyles = $pageStyles ?? [];
$extraHead  = $extraHead ?? '';

// Path from the current page back to the project root.
// Pages in pages/ use the default '../'. The root index.php sets it to ''.
$base = $base ?? '../';

// Current file name, e.g. "map.php", used to highlight the menu link
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="stylesheet" href="<?= $base ?>css/base.css">
    <link rel="stylesheet" href="<?= $base ?>css/header.css">

    <?php foreach ($pageStyles as $style): ?>
        <link rel="stylesheet" href="<?= $base ?>css/<?= htmlspecialchars($style, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>

    <?= $extraHead ?>
</head>
<body>

<header class="site-header">
    <h1><a href="<?= $base ?>index.php">Conflict Warning System</a></h1>

<nav class="site-nav">
    <a href="<?= $base ?>index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>

    <?php if (isLoggedIn()): ?>
        <a href="<?= $base ?>user/dashboard.php">My Dashboard</a>
        <a href="<?= $base ?>pages/logout.php">Logout</a>
    <?php else: ?>
        <a href="<?= $base ?>pages/login.php" class="<?= $currentPage === 'login.php' ? 'active' : '' ?>">Login</a>
        <a href="<?= $base ?>pages/register.php" class="<?= $currentPage === 'register.php' ? 'active' : '' ?>">Register</a>
    <?php endif; ?>
</nav>
</header>