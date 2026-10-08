<?php
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