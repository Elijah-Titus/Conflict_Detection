<?php
require dirname(__DIR__) . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

$pageTitle  = 'Create Account';
$pageStyles = ['auth.css'];

// A logged-in user has no reason to register again
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['full_name' => '', 'email' => '', 'phone' => ''];   // refill the form after an error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $old = ['full_name' => $name, 'email' => $email, 'phone' => $phone];

    // Remove spaces and dashes so "0803 123 4567" and "0803-123-4567" both work
    $phoneClean = preg_replace('/[\s\-]/', '', $phone);

    // ---------- Validation ----------
    if ($name === '' || strlen($name) > 120) {
        $errors[] = 'Please enter your full name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^\+?[0-9]{10,15}$/', $phoneClean)) {
        $errors[] = 'Please enter a valid phone number (10 to 15 digits).';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'The two passwords do not match.';
    }

    // ---------- Save ----------
    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (full_name, email, phone, password_hash)
                 VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $name,
                $email,
                $phoneClean,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            // 23000 = duplicate value, so the email is already registered
            if ($e->getCode() === '23000') {
                $errors[] = 'An account with this email already exists.';
            } else {
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }
}

require dirname(__DIR__) . '/includes/header.php';
?>

<main class="auth-container">
    <div class="auth-card">
        <h2>Create a farmer account</h2>
        <p class="auth-intro">Log in when you report, so mediators can contact you about your case.</p>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="full_name">Full name</label>
            <input type="text" name="full_name" id="full_name"
                   value="<?= htmlspecialchars($old['full_name']) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email"
                   value="<?= htmlspecialchars($old['email']) ?>" required>

            <label for="phone">Phone number</label>
            <input type="tel" name="phone" id="phone"
                   value="<?= htmlspecialchars($old['phone']) ?>" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" minlength="8" required>

            <label for="confirm_password">Confirm password</label>
            <input type="password" name="confirm_password" id="confirm_password" minlength="8" required>

            <p class="auth-note">
                Your name, email and phone number are visible only to system
                administrators. They are never shown on the public map.
            </p>

            <button type="submit">Create account</button>
        </form>

        <p class="auth-footer">Already registered? <a href="login.php">Log in</a></p>
    </div>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>