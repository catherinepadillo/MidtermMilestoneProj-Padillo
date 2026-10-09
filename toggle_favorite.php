<?php
// toggle_favorite.php


ob_start();
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once 'config/Database.php';

$input = json_decode(file_get_contents('php://input'), true);
$recipe_id = filter_var($input['recipe_id'] ?? 0, FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if (!$recipe_id) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid recipe ID']);
    exit;
}

try {
    $db = Database::getInstance();

    // Check if already favorited
    $checkStmt = $db->prepare("SELECT id FROM favorites WHERE user_id = :u AND recipe_id = :r");
    $checkStmt->execute([':u' => $user_id, ':r' => $recipe_id]);
    $fav = $checkStmt->fetch();

    if ($fav) {
        // Remove from favorites
        $delStmt = $db->prepare("DELETE FROM favorites WHERE id = :id");
        $delStmt->execute([':id' => $fav['id']]);
        $status = 'removed';
    } else {
        // Add to favorites
        $addStmt = $db->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (:u, :r)");
        $addStmt->execute([':u' => $user_id, ':r' => $recipe_id]);
        $status = 'added';
    }

    ob_clean();
    echo json_encode(['success' => true, 'status' => $status]);
    exit;

} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}