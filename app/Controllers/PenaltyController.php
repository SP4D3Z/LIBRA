<?php
namespace App\Controllers;

use App\Models\PenaltyModel;

class PenaltyController {
    protected $model;
    public function __construct($pdo){
        $this->model = new PenaltyModel($pdo);
    }

    public function listUserPenalties() {
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        if(empty($_SESSION['user'])) return [];
        $userId = $_SESSION['user']['id'];
        return $this->model->getByUser($userId);
    }
    public function handleMarkPaid($get, $currentUser) {
        if(isset($get['pay']) && is_numeric($get['pay'])) {
            $this->model->markPaid((int)$get['pay'], $currentUser['user_id']);
            header('Location: penalties.php');
            exit;
        }
    }

    public function getPenalties() {
        return $this->model->getPenalties();
    }
}