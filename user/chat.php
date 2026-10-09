<?php
require dirname(__DIR__) . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

requireLogin();

$userId = (int)$_SESSION['user_id'];
$userStmt = $pdo->prepare('SELECT full_name, email, phone, created_at FROM users WHERE id = ?');
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

if (!$user) {
    header('Location: ../Pages/logout.php');
    exit;
}

$error = '';
$messageInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf'] ?? null;
    $postedMessage = $_POST['message'] ?? '';
    $messageInput = is_string($postedMessage) ? $postedMessage : '';

    if (!verifyCsrf(is_string($submittedToken) ? $submittedToken : null)) {
        $error = 'Your session has expired. Refresh the page and try again.';
    } elseif (trim($messageInput) === '') {
        $error = 'Enter a message before sending.';
    } elseif (mb_strlen(trim($messageInput), 'UTF-8') > 2000) {
        $error = 'Messages must be 2,000 characters or fewer.';
    } else {
        $insert = $pdo->prepare(
            'INSERT INTO chat_messages (user_id, sender_type, message_text)
             VALUES (?, ?, ?)'
        );
        $insert->execute([$userId, 'user', trim($messageInput)]);
        $_SESSION['chat_flash'] = 'Your message was sent.';

        header('Location: chat.php');
        exit;
    }
}

$messagesStmt = $pdo->prepare(
    'SELECT id, sender_type, message_text, created_at
     FROM chat_messages
     WHERE user_id = ?
     ORDER BY id ASC'
);
$messagesStmt->execute([$userId]);
$messages = $messagesStmt->fetchAll();
$flash = $_SESSION['chat_flash'] ?? '';
unset($_SESSION['chat_flash']);

$pageTitle = 'Chat with Admin';
$pageStyles = ['chat.css'];
$pageScripts = ['js/chat.js'];

require dirname(__DIR__) . '/includes/user_header.php';
?>

<section class="chat-page">
    <header class="chat-heading">
        <div>
            <span class="chat-eyebrow">Private conversation</span>
            <h1>Chat For Help</h1>
            <p>Messages are visible only to you and the administration team.</p>
        </div>
    </header>

    <?php if ($flash !== ''): ?>
        <p class="chat-flash" role="status"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="chat-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <section class="chat-panel">
        <div class="chat-thread"
             id="chat-thread"
             data-poll-url="../includes/chat_poll.php"
             data-user-id="<?= $userId ?>"
             data-viewer-role="user">
            <div class="chat-thread-heading">
                <span class="chat-thread-avatar">A</span>
                <div>
                    <strong>Administration team</strong>
                    <span>Replies will appear here</span>
                </div>
            </div>

            <div class="chat-messages" id="chat-messages" aria-live="polite" aria-label="Conversation messages">
                <?php if (!$messages): ?>
                    <p class="chat-empty" id="chat-empty">No messages yet. Send a message to start the conversation.</p>
                <?php endif; ?>

                <?php foreach ($messages as $chatMessage): ?>
                    <?php $isOwnMessage = $chatMessage['sender_type'] === 'user'; ?>
                    <article class="chat-message <?= $isOwnMessage ? 'chat-message-own' : 'chat-message-other' ?>"
                             data-message-id="<?= (int)$chatMessage['id'] ?>">
                        <span class="chat-message-author"><?= $isOwnMessage ? 'You' : 'Admin' ?></span>
                        <p><?= htmlspecialchars($chatMessage['message_text'], ENT_QUOTES, 'UTF-8') ?></p>
                        <time><?= htmlspecialchars(date('d M Y, H:i', strtotime($chatMessage['created_at'])), ENT_QUOTES, 'UTF-8') ?></time>
                    </article>
                <?php endforeach; ?>
            </div>

            <form class="chat-compose" method="POST" action="chat.php">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <label class="visually-hidden" for="chat-message">Your message</label>
                <textarea id="chat-message" name="message" rows="2" maxlength="2000"
                          placeholder="Write a message…" required><?= htmlspecialchars($messageInput, ENT_QUOTES, 'UTF-8') ?></textarea>
                <button type="submit">Send message</button>
            </form>
        </div>
    </section>
</section>

<?php require dirname(__DIR__) . '/includes/user_footer.php'; ?>
