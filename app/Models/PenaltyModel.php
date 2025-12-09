<?php
namespace App\Models;

use PDO;

class PenaltyModel {
    protected $pdo;
    public function __construct($pdo){$this->pdo = $pdo;}

    public function markPaid($penalty_id, $staff_id) {
        $stmt = $this->pdo->prepare("UPDATE penalties SET is_paid = 1, payment_date = ?, staff_id_processed = ? WHERE penalty_id = ?");
        $stmt->execute([date('Y-m-d'), $staff_id, $penalty_id]);
    }

    public function getPenalties() {
        $sql = "SELECT p.*, u.first_name, u.last_name FROM penalties p JOIN users u ON p.user_id=u.user_id ORDER BY p.created_at DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function calculateOverduePenalties() {
        // Find overdue books that haven't been penalized yet
        $sql = "SELECT bt.*, b.title, b.price 
                FROM borrowing_transactions bt 
                JOIN books b ON bt.book_id = b.book_id 
                LEFT JOIN penalties p ON bt.transaction_id = p.transaction_id 
                WHERE bt.status = 'active' 
                AND bt.due_date < CURDATE() 
                AND p.penalty_id IS NULL";
        
        $overdue_books = $this->pdo->query($sql)->fetchAll();
        
        foreach($overdue_books as $book) {
            $days_late = floor((time() - strtotime($book['due_date'])) / (60 * 60 * 24));
            $penalty_amount = $days_late * 10.00; // ₱10 per day
            
            // Create penalty record
            $this->createPenalty($book['user_id'], $book['transaction_id'], 'overdue', $penalty_amount);
        }
    }

    public function createPenalty($user_id, $transaction_id, $type, $amount) {
        $stmt = $this->pdo->prepare("INSERT INTO penalties (user_id, transaction_id, penalty_type, amount, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $transaction_id, $type, $amount, date('Y-m-d H:i:s')]);
    }

    public function getUserPenalties($user_id) {
        $sql = "SELECT p.*, bt.transaction_id, b.title 
                FROM penalties p 
                LEFT JOIN borrowing_transactions bt ON p.transaction_id = bt.transaction_id 
                LEFT JOIN books b ON bt.book_id = b.book_id 
                WHERE p.user_id = ? 
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
    
    // FIXED: Changed 'assigned_at' to 'created_at'
    public function getByUser($userId) {
        $sql = "SELECT p.*, b.title 
                FROM penalties p
                LEFT JOIN borrowing_transactions bt ON p.transaction_id = bt.transaction_id
                LEFT JOIN books b ON bt.book_id = b.book_id
                WHERE p.user_id = :uid
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function createLostBookPenalty($user_id, $book_id, $book_price) {
    $stmt = $this->pdo->prepare("INSERT INTO penalties (user_id, book_id, penalty_type, amount, description, created_at) VALUES (?, ?, 'lost', ?, 'Lost book penalty', ?)");
    $stmt->execute([$user_id, $book_id, $book_price, date('Y-m-d H:i:s')]);
}

public function createDamagePenalty($user_id, $book_id, $damage_amount, $description) {
    $stmt = $this->pdo->prepare("INSERT INTO penalties (user_id, book_id, penalty_type, amount, description, created_at) VALUES (?, ?, 'damage', ?, ?, ?)");
    $stmt->execute([$user_id, $book_id, $damage_amount, $description, date('Y-m-d H:i:s')]);
}

public function calculateBookReplacementCost($book_id) {
    $stmt = $this->pdo->prepare("SELECT price FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    return $book ? $book['price'] : 500.00; // Default ₱500 if price not set
}
}