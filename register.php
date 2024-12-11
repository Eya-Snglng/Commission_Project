<?php
require 'core/dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if (!empty($username) && !empty($password) && !empty($role)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $password, $role])) {
                $_SESSION['message'] = "Registration successful!";
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['message'] = "Error inserting user.";
            }
        } else {
            $_SESSION['message'] = "Username already exists.";
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
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <div class="container">
        <header class="page-header">
            <h1>Register</h1>
        </header>

        <section class="registration-form">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="error-message">
                    <p><?php echo $_SESSION['message'];
                        unset($_SESSION['message']); ?></p>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required placeholder="Enter username">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                </div>

                <div class="form-group">
                    <label for="role">Role:</label>
                    <select name="role" id="role">
                        <option value="Applicant">Applicant</option>
                        <option value="HR">HR</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit">Register</button>
                </div>
            </form>
        </section>
    </div>
</body>

</html>