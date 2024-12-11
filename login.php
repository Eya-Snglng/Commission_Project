<?php
session_start();
require 'core/dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $username;

        if ($user['role'] === 'HR') {
            header("Location: hr_homepage.php");
        } else {
            header("Location: applicant_homepage.php");
        }
        exit();
    } else {
        $_SESSION['message'] = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>

    <header class="login-header">
        <h1>Login</h1>
    </header>
    <section class="login-message">
        <?php if (isset($_SESSION['message'])): ?>
            <p class="error-message"><?php echo $_SESSION['message'];
                                        unset($_SESSION['message']); ?></p>
        <?php endif; ?>
    </section>
    <section class="login-form">
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-login">Login</button>
            </div>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="register.php" class="hollow">Register here</a></p>
        </div>
    </section>
    </main>
</body>

</html>