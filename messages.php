<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT m.*, u.username AS sender FROM messages m JOIN users u ON m.sender_id = u.user_id WHERE m.receiver_id = ?");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $receiver_id, $message]);

    echo "Reply sent successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <main class="messages-section">
        <header>
            <h1>Messages</h1>
        </header>
        <section class="messages-list">
            <ul>
                <?php foreach ($messages as $msg): ?>
                    <li class="message-item">
                        <div class="message-header">
                            <strong>From:</strong> <?php echo htmlspecialchars($msg['sender']); ?>
                        </div>
                        <div class="message-body">
                            <p><?php echo htmlspecialchars($msg['message']); ?></p>
                        </div>
                        <form method="POST" class="reply-form">
                            <input type="hidden" name="receiver_id" value="<?php echo $msg['sender_id']; ?>">
                            <div class="form-group">
                                <textarea name="message" required placeholder="Reply to this message"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit">Send Reply</button>
                            </div>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <footer class="navigation">
            <a href="hr_homepage.php" class="back-link">Back to Homepage</a>
        </footer>
    </main>
</body>

</html>