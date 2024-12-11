<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $created_by]);

    echo "Job post created successfully. <a href='hr_homepage.php'>Go back</a>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job Post</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <main>
        <header>
            <h1>Add a Job Post</h1>
        </header>
        <section>
            <form action="core/handleForms.php" method="POST">
                <div class="form-group">
                    <label for="job-title">Job Title</label>
                    <input type="text" id="job-title" name="title" required placeholder="Enter job title">
                </div>
                <div class="form-group">
                    <label for="job-description">Job Description</label>
                    <textarea id="job-description" name="description" required placeholder="Enter job description"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="createJobBtn">Create Job Post</button>
                </div>
            </form>
        </section>
    </main>
</body>

</html>