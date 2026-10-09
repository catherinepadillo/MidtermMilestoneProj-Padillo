<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'classes/Comment.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($comment_id) {
        $commentObj = new Comment();
        $c = $commentObj->getById($comment_id);
        if ($c) {
            $commentObj->delete($comment_id, $_SESSION['user_id']);
            header("Location: recipe_detail.php?id=" . $c['recipe_id']);
            exit;
        }
    }
}
header('Location: index.php');
exit;