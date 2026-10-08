<?php
require dirname(__DIR__) . '/includes/auth.php';

// Wipe the session data, then destroy the session itself
$_SESSION = [];
session_destroy();

header('Location: ../index.php');
exit;