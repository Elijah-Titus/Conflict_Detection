<?php
require __DIR__ . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

if (adminIsAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Look in the admins table, not the farmers' table
    $stmt = $pdo->prepare('SELECT id, full_name, password_hash FROM admins WHERE email = ?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);

        // Start clean, so no farmer session data is left in this browser
        $_SESSION = [];
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/admin-login.css">
</head>
<body class="admin-login-body">

<main class="admin-login-card">
    <div class="admin-login-logo">🛡️</div>
    <h1>Admin sign in</h1>
    <p class="admin-login-sub">Authorised staff only</p>

    <?php if ($error): ?>
        <div class="admin-login-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" autocomplete="username"
               value="<?= htmlspecialchars($email) ?>" required autofocus>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" autocomplete="current-password" required>

        <button type="submit">Sign in</button>
    </form>
</main>

</body>
</html>