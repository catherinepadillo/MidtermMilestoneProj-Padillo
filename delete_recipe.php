<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'classes/Recipe.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($recipe_id) {
        $recipeObj = new Recipe();
        $recipeObj->delete($recipe_id, $_SESSION['user_id']);
    }
}
header('Location: index.php');
exit;