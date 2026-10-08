<?php
require dirname(__DIR__) . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

$pageTitle  = 'Login';
$pageStyles = ['auth.css'];

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, full_name, password_hash, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // New session id on login stops "session fixation" attacks
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['full_name'];
        $_SESSION['role']    = $user['role'];

       header('Location: ../user/dashboard.php');
        exit;
    }

    // Same message for "no such email" and "wrong password"
    $error = 'Invalid email or password.';
}

require dirname(__DIR__) . '/includes/header.php';
?>

<main class="auth-container">
    <div class="auth-card">
        <h2>Log in</h2>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Account created. You can now log in.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Email</label>
            <input type="email" name="email" id="email"
                   value="<?= htmlspecialchars($email) ?>" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Log in</button>
        </form>

        <p class="auth-footer">No account yet? <a href="register.php">Create one</a></p>
    </div>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>