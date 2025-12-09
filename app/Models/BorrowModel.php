<?php
namespace App\Models;

use PDO;

class BorrowModel {
    protected $pdo;
    public function __construct($pdo){
        $this->pdo = $pdo;
    }
    
    public function getAllBorrowings() {
        $sql = "SELECT bt.*, u.first_name, u.last_name, b.title 
                FROM borrowing_transactions bt 
                JOIN users u ON bt.user_id=u.user_id 
                JOIN books b ON bt.book_id=b.book_id 
                ORDER BY bt.borrowed_date DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    // FIXED: Changed table name from 'borrows' to 'borrowing_transactions'
    public function getByUser($userId) {
        $sql = "SELECT bt.*, b.title, b.author 
                FROM borrowing_transactions bt 
                JOIN books b ON bt.book_id = b.book_id 
                WHERE bt.user_id = :uid 
                ORDER BY bt.borrowed_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        return $this->pdo->query("SELECT user_id, first_name, last_name, user_type FROM users ORDER BY first_name")->fetchAll();
    }

    public function getAllBooks() {
        return $this->pdo->query("SELECT book_id, title, available_copies FROM books WHERE is_archived = 0 ORDER BY title")->fetchAll();
    }

    public function getBookAvailability($book_id) {
        $stmt = $this->pdo->prepare("SELECT available_copies FROM books WHERE book_id = ?");
        $stmt->execute([$book_id]);
        return $stmt->fetch();
    }

    public function getUserType($user_id) {
        $stmt = $this->pdo->prepare("SELECT user_type FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getStudentBorrowCount($user_id, $sem_start, $sem_end) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM borrowing_transactions WHERE user_id = ? AND borrowed_date BETWEEN ? AND ? AND status = 'active'");
        $stmt->execute([$user_id, $sem_start, $sem_end]);
        return $stmt->fetchColumn();
    }

    public function getCurrentSemester() {
        $month = date('n'); // Current month (1-12)
        
        if ($month >= 6 && $month <= 10) {
            // First Semester: June 1 - October 31
            return [
                'name' => 'First Semester',
                'start' => date('Y') . '-06-01',
                'end' => date('Y') . '-10-31'
            ];
        } elseif ($month == 11 || $month == 12 || $month <= 3) {
            // Second Semester: November 1 - March 31
            $year = $month <= 3 ? date('Y') - 1 : date('Y');
            return [
                'name' => 'Second Semester', 
                'start' => $year . '-11-01',
                'end' => ($year + 1) . '-03-31'
            ];
        } else {
            // Summer: April 1 - May 31
            return [
                'name' => 'Summer',
                'start' => date('Y') . '-04-01',
                'end' => date('Y') . '-05-31'
            ];
        }
    }

    public function borrowBook($user_id, $book_id, $borrowed, $due, $staff_id) {
        // Use transaction to ensure data consistency
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO borrowing_transactions (user_id, book_id, borrowed_date, due_date, staff_id_borrowed, status) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$user_id, $book_id, $borrowed, $due, $staff_id, 'active']);
            
            $updateStmt = $this->pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?");
            $updateStmt->execute([$book_id]);
            
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function returnBook($transaction_id, $staff_id) {
        $this->pdo->beginTransaction();
        try {
            $updateStmt = $this->pdo->prepare("UPDATE borrowing_transactions SET returned_date = ?, status = 'returned', staff_id_returned = ? WHERE transaction_id = ?");
            $updateStmt->execute([date('Y-m-d'), $staff_id, $transaction_id]);
            
            $bookStmt = $this->pdo->prepare("SELECT book_id FROM borrowing_transactions WHERE transaction_id = ?");
            $bookStmt->execute([$transaction_id]);
            $bookid = $bookStmt->fetchColumn();
            
            if($bookid) {
                $updateBookStmt = $this->pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE book_id = ?");
                $updateBookStmt->execute([$bookid]);
            }
            
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    public function getOverdueBooksCount($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM borrowing_transactions WHERE user_id = ? AND status = 'active' AND due_date < CURDATE()");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getUnpaidPenalties($user_id) {
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM penalties WHERE user_id = ? AND is_paid = 0");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getUserActiveBorrowings($user_id) {
        $sql = "SELECT bt.*, b.title, b.author 
                FROM borrowing_transactions bt 
                JOIN books b ON bt.book_id = b.book_id 
                WHERE bt.user_id = ? AND bt.status = 'active' 
                ORDER BY bt.due_date";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
    // Add these methods to your existing BorrowModel.php class:

public function getAllBorrowersWithStatus() {
    $sql = "SELECT 
                u.user_id, 
                u.username, 
                u.first_name, 
                u.last_name, 
                u.user_type,
                COUNT(DISTINCT CASE WHEN bt.status = 'active' THEN bt.transaction_id END) as active_borrowings,
                COUNT(DISTINCT CASE WHEN bt.status = 'active' AND bt.due_date < CURDATE() THEN bt.transaction_id END) as overdue_books,
                COALESCE(SUM(CASE WHEN p.is_paid = 0 THEN p.amount ELSE 0 END), 0) as unpaid_penalties
            FROM users u
            LEFT JOIN borrowing_transactions bt ON u.user_id = bt.user_id
            LEFT JOIN penalties p ON u.user_id = p.user_id AND p.is_paid = 0
            WHERE u.user_type IN ('student', 'teacher')
            AND u.is_active = 1
            GROUP BY u.user_id, u.username, u.first_name, u.last_name, u.user_type
            ORDER BY u.user_type, u.first_name";
    
    return $this->pdo->query($sql)->fetchAll();
}

public function getBorrowingReport($start_date = null, $end_date = null) {
    $where = "";
    $params = [];
    
    if ($start_date && $end_date) {
        $where = "WHERE bt.borrowed_date BETWEEN ? AND ?";
        $params = [$start_date, $end_date];
    } elseif ($start_date) {
        $where = "WHERE bt.borrowed_date >= ?";
        $params = [$start_date];
    } elseif ($end_date) {
        $where = "WHERE bt.borrowed_date <= ?";
        $params = [$end_date];
    }
    
    $sql = "SELECT 
                DATE(bt.borrowed_date) as borrow_date,
                COUNT(*) as total_borrowed,
                SUM(CASE WHEN bt.status = 'active' THEN 1 ELSE 0 END) as still_borrowed,
                SUM(CASE WHEN bt.status = 'returned' THEN 1 ELSE 0 END) as returned,
                SUM(CASE WHEN bt.status = 'active' AND bt.due_date < CURDATE() THEN 1 ELSE 0 END) as overdue,
                u.user_type,
                b.category_id,
                c.category_name
            FROM borrowing_transactions bt
            JOIN users u ON bt.user_id = u.user_id
            JOIN books b ON bt.book_id = b.book_id
            LEFT JOIN categories c ON b.category_id = c.category_id
            {$where}
            GROUP BY DATE(bt.borrowed_date), u.user_type, b.category_id
            ORDER BY borrow_date DESC";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
}