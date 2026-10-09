<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';

function adminIsAuthenticated(): bool
{
    return isset($_SESSION['admin_id']);
}

function requireAdminAccount(): void
{
    if (adminIsAuthenticated()) {
        return;
    }

    header('Location: ' . (isLoggedIn() ? '../user/dashboard.php' : 'login.php'));
    exit;
}