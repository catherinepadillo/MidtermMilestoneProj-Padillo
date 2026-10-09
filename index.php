<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit; 
}

require_once 'classes/Recipe.php';

$recipeObj = new Recipe();
$categories = $recipeObj->getCategories();

$keyword = trim($_GET['keyword'] ?? '');
$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) ?? 0;

$recipes = $recipeObj->getAll($keyword, $category_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Home Cooks - Feed</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/app.js" defer></script>
</head>
<body>
<header>
    <h1>🍳 Barangay Home Cooks</h1>
    <nav>
        <span>Lutuin ni: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
        <a href="index.php">Feed</a>
        <a href="favorites.php">Favorites</a>
        <a href="create_recipe.php" class="btn">+ New Recipe</a>
        <form method="POST" action="logout.php" style="display:inline;">
            <button type="submit">Logout</button>
        </form>
    </nav>
</header>

<div class="container">
    <div class="card">
        <form method="GET" action="index.php" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="keyword" placeholder="Search dish name or ingredient..." value="<?= htmlspecialchars($keyword) ?>" style="flex: 2; padding: 8px;">
            <select name="category_id" style="flex: 1; padding: 8px;">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Filter</button>
        </form>
    </div>

    <?php if (empty($recipes)): ?>
        <div class="card"><p>No recipes found.</p></div>
    <?php else: ?>
        <?php foreach ($recipes as $r): ?>
            <?php $isFav = $recipeObj->isFavorited($_SESSION['user_id'], $r['id']); ?>
            <div class="card">
                <h2><a href="recipe_detail.php?id=<?= $r['id'] ?>" style="color: #b91c1c; text-decoration: none;"><?= htmlspecialchars($r['title']) ?></a></h2>
                <p class="meta">
                    Category: <strong><?= htmlspecialchars($r['category_name']) ?></strong> | 
                    Posted by: <strong><?= htmlspecialchars($r['username']) ?></strong> | 
                    ⏱️ <?= $r['cooking_time_mins'] ?> mins
                    <?php if ($r['is_edited']): ?><span class="badge">Edited</span><?php endif; ?>
                </p>
                <p><?= htmlspecialchars($r['description']) ?></p>
                <br>
                <button class="fav-btn <?= $isFav ? 'active' : '' ?>" data-recipe-id="<?= $r['id'] ?>">
                    <?= $isFav ? '❤️ Saved to Favorites' : '🤍 Save to Favorites' ?>
                </button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>