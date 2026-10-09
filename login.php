<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'classes/User.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email address.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {
        $userObj = new User();
        $user = $userObj->login($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Invalid email or password credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Home Cooks - Login</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="container" style="max-width: 450px;">
    <div class="card">
        <h2>Maligayang Pagbabalik!</h2>
        <p class="meta">Log in to view recipes.</p>
        <?php if (isset($_GET['registered'])): ?>
            <p style="color: green; margin-bottom: 10px;">Registration successful! Please log in.</p>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="error-list">
                <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Log In</button>
        </form>
        <p style="margin-top: 15px;">Wala pang account? <a href="register.php">Mag-register dito</a></p>
    </div>
</div>
</body>
</html>