<?php
// classes/User.php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function register($username, $email, $password) {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            return "Username or Email is already taken.";
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        return $stmt->execute([$username, $email, $hashed]);
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}