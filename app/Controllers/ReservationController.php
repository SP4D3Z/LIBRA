<?php
namespace App\Controllers;

use App\Models\ReservationModel;

class ReservationController {
    protected $model;
    public function __construct($pdo){
        $this->model = new ReservationModel($pdo);
    }

    public function listUserReservations() {
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        if(empty($_SESSION['user'])) return [];
        $userId = $_SESSION['user']['id'];
        return $this->model->getByUser($userId);
    }

    public function handleReservation($post) {
        $msg = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($post['reserve'])) {
            $this->model->createReservation($post['user_id'], $post['book_id']);
            $msg = "Reservation created";
        }
        return $msg;
    }

    public function getReservations() {
        return $this->model->getReservations();
    }

    public function getUsers() {
        return $this->model->getUsers();
    }

    public function getBooks() {
        return $this->model->getBooks();
    }
}