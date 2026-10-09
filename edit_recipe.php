<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'classes/Recipe.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$recipe_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$recipeObj = new Recipe();
$recipe = $recipeObj->getById($recipe_id);

if (!$recipe || $recipe['user_id'] != $_SESSION['user_id']) { header('Location: index.php'); exit; }

$categories = $recipeObj->getCategories();
$ingredients = $recipeObj->getIngredients($recipe_id);
$errors = [];

if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $title = trim($_POST['title'] ?? '');
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $description = trim($_POST['description'] ?? '');
    $cooking_steps = trim($_POST['cooking_steps'] ?? '');
    $time_mins = filter_input(INPUT_POST, 'cooking_time_mins', FILTER_VALIDATE_INT) ?? 15;
    $ing_list = $_POST['ingredients'] ?? [];

    if (empty($title)) $errors[] = "Title required.";
    if (empty(array_filter($ing_list))) $errors[] = "At least one ingredient required.";

    if (empty($errors)) {
        $recipeObj->update($recipe_id, $_SESSION['user_id'], $category_id, $title, $description, $cooking_steps, $time_mins, $ing_list);
        header("Location: recipe_detail.php?id=$recipe_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/app.js" defer></script>
</head>
<body>
<div class="container" style="max-width: 650px;">
    <div class="card">
        <h2>Edit Recipe</h2>
        <form method="POST" action="edit_recipe.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $recipe['id'] ?>">

            <div class="form-group">
                <label>Recipe Title</label>
                <input type="text" name="title" required value="<?= htmlspecialchars($recipe['title']) ?>">
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $recipe['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Est. Cooking Time (Minutes)</label>
                <input type="number" name="cooking_time_mins" value="<?= $recipe['cooking_time_mins'] ?>" min="1" required>
            </div>
            <div class="form-group">
                <label>Short Description</label>
                <textarea name="description" rows="2" required><?= htmlspecialchars($recipe['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Ingredients</label>
                <div id="ingredients-container">
                    <?php foreach ($ingredients as $ing): ?>
                        <div class="ingredient-row">
                            <input type="text" name="ingredients[]" value="<?= htmlspecialchars($ing['ingredient_name']) ?>" required>
                            <button type="button" class="btn btn-danger remove-ing-btn">X</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br>
                <button type="button" id="add-ingredient-btn" class="btn btn-secondary">+ Add More Ingredient</button>
            </div>
            <div class="form-group">
                <label>Cooking Steps</label>
                <textarea name="cooking_steps" rows="6" required><?= htmlspecialchars($recipe['cooking_steps']) ?></textarea>
            </div>
            <button type="submit" class="btn">Update Recipe</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>