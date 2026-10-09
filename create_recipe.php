<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'classes/Recipe.php';

$recipeObj = new Recipe();
$categories = $recipeObj->getCategories();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $cooking_time = (int)($_POST['cooking_time_mins'] ?? 0);
    $estimated_cost = (float)($_POST['estimated_cost'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $cooking_steps = trim($_POST['cooking_steps'] ?? '');
    $ingredients = array_filter($_POST['ingredients'] ?? [], function($i) { return !empty(trim($i)); });

    if (empty($title)) $errors[] = "Recipe title is required.";
    if ($category_id <= 0) $errors[] = "Select a valid category.";
    if ($cooking_time <= 0) $errors[] = "Enter valid cooking time.";
    if ($estimated_cost <= 0) $errors[] = "Enter valid estimated cost.";
    if (empty($description)) $errors[] = "Description is required.";
    if (empty($cooking_steps)) $errors[] = "Cooking steps are required.";
    if (empty($ingredients)) $errors[] = "At least one ingredient is required.";

    if (empty($errors)) {
        $created = $recipeObj->create($_SESSION['user_id'], $category_id, $title, $description, $cooking_steps, $cooking_time, $estimated_cost, $ingredients);
        if ($created) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Failed to publish recipe.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Recipe - Barangay Home Cooks</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/app.js" defer></script>
</head>
<body>
<div class="container" style="max-width: 650px;">
    <div class="card">
        <h2>Post New Recipe</h2>
        <?php if (!empty($errors)): ?>
            <div class="error-list">
                <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="create_recipe.php">
            <div class="form-group">
                <label>Recipe Title</label>
                <input type="text" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>

            <div style="display: flex; gap: 12px;">
                <div class="form-group" style="flex: 1;">
                    <label>Category</label>
                    <select name="category_id" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Cooking Time (mins)</label>
                    <input type="number" name="cooking_time_mins" required value="<?= htmlspecialchars($_POST['cooking_time_mins'] ?? '20') ?>">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Est. Cost (₱)</label>
                    <input type="number" step="0.01" name="estimated_cost" required value="<?= htmlspecialchars($_POST['estimated_cost'] ?? '100.00') ?>">
                </div>
            </div>

            <!-- Dynamic Ingredients Section -->
            <div class="form-group">
                <label>Ingredients</label>
                <div id="ingredient-list">
                    <div class="ingredient-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                        <input type="text" name="ingredients[]" placeholder="e.g., 1/2 kg Pork Belly, cubed" required>
                        <button type="button" class="btn remove-ingredient-btn" style="background-color: #ef4444; padding: 6px 12px; font-size: 0.8rem;">✕</button>
                    </div>
                </div>
                <button type="button" id="add-ingredient-btn" class="btn" style="background-color: #059669; margin-top: 6px; font-size: 0.85rem;">+ Add Ingredient</button>
            </div>

            <div class="form-group">
                <label>Short Description</label>
                <textarea name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Cooking Steps</label>
                <textarea name="cooking_steps" required><?= htmlspecialchars($_POST['cooking_steps'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn">Publish Recipe</button>
        </form>
    </div>
</div>
</body>
</html>