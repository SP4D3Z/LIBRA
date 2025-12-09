<?php
namespace App\Models;

use PDO;

class User {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
        $stmt->execute(['u' => $username]);
        return $stmt->fetch();
    }
    
    // Add this method for consistency
    public function findById($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch();
    }
}