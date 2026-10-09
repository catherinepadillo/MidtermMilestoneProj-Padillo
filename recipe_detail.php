<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'classes/Recipe.php';

$recipeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$recipeId) {
    header('Location: index.php');
    exit;
}

$recipeObj = new Recipe();
$recipe = $recipeObj->getById($recipeId);

if (!$recipe) {
    die("Recipe not found.");
}

$ingredients = $recipeObj->getIngredients($recipeId);
$isFav = $recipeObj->isFavorited($_SESSION['user_id'], $recipeId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($recipe['title']) ?> - Barangay Home Cooks</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/app.js" defer></script>
</head>
<body>
<header>
    <h1>🍳 Barangay Home Cooks</h1>
    <nav>
        <a href="index.php">← Back to Feed</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <h2><?= htmlspecialchars($recipe['title']) ?></h2>
        <p class="meta">
            Category: <strong><?= htmlspecialchars($recipe['category_name']) ?></strong> | 
            Posted by: <strong><?= htmlspecialchars($recipe['username']) ?></strong> | 
            ⏱️ <?= $recipe['cooking_time_mins'] ?> mins | 
            💰 <strong>Est. Cost: ₱<?= number_format($recipe['estimated_cost'], 2) ?></strong>
        </p>

        <p style="margin-bottom: 20px; color: #475569;"><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>

        <h3>Mga Sangkap (Ingredients):</h3>
        <?php if (empty($ingredients)): ?>
            <p style="color: #94a3b8; italic;">No ingredients listed.</p>
        <?php else: ?>
            <ul style="margin: 10px 0 24px 20px;">
                <?php foreach ($ingredients as $ing): ?>
                    <li style="margin-bottom: 6px;"><?= htmlspecialchars($ing['ingredient_name']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h3>Paraan ng Pagluluto (Cooking Steps):</h3>
        <p style="white-space: pre-line; line-height: 1.8; margin-top: 10px;"><?= htmlspecialchars($recipe['cooking_steps']) ?></p>

        <br><br>
        <button class="fav-btn <?= $isFav ? 'active' : '' ?>" data-recipe-id="<?= $recipe['id'] ?>">
            <?= $isFav ? '❤️ Saved to Favorites' : '🤍 Save to Favorites' ?>
        </button>
    </div>
</div>
</body>
</html>