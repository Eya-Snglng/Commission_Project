<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    echo "Job ID is missing.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE job_id = ?");
    $deleted = $stmt->execute([$job_id]);

    if ($deleted) {
        echo "Job post deleted successfully. <a href='hr_homepage.php'>Go back to HR Homepage</a>";
        exit();
    } else {
        echo "Error deleting job post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Job Post</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <main class="delete-job-container">
        <header class="delete-job-header">
            <h1>Delete Job Post</h1>
        </header>
        <section class="delete-job-content">
            <p class="delete-confirmation-message">Are you sure you want to delete this job post?</p>
            <form action="core/handleForms.php" method="POST" class="delete-job-form">
                <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                <div class="form-actions">
                    <button type="submit" name="deleteJobBtn" class="delete-btn">Delete Job</button>
                    <a href="hr_homepage.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>

</html>