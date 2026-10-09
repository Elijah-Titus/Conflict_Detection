<?php

// Use at the top of admin-only pages
function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        // A farmer who types an admin address is sent back to their own dashboard
        header('Location: ../user/dashboard.php');
        exit;
    }
}

// Where each kind of user lands after login (path works from pages/, user/ and admin/)
function homeUrl(): string
{
    return isAdmin() ? '../admin/dashboard.php' : '../user/dashboard.php';
}

// Returns this session's CSRF token, creating one if needed
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

// True if the submitted token matches the session's token
function verifyCsrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $token);
}

// Start the session once, on every page that includes this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// True if someone is logged in
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

// True if the logged-in person is an admin
function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

// Use at the top of a page that needs a login
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ../pages/login.php');
        exit;
    }
}