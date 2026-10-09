<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'classes/User.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation Rules
    if (strlen($username) < 3) $errors[] = "Username must be at least 3 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email address is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";

    if (empty($errors)) {
        $userObj = new User();
        $result = $userObj->register($username, $email, $password);
        if ($result === true) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $errors[] = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Home Cooks - Register</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="container" style="max-width: 450px;">
    <div class="card">
        <h2>Sali na sa Barangay Cooks!</h2>
        <p class="meta">Create your account to share recipes.</p>
        <?php if (!empty($errors)): ?>
            <div class="error-list">
                <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required minlength="3" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <button type="submit" class="btn">Register Account</button>
        </form>
        <p style="margin-top: 15px;">May account na? <a href="login.php">Log in dito</a></p>
    </div>
</div>
</body>
</html>