<?php
require __DIR__ . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

requireAdminAccount();

$threads = $pdo->query(
    "SELECT u.id, u.full_name, u.email,
            MAX(m.id) AS latest_message_id,
            MAX(m.created_at) AS latest_message_at
     FROM users u
     LEFT JOIN chat_messages m ON m.user_id = u.id
     GROUP BY u.id, u.full_name, u.email
     ORDER BY COALESCE(MAX(m.id), 0) DESC, u.full_name ASC"
)->fetchAll();

$requestedUserId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$selectedThread = null;

if ($requestedUserId !== null && $requestedUserId !== false) {
    foreach ($threads as $thread) {
        if ((int)$thread['id'] === $requestedUserId) {
            $selectedThread = $thread;
            break;
        }
    }
} elseif ($threads) {
    $selectedThread = $threads[0];
}

$error = '';
$messageInput = '';
$selectedUserId = $selectedThread ? (int)$selectedThread['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf'] ?? null;
    $postedUserId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $postedMessage = $_POST['message'] ?? '';
    $messageInput = is_string($postedMessage) ? $postedMessage : '';

    if (!verifyCsrf(is_string($submittedToken) ? $submittedToken : null)) {
        $error = 'Your session has expired. Refresh the page and try again.';
    } elseif ($postedUserId === false || $postedUserId === null) {
        $error = 'Select a valid user conversation.';
    } else {
        foreach ($threads as $thread) {
            if ((int)$thread['id'] === $postedUserId) {
                $selectedThread = $thread;
                $selectedUserId = $postedUserId;
                break;
            }
        }

        if (!$selectedThread) {
            $error = 'That user conversation is not available.';
        } elseif (trim($messageInput) === '') {
            $error = 'Enter a message before sending.';
        } elseif (mb_strlen(trim($messageInput), 'UTF-8') > 2000) {
            $error = 'Messages must be 2,000 characters or fewer.';
        } else {
            $insert = $pdo->prepare(
                'INSERT INTO chat_messages (user_id, sender_type, message_text)
                 VALUES (?, ?, ?)'
            );
            $insert->execute([$selectedUserId, 'admin', trim($messageInput)]);
            $_SESSION['chat_flash'] = 'Your reply was sent.';

            header('Location: chat.php?user_id=' . $selectedUserId);
            exit;
        }
    }
}

$messages = [];
if ($selectedThread) {
    $messagesStmt = $pdo->prepare(
        'SELECT id, sender_type, message_text, created_at
         FROM chat_messages
         WHERE user_id = ?
         ORDER BY id ASC'
    );
    $messagesStmt->execute([$selectedUserId]);
    $messages = $messagesStmt->fetchAll();
}

$flash = $_SESSION['chat_flash'] ?? '';
unset($_SESSION['chat_flash']);

$pageTitle = 'User Messages';
$pageStyles = ['chat.css'];
$pageScripts = ['js/chat.js'];

require __DIR__ . '/includes/admin_header.php';
?>

<section class="admin-chat-page">
    <header class="chat-heading">
        <div>
            <span class="chat-eyebrow">Private support conversations</span>
            <h1>User Messages</h1>
            <p>Choose a registered user to view and reply to their private conversation.</p>
        </div>
    </header>

    <?php if ($flash !== ''): ?>
        <p class="chat-flash" role="status"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="chat-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <div class="admin-chat-layout">
        <nav class="chat-inbox" aria-label="User conversations">
            <h2>Registered users</h2>
            <?php if (!$threads): ?>
                <p class="chat-inbox-empty">No registered users found.</p>
            <?php else: ?>
                <?php foreach ($threads as $thread): ?>
                    <a class="chat-inbox-item <?= $selectedThread && (int)$selectedThread['id'] === (int)$thread['id'] ? 'active' : '' ?>"
                       href="chat.php?user_id=<?= (int)$thread['id'] ?>">
                        <span class="chat-thread-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($thread['full_name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="chat-inbox-user">
                            <strong><?= htmlspecialchars($thread['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span><?= $thread['latest_message_at'] ? htmlspecialchars(date('d M Y, H:i', strtotime($thread['latest_message_at'])), ENT_QUOTES, 'UTF-8') : 'No messages yet' ?></span>
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </nav>

        <?php if ($selectedThread): ?>
            <section class="chat-panel">
                <div class="chat-thread"
                     id="chat-thread"
                     data-poll-url="../includes/chat_poll.php"
                     data-user-id="<?= $selectedUserId ?>"
                     data-viewer-role="admin">
                    <div class="chat-thread-heading">
                        <span class="chat-thread-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($selectedThread['full_name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                        <div>
                            <strong><?= htmlspecialchars($selectedThread['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span><?= htmlspecialchars($selectedThread['email'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>

                    <div class="chat-messages" id="chat-messages" aria-live="polite" aria-label="Conversation messages">
                        <?php if (!$messages): ?>
                            <p class="chat-empty" id="chat-empty">No messages yet. Send a reply to start this conversation.</p>
                        <?php endif; ?>

                        <?php foreach ($messages as $chatMessage): ?>
                            <?php $isOwnMessage = $chatMessage['sender_type'] === 'admin'; ?>
                            <article class="chat-message <?= $isOwnMessage ? 'chat-message-own' : 'chat-message-other' ?>"
                                     data-message-id="<?= (int)$chatMessage['id'] ?>">
                                <span class="chat-message-author"><?= $isOwnMessage ? 'You' : htmlspecialchars($selectedThread['full_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                <p><?= htmlspecialchars($chatMessage['message_text'], ENT_QUOTES, 'UTF-8') ?></p>
                                <time><?= htmlspecialchars(date('d M Y, H:i', strtotime($chatMessage['created_at'])), ENT_QUOTES, 'UTF-8') ?></time>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <form class="chat-compose" method="POST" action="chat.php?user_id=<?= $selectedUserId ?>">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="user_id" value="<?= $selectedUserId ?>">
                        <label class="visually-hidden" for="chat-message">Your reply</label>
                        <textarea id="chat-message" name="message" rows="2" maxlength="2000"
                                  placeholder="Write a reply…" required><?= htmlspecialchars($messageInput, ENT_QUOTES, 'UTF-8') ?></textarea>
                        <button type="submit">Send reply</button>
                    </form>
                </div>
            </section>
        <?php else: ?>
            <section class="chat-panel chat-no-selection">
                <p>Select a user to view their conversation.</p>
            </section>
        <?php endif; ?>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/user_footer.php'; ?>
