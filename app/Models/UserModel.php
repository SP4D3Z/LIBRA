<?php
namespace App\Models;

use PDO;

class UserModel {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function findByUsername(string $username) {
        $sql = "SELECT * FROM users WHERE username = :u LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATED: Changed to password_hash to match AuthController
  public function createUser(array $data) {
    // Check if email column exists
    $hasEmailColumn = false;
    try {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
        $hasEmailColumn = $stmt->rowCount() > 0;
    } catch (\Exception $e) {
        $hasEmailColumn = false;
    }
    
    if ($hasEmailColumn) {
        $sql = "INSERT INTO users (username, first_name, last_name, phone, address, password_hash, user_type, email)
                VALUES (:username, :first_name, :last_name, :phone, :address, :password_hash, :user_type, :email)";
        $params = [
            ':username'   => $data['username'],
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':phone'      => $data['phone'] ?? null,
            ':address'    => $data['address'] ?? null,
            ':password_hash' => $data['password'],
            ':user_type'  => $data['user_type'],
            ':email'      => null
        ];
    } else {
        $sql = "INSERT INTO users (username, first_name, last_name, phone, address, password_hash, user_type)
                VALUES (:username, :first_name, :last_name, :phone, :address, :password_hash, :user_type)";
        $params = [
            ':username'   => $data['username'],
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':phone'      => $data['phone'] ?? null,
            ':address'    => $data['address'] ?? null,
            ':password_hash' => $data['password'],
            ':user_type'  => $data['user_type']
        ];
    }
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $this->pdo->lastInsertId();
}

    public function getUsers() {
        return $this->pdo->query("SELECT user_id, username, first_name, last_name, user_type, is_active, created_at FROM users WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
    }

 public function createRole($userType, $userId, $data) {
    try {
        // Check if roles table exists
        $tableExists = $this->pdo->query("SHOW TABLES LIKE 'roles'")->rowCount() > 0;
        
        if (!$tableExists) {
            // Create roles table
            $createTableSQL = "CREATE TABLE IF NOT EXISTS roles (
                role_id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                user_type ENUM('student', 'teacher', 'staff', 'librarian') NOT NULL,
                program VARCHAR(100),
                year_level INT,
                department VARCHAR(100),
                position VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($createTableSQL);
        }
        
        $stmt = $this->pdo->prepare("INSERT INTO roles (user_id, user_type, program, year_level, department, position) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $userType,
            $data['program'] ?? null,
            $data['year_level'] ?? null,
            $data['department'] ?? null,
            $data['position'] ?? null
        ]);
    } catch (\Exception $e) {
        // Log error but don't stop registration
        error_log("Role creation failed: " . $e->getMessage());
    }
}

    public function softDeleteUser($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->rowCount();
        } catch (\Exception $e) {
            throw new \Exception("Failed to deactivate user: " . $e->getMessage());
        }
    }
    
    public function executeQuery($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    // ADD THIS METHOD: Get user by ID
    public function findById($userId) {
        $sql = "SELECT * FROM users WHERE user_id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}