<?php
// classes/Comment.php
require_once __DIR__ . '/../config/Database.php';

class Comment {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getByRecipe($recipe_id) {
        $stmt = $this->pdo->prepare('
            SELECT c.*, u.username 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.recipe_id = ? 
            ORDER BY c.created_at ASC
        ');
        $stmt->execute([$recipe_id]);
        return $stmt->fetchAll();
    }

    public function add($recipe_id, $user_id, $comment_text) {
        $stmt = $this->pdo->prepare('INSERT INTO comments (recipe_id, user_id, comment_text) VALUES (?, ?, ?)');
        return $stmt->execute([$recipe_id, $user_id, $comment_text]);
    }

    public function update($comment_id, $user_id, $comment_text) {
        $stmt = $this->pdo->prepare('UPDATE comments SET comment_text = ?, is_edited = 1 WHERE id = ? AND user_id = ?');
        return $stmt->execute([$comment_text, $comment_id, $user_id]);
    }

    public function delete($comment_id, $user_id) {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?');
        return $stmt->execute([$comment_id, $user_id]);
    }

    public function getById($comment_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM comments WHERE id = ?');
        $stmt->execute([$comment_id]);
        return $stmt->fetch();
    }
}