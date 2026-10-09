<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'classes/Comment.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$comment_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$commentObj = new Comment();
$comment = $commentObj->getById($comment_id);

if (!$comment || $comment['user_id'] != $_SESSION['user_id']) { header('Location: index.php'); exit; }

if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $text = trim($_POST['comment_text'] ?? '');
    if (!empty($text)) {
        $commentObj->update($comment_id, $_SESSION['user_id'], $text);
        header("Location: recipe_detail.php?id=" . $comment['recipe_id']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="container" style="max-width: 500px;">
    <div class="card">
        <h2>Edit Comment</h2>
        <form method="POST" action="edit_comment.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $comment['id'] ?>">
            <div class="form-group">
                <textarea name="comment_text" rows="3" required><?= htmlspecialchars($comment['comment_text']) ?></textarea>
            </div>
            <button type="submit" class="btn">Update Comment</button>
            <a href="recipe_detail.php?id=<?= $comment['recipe_id'] ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>