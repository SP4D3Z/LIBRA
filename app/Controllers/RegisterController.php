<?php
namespace App\Controllers;

use App\Models\UserModel;
use PDOException;

class RegisterController {
    private $model;
    public function __construct($pdo) { 
        $this->model = new UserModel($pdo); 
    }

    public function handleRegister($data) {
        $err = '';
        $msg = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = $data ?? [];

            $payload = [
                'username'   => trim($post['username'] ?? ''),
                'first_name' => trim($post['first_name'] ?? ''),
                'last_name'  => trim($post['last_name'] ?? ''),
                'password'   => $post['password'] ?? '',
                'confirm'    => $post['confirm_password'] ?? '',
                'user_type'  => $post['user_type'] ?? 'student',
                'program'    => $post['program'] ?? null,
                'year_level' => $post['year_level'] ?? null,
                'department' => $post['department'] ?? null,
                'position'   => $post['position'] ?? null,
                'phone'      => $post['phone'] ?? null,
                'address'    => $post['address'] ?? null
            ];

            if ($payload['username'] === '' || $payload['first_name'] === '' || $payload['last_name'] === '') {
                $err = "Please fill in required fields.";
            } elseif ($payload['password'] === '' || $payload['confirm'] === '') {
                $err = "Please provide and confirm a password.";
            } elseif ($payload['password'] !== $payload['confirm']) {
                $err = "Passwords do not match!";
            } elseif (strlen($payload['password']) < 6) {
                $err = "Password must be at least 6 characters long.";
            } else {
                // In the try block of handleRegister method:
try {
    // CHECK: username already exists?
    if (method_exists($this->model, 'findByUsername')) {
        $existing = $this->model->findByUsername($payload['username']);
        if ($existing) {
            $err = "Username already taken. Choose a different username.";
            return [$err, $msg];
        }
    }

    // hash password
    $payload['password'] = password_hash($payload['password'], PASSWORD_DEFAULT);

    // createUser should return the new user id (lastInsertId)
    $newId = $this->model->createUser($payload);
    if (!$newId) throw new \Exception('Failed to create user.');

    $msg = "Account created successfully! You may now login.";
} catch (PDOException $e) {
                    $err = $e->getMessage();
                }
            }
        }

        return [$err, $msg];
    }
}