<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'classes/Recipe.php';

$recipeObj = new Recipe();
$favorites = $recipeObj->getUserFavorites($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Saved Favorites</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/app.js" defer></script>
</head>
<body>
<header>
    <h1>🍳 Barangay Home Cooks</h1>
    <nav>
        <a href="index.php">Feed</a>
        <a href="favorites.php">Favorites</a>
        <form method="POST" action="logout.php" style="display:inline;">
            <button type="submit">Logout</button>
        </form>
    </nav>
</header>

<div class="container">
    <h2>My Favorite Recipes</h2>
    <br>
    <?php if (empty($favorites)): ?>
        <div class="card"><p>You haven't saved any recipes yet.</p></div>
    <?php else: ?>
        <?php foreach ($favorites as $r): ?>
            <div class="card">
                <h2><a href="recipe_detail.php?id=<?= $r['id'] ?>" style="color: #b91c1c; text-decoration: none;"><?= htmlspecialchars($r['title']) ?></a></h2>
                <p class="meta">Category: <strong><?= htmlspecialchars($r['category_name']) ?></strong> | By: <strong><?= htmlspecialchars($r['username']) ?></strong></p>
                <p><?= htmlspecialchars($r['description']) ?></p>
                <br>
                <button class="fav-btn active" data-recipe-id="<?= $r['id'] ?>">❤️ Saved to Favorites</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>