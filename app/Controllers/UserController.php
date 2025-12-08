<?php
namespace App\Controllers;

use App\Models\UserModel;

class UserController {
    private $model;
    public function __construct($pdo) { $this->model = new UserModel($pdo); }

    public function handleCreateUser($post, $canAdd) {
        $msg = '';
        $err = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canAdd) {
            try {
                $this->model->createUser($post);
                $msg = "User created";
            } catch(\Exception $e) {
                $err = $e->getMessage();
            }
        }
        return [$msg, $err];
    }
    public function handleDeleteUser($userId, $currentUser) {
    $msg = '';
    $err = '';
    
    // Check if current user has permission (librarian or staff)
    if (!in_array($currentUser['user_type'], ['librarian', 'staff'])) {
        $err = "You don't have permission to delete users";
        return [$msg, $err];
    }
    
    try {
        // Prevent users from deleting themselves
        if ($userId == $currentUser['user_id']) {
            $err = "You cannot delete your own account";
            return [$msg, $err];
        }
        
        $this->model->softDeleteUser($userId);
        $msg = "User deactivated successfully";
    } catch (\Exception $e) {
        $err = $e->getMessage();
    }
    
    return [$msg, $err];
}

    public function getUsers() {
        return $this->model->getUsers();
    }
}