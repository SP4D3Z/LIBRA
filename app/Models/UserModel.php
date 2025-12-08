<?php
namespace App\Models;

use PDO;

class UserModel {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    // Add this helper to check existing username
    public function findByUsername(string $username) {
        $sql = "SELECT * FROM users WHERE username = :u LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Example createUser - ensure this matches your schema and column names
    public function createUser(array $data) {
        $sql = "INSERT INTO users (username, first_name, last_name, phone, address, password, user_type)
                VALUES (:username, :first_name, :last_name, :phone, :address, :password, :user_type)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':username'   => $data['username'],
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':phone'      => $data['phone'],
            ':address'    => $data['address'],
            ':password'   => $data['password'],
            ':user_type'  => $data['user_type']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function getUsers() {
        return $this->pdo->query("SELECT user_id,username,first_name,last_name,user_type,is_active,created_at FROM users WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
    }

    public function createRole($userType, $userId, $data) {
        $stmt = $this->pdo->prepare("INSERT INTO roles (user_id, user_type, program, year_level, department, position) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $userType,
            $data['program'] ?? null,
            $data['year_level'] ?? null,
            $data['department'] ?? null,
            $data['position'] ?? null
        ]);
    }

    // ADD THIS NEW METHOD FOR SOFT DELETE
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
}