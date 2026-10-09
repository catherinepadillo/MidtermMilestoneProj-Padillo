<?php
// classes/Recipe.php

require_once __DIR__ . '/../config/Database.php';

class Recipe {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getCategories() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT r.*, u.username, c.name AS category_name 
                                    FROM recipes r
                                    JOIN users u ON r.user_id = u.id
                                    JOIN categories c ON r.category_id = c.id
                                    WHERE r.id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getIngredients($recipeId) {
        $stmt = $this->db->prepare("SELECT * FROM ingredients WHERE recipe_id = :recipe_id ORDER BY id ASC");
        $stmt->execute([':recipe_id' => $recipeId]);
        return $stmt->fetchAll();
    }

    public function getAll($keyword = '', $category_id = 0, $max_cost = 0) {
        $sql = "SELECT r.*, u.username, c.name AS category_name 
                FROM recipes r
                JOIN users u ON r.user_id = u.id
                JOIN categories c ON r.category_id = c.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (r.title LIKE :keyword OR r.description LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if ($category_id > 0) {
            $sql .= " AND r.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        if ($max_cost > 0) {
            $sql .= " AND r.estimated_cost <= :max_cost";
            $params[':max_cost'] = $max_cost;
        }

        $sql .= " ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($userId, $categoryId, $title, $description, $cookingSteps, $cookingTime, $estimatedCost, $ingredients) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("INSERT INTO recipes (user_id, category_id, title, description, cooking_steps, cooking_time_mins, estimated_cost) VALUES (:user_id, :category_id, :title, :description, :cooking_steps, :cooking_time, :estimated_cost)");
            $stmt->execute([
                ':user_id'        => $userId,
                ':category_id'    => $categoryId,
                ':title'          => $title,
                ':description'    => $description,
                ':cooking_steps'  => $cookingSteps,
                ':cooking_time'   => $cookingTime,
                ':estimated_cost' => $estimatedCost
            ]);
            $recipeId = $this->db->lastInsertId();

            $ingStmt = $this->db->prepare("INSERT INTO ingredients (recipe_id, ingredient_name) VALUES (:recipe_id, :ingredient_name)");
            foreach ($ingredients as $ing) {
                if (!empty(trim($ing))) {
                    $ingStmt->execute([':recipe_id' => $recipeId, ':ingredient_name' => trim($ing)]);
                }
            }

            $this->db->commit();
            return $recipeId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getUserFavorites($userId) {
        $sql = "SELECT r.*, u.username, c.name AS category_name 
                FROM favorites f
                JOIN recipes r ON f.recipe_id = r.id
                JOIN users u ON r.user_id = u.id
                JOIN categories c ON r.category_id = c.id
                WHERE f.user_id = :user_id
                ORDER BY f.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function isFavorited($userId, $recipeId) {
        $stmt = $this->db->prepare("SELECT 1 FROM favorites WHERE user_id = :u AND recipe_id = :r");
        $stmt->execute([':u' => $userId, ':r' => $recipeId]);
        return (bool)$stmt->fetch();
    }
}