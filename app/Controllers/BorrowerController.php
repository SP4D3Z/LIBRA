<?php
namespace App\Controllers;

use App\Models\BorrowModel;
use App\Models\UserModel;

class BorrowerController {
    private $pdo;
    private $borrowModel;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->borrowModel = new BorrowModel($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function getBorrowerStatus($user_id = null) {
        if ($user_id) {
            $user = $this->userModel->findById($user_id);
            if (!$user) {
                return null;
            }
            
            return [
                'user_info' => $user,
                'active_borrowings' => $this->borrowModel->getUserActiveBorrowings($user_id),
                'overdue_count' => $this->borrowModel->getOverdueBooksCount($user_id),
                'unpaid_penalties' => $this->borrowModel->getUnpaidPenalties($user_id)
            ];
        }
        
        // Get all students and teachers with their borrowing status
        $sql = "SELECT 
                    u.user_id,
                    u.username,
                    u.first_name,
                    u.last_name,
                    u.user_type,
                    (SELECT COUNT(*) 
                     FROM borrowing_transactions bt 
                     WHERE bt.user_id = u.user_id AND bt.status = 'active') as active_borrowings,
                    (SELECT COUNT(*) 
                     FROM borrowing_transactions bt 
                     WHERE bt.user_id = u.user_id AND bt.status = 'active' AND bt.due_date < CURDATE()) as overdue_books,
                    (SELECT COALESCE(SUM(p.amount), 0)
                     FROM penalties p
                     WHERE p.user_id = u.user_id AND p.is_paid = 0) as unpaid_penalties
                FROM users u
                WHERE u.user_type IN ('student', 'teacher')
                AND u.is_active = 1
                ORDER BY u.user_type, u.first_name, u.last_name";
        
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function getBorrowerDetails($user_id) {
        $sql = "SELECT 
                    u.*,
                    (SELECT COUNT(*) FROM borrowing_transactions WHERE user_id = u.user_id) as total_borrowed,
                    (SELECT COUNT(*) FROM borrowing_transactions WHERE user_id = u.user_id AND status = 'returned') as returned_count,
                    (SELECT COUNT(*) FROM reservations WHERE user_id = u.user_id) as total_reservations
                FROM users u
                WHERE u.user_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return null;
        }
        
        return [
            'user' => $user,
            'current_borrowings' => $this->borrowModel->getUserActiveBorrowings($user_id),
            'borrow_history' => $this->getBorrowHistory($user_id),
            'penalties' => $this->getUserPenalties($user_id)
        ];
    }
    
    private function getBorrowHistory($user_id) {
        $sql = "SELECT 
                    bt.*,
                    b.title,
                    b.author,
                    CASE 
                        WHEN bt.returned_date IS NOT NULL THEN 'Returned'
                        WHEN bt.due_date < CURDATE() THEN 'Overdue'
                        ELSE 'Active'
                    END as status_display
                FROM borrowing_transactions bt
                JOIN books b ON bt.book_id = b.book_id
                WHERE bt.user_id = ?
                ORDER BY bt.borrowed_date DESC
                LIMIT 20";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
    
    private function getUserPenalties($user_id) {
        $sql = "SELECT 
                    p.*,
                    b.title,
                    bt.transaction_id
                FROM penalties p
                LEFT JOIN borrowing_transactions bt ON p.transaction_id = bt.transaction_id
                LEFT JOIN books b ON bt.book_id = b.book_id
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}