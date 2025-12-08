<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function login($username, $password) {
        $user = $this->userModel->findByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }
}