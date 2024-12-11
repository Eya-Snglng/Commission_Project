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

$job = getJobPostByID($pdo, $job_id);

if (!$job) {
    echo "Job not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE job_posts SET title = ?, description = ? WHERE job_id = ?");
    $updated = $stmt->execute([$title, $description, $job_id]);

    if ($updated) {
        echo "Job post updated successfully. <a href='hr_homepage.php'>Go back to HR Homepage</a>";
        exit();
    } else {
        echo "Error updating job post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Post</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <main>
        <header>
            <h1>Edit Job Post</h1>
        </header>
        <section>
            <form action="core/handleForms.php" method="POST">
                <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                <div class="form-group">
                    <label for="job-title">Job Title</label>
                    <input type="text" id="job-title" name="title" value="<?php echo $job['title']; ?>" required placeholder="Enter job title">
                </div>
                <div class="form-group">
                    <label for="job-description">Job Description</label>
                    <textarea id="job-description" name="description" required placeholder="Enter job description"><?php echo $job['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="editJobBtn">Update Job</button>
                </div>
            </form>
            <div class="form-actions">
                <a href="hr_homepage.php" class="cancel-link">Cancel</a>
            </div>
        </section>
    </main>
</body>

</html>