<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT a.*, j.title, u.username AS applicant_name 
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    JOIN users u ON a.applicant_id = u.user_id
");

$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->
</head>

<body>
    <div class="container">
        <header>
            <h1>Applications</h1>
        </header>

        <section class="applications">
            <h2>Application List</h2>
            <?php if ($applications): ?>
                <ul>
                    <?php foreach ($applications as $app): ?>
                        <li class="application-card">
                            <strong>Applicant:</strong> <?php echo htmlspecialchars($app['applicant_name']); ?><br>
                            <strong>Job Title:</strong> <?php echo htmlspecialchars($app['title']); ?><br>
                            <strong>Status:</strong> <?php echo htmlspecialchars($app['status']); ?><br>
                            <a class="status-btn" href="update_status.php?application_id=<?php echo $app['application_id']; ?>&status=Accepted">Accept</a> |
                            <a class="status-btn" href="update_status.php?application_id=<?php echo $app['application_id']; ?>&status=Rejected">Reject</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No applications available.</p>
            <?php endif; ?>
        </section>

        <section class="actions">
            <p><a href="hr_homepage.php" class="btn">Back to Homepage</a></p>
        </section>
    </div>
</body>

</html>