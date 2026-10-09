<?php
require __DIR__ . '/includes/auth.php';

// Wipe the session data, then destroy the session itself
$_SESSION = [];
session_destroy();

// Back to the admin sign-in page
header('Location: login.php');
exit;