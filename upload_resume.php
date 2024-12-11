<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
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
    $applicant_id = $_SESSION['user_id'];
    $resume = $_FILES['resume'];

    if (!empty($resume['name'])) {
        $resumePath = 'uploads/' . basename($resume['name']);

        if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, resume) VALUES (?, ?, ?)");
            $stmt->execute([$job_id, $applicant_id, $resumePath]);

            echo "Application submitted successfully! <a href='applicant_homepage.php'>Go back to Homepage</a>";
        } else {
            echo "Error uploading resume.";
        }
    } else {
        echo "Please upload a resume.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Here</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->
</head>

<body>
    <div class="container">
        <header>
            <h1>Apply Here: <?php echo htmlspecialchars($job['title']); ?></h1>
        </header>

        <form action="upload_resume.php?job_id=<?php echo $job_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="resume">Upload Resume:</label>
                <input type="file" name="resume" id="resume" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Apply</button>
                <a href="applicant_homepage.php" class="btn cancel-btn">Cancel and go back to Homepage</a>
            </div>
        </form>
        </section>
    </div>
</body>

</html>