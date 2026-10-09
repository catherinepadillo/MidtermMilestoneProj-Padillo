<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'classes/Recipe.php';
require_once 'classes/Comment.php';

$recipe_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$recipe_id) { header('Location: index.php'); exit; }

$recipeObj = new Recipe();
$commentObj = new Comment();

$recipe = $recipeObj->getById($recipe_id);
if (!$recipe) { header('Location: index.php'); exit; }

$ingredients = $recipeObj->getIngredients($recipe_id);
$comments = $commentObj->getByRecipe($recipe_id);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $comment_text = trim($_POST['comment_text'] ?? '');
    if (empty($comment_text)) {
        $errors[] = "Comment text cannot be empty.";
    } else {
        $commentObj->add($recipe_id, $_SESSION['user_id'], $comment_text);
        header("Location: recipe_detail.php?id=$recipe_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['title']) ?> - Details</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/app.js" defer></script>
</head>
<body>
<header>
    <h1>🍳 Barangay Home Cooks</h1>
    <nav>
        <a href="index.php">Back to Feed</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <h2><?= htmlspecialchars($recipe['title']) ?></h2>
        <p class="meta">
            Category: <strong><?= htmlspecialchars($recipe['category_name']) ?></strong> | 
            By: <strong><?= htmlspecialchars($recipe['username']) ?></strong> | 
            Est. Time: ⏱️ <?= $recipe['cooking_time_mins'] ?> mins
            <?php if ($recipe['is_edited']): ?><span class="badge">Edited</span><?php endif; ?>
        </p>
        <p><strong>Description:</strong> <?= htmlspecialchars($recipe['description']) ?></p>
        <br>
        
        <h3>Mga Sangkap (Ingredients):</h3>
        <ul>
            <?php foreach ($ingredients as $ing): ?>
                <li><?= htmlspecialchars($ing['ingredient_name']) ?></li>
            <?php endforeach; ?>
        </ul>
        <br>

        <h3>Paraan ng Pagluluto (Steps):</h3>
        <p><?= nl2br(htmlspecialchars($recipe['cooking_steps'])) ?></p>
        <br>

        <?php if ($recipe['user_id'] == $_SESSION['user_id']): ?>
            <div style="display: flex; gap: 8px;">
                <form method="POST" action="edit_recipe.php">
                    <input type="hidden" name="id" value="<?= $recipe['id'] ?>">
                    <button type="submit" class="btn btn-secondary">Edit Recipe</button>
                </form>
                <form method="POST" action="delete_recipe.php" onsubmit="return confirm('Delete this recipe?');">
                    <input type="hidden" name="id" value="<?= $recipe['id'] ?>">
                    <button type="submit" class="btn btn-danger">Delete Recipe</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Comments</h3>
        <?php foreach ($comments as $c): ?>
            <div class="comment-box">
                <p class="meta">
                    <strong><?= htmlspecialchars($c['username']) ?></strong> on <?= $c['created_at'] ?>
                    <?php if ($c['is_edited']): ?><span class="badge">Edited</span><?php endif; ?>
                </p>
                <p><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>
                <?php if ($c['user_id'] == $_SESSION['user_id']): ?>
                    <div style="display: flex; gap: 8px; margin-top: 5px;">
                        <form method="POST" action="edit_comment.php">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit">Edit</button>
                        </form>
                        <form method="POST" action="delete_comment.php" onsubmit="return confirm('Delete comment?');">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="recipe_detail.php?id=<?= $recipe_id ?>" style="margin-top: 15px;">
            <input type="hidden" name="action" value="add_comment">
            <div class="form-group">
                <textarea name="comment_text" rows="3" placeholder="Leave feedback or cooking tips..." required></textarea>
            </div>
            <button type="submit" class="btn">Post Comment</button>
        </form>
    </div>
</div>
</body>
</html>