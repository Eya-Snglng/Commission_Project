<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: login.php");
    exit();
}

$applicant_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, j.title AS job_title, j.description AS job_description, a.status AS application_status
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    WHERE a.applicant_id = ?
");
$stmt->execute([$applicant_id]);
$applications = $stmt->fetchAll();

$stmtJobPosts = $pdo->query("SELECT * FROM job_posts");
$job_posts = $stmtJobPosts->fetchAll();

$stmtMessages = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmtMessages->execute([$applicant_id]);
$messages = $stmtMessages->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Findhire Now!</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->
</head>

<body>
    <div class="container">
        <header>
            <h1>Hi, User! Welcome to FindHire</h1>
        </header>

        <section class="job-posts">
            <h2>Find Jobs</h2>
            <?php if ($job_posts): ?>
                <ul>
                    <?php foreach ($job_posts as $job): ?>
                        <li class="job-card">
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
                            <a class="apply-btn" href="upload_resume.php?job_id=<?php echo $job['job_id']; ?>">Apply</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Currently no job posts available.</p>
            <?php endif; ?>
        </section>

        <section class="applications">
            <h2>Sent Applications</h2>
            <?php if ($applications): ?>
                <ul>
                    <?php foreach ($applications as $application): ?>
                        <li class="application-card">
                            <strong><?php echo htmlspecialchars($application['job_title']); ?></strong><br>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($application['job_description']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You have no sent applications yet.</p>
            <?php endif; ?>
        </section>

        <section class="messages">
            <h2>Inbox</h2>
            <?php if ($messages): ?>
                <ul>
                    <?php foreach ($messages as $message): ?>
                        <li class="message-card">
                            <strong>From: <?php echo htmlspecialchars($message['sender_name']); ?></strong><br>
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <p><small>Sent on: <?php echo $message['sent_at']; ?></small></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You have no messages yet.</p>
            <?php endif; ?>
        </section>

        <section class="actions">
            <p><a href="message_hr.php">
                    <button class="btn">Send Message to HR!</button>
                </a></p>

            <footer class="dashboard-footer" style="text-align: center;">
                <p><a href="logout.php" class="logout-btn">Logout</a></p>
            </footer>
        </section>
    </div>
</body>

</html>