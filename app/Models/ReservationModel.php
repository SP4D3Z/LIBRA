<?php
namespace App\Models;

use PDO;

class ReservationModel {
    protected $pdo;
    public function __construct($pdo){$this->pdo = $pdo;}

    public function createReservation($user_id, $book_id) {
        $stmt = $this->pdo->prepare("INSERT INTO reservations (user_id, book_id, reservation_date, expiry_date, status) VALUES (?,?,?,?,?)");
        $res_date = date('Y-m-d');
        $exp = date('Y-m-d', strtotime("+7 days"));
        $stmt->execute([$user_id, $book_id, $res_date, $exp, 'pending']);
    }

    public function getByUser($userId) {
        $sql = "SELECT r.* /*, join book fields if required */
                FROM reservations r
                WHERE r.user_id = :uid
                ORDER BY r.reserved_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservations() {
        $sql = "SELECT r.*, u.first_name, u.last_name, b.title FROM reservations r JOIN users u ON r.user_id=u.user_id JOIN books b ON r.book_id=b.book_id ORDER BY r.reservation_date DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getUsers() {
        return $this->pdo->query("SELECT user_id, first_name, last_name FROM users ORDER BY first_name")->fetchAll();
    }

    public function getBooks() {
        return $this->pdo->query("SELECT book_id, title FROM books WHERE is_archived = 0")->fetchAll();
    }
}