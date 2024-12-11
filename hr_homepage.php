<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$hr_id = $_SESSION['user_id'];

$stmtJobPosts = $pdo->prepare("SELECT * FROM job_posts WHERE created_by = ?");
$stmtJobPosts->execute([$hr_id]);
$job_posts = $stmtJobPosts->fetchAll();

$stmtApplications = $pdo->prepare("
    SELECT a.*, j.title AS job_title, u.username AS applicant_name, a.status AS application_status
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    JOIN users u ON a.applicant_id = u.user_id
    WHERE j.created_by = ?
");
$stmtApplications->execute([$hr_id]);
$applications = $stmtApplications->fetchAll();

$stmtMessages = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmtMessages->execute([$hr_id]);
$messages = $stmtMessages->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $receiver_id = $_POST['receiver_id'];
    $sender_id = $_SESSION['user_id'];

    if (!empty($message) && !empty($receiver_id)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type) VALUES (?, ?, ?, 'reply')");
        if ($stmt->execute([$sender_id, $receiver_id, $message])) {
            $_SESSION['message'] = "Reply sent successfully!";
            header("Location: hr_homepage.php");
            exit();
        } else {
            $_SESSION['message'] = "Error sending reply.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Findhire Now!</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Hi, Admin! Welcome to FindHire</h1>
            <p style="text-align: center;"> You can add a new job post here:
            <p>
            <div class="add-job-btn" style="text-align: center;">
                <a href="create_job.php" class="btn">Add New Job Post</a>
            </div>
        </header>

        <section class="manage-job-posts">
            <h2>Recent Job Postings</h2>
            <?php if ($job_posts): ?>
                <ul class="job-post-list">
                    <?php foreach ($job_posts as $job): ?>
                        <li class="job-card">
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p class="job-description"><?php echo htmlspecialchars($job['description']); ?></p>
                            <div class="job-actions">
                                <a href="edit_job.php?job_id=<?php echo $job['job_id']; ?>" class="btn">Edit</a>
                                <a href="delete_job.php?job_id=<?php echo $job['job_id']; ?>" class="btn">Delete</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Currently no job posts available.</p>
            <?php endif; ?>
        </section>

        <section class="applications">
            <h2>Received Applications</h2>
            <?php if ($applications): ?>
                <ul class="application-list">
                    <?php foreach ($applications as $application): ?>
                        <li class="application-card">
                            <strong class="application-job-title"><?php echo htmlspecialchars($application['job_title']); ?></strong><br>
                            <p><strong>Applicant:</strong> <?php echo htmlspecialchars($application['applicant_name']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
                            <a href="update_status.php?application_id=<?php echo $application['application_id']; ?>" class="btn">Update Status</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>There are no applications yet.</p>
            <?php endif; ?>
        </section>

        <section class="messages">
            <h2>Inbox</h2>
            <?php if ($messages): ?>
                <ul class="message-list">
                    <?php foreach ($messages as $message): ?>
                        <li class="message-card">
                            <strong class="message-sender">From: <?php echo htmlspecialchars($message['sender_name']); ?></strong><br>
                            <p class="message-content"><?php echo htmlspecialchars($message['message']); ?></p>
                            <p class="message-timestamp"><small>Sent on: <?php echo $message['sent_at']; ?></small></p>

                            <form action="hr_homepage.php" method="POST" class="reply-form">
                                <input type="hidden" name="receiver_id" value="<?php echo $message['sender_id']; ?>">
                                <textarea name="message" rows="3" cols="50" required></textarea><br>
                                <button type="submit" class="btn">Reply</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You have no messages yet.</p>
            <?php endif; ?>
        </section>

        <footer class="dashboard-footer" style="text-align: center;">
            <p><a href="logout.php" class="logout-btn">Logout</a></p>
        </footer>
    </div>
</body>

</html>